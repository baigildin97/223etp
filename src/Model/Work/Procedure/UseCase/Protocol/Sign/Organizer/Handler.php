<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Protocol\Sign\Organizer;


use App\Helpers\FormatMoney;
use App\Model\Flusher;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\Lot\LotRepository;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use App\Model\Work\Procedure\Entity\Protocol\IdNumber;
use App\Model\Work\Procedure\Entity\Protocol\Reason;
use App\Model\Work\Procedure\Entity\Protocol\Status;
use App\Model\Work\Procedure\Entity\Protocol\Type;
use App\Model\Work\Procedure\Entity\Protocol\XmlDocument;
use App\Services\Tasks\Notification;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Handler
 * @package App\Model\Work\Procedure\UseCase\Protocol\Sign\Organizer
 */
class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var ProcedureRepository
     */
    private $procedureRepository;

    /**
     * @var LotRepository
     */
    private $lotRepository;

    /**
     * @var Notification
     */
    private $notificationService;

    /**
     * @var FormatMoney
     */
    private $formatMoney;

    private $urlGenerator;


    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param ProcedureRepository $procedureRepository
     * @param LotRepository $lotRepository
     * @param Notification $notificationService
     * @param FormatMoney $formatMoney
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(Flusher $flusher,
                                ProcedureRepository $procedureRepository,
                                LotRepository $lotRepository,
                                Notification $notificationService,
                                FormatMoney $formatMoney,
                                UrlGeneratorInterface $urlGenerator
    )
    {
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
        $this->lotRepository = $lotRepository;
        $this->notificationService = $notificationService;
        $this->formatMoney = $formatMoney;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     *  Подписание организатором, генерация протокола
     * @param Command $command
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function handle(Command $command): void
    {
        $procedure = $this->procedureRepository->get(new Id($command->procedureId));

        foreach ($procedure->getLots() as $lot){
            foreach ($lot->getBids() as $bid){
                if ($bid->getStatus()->isSent()){
                    throw new \DomainException('Не все заявки рассмотрены.');
                }
            }
        }

        if ($command->userId !== $procedure->getOrganizer()->getUser()->getId()->getValue()) {
            throw new \DomainException("You are not the organizer of this procedure");
        }

        $thumbprint = $procedure->getOrganizer()->getCertificate()->getThumbprint();
        $reason = new Reason($command->reason);

        $procedure->addProtocol(
            \App\Model\Work\Procedure\Entity\Protocol\Id::next(),
            IdNumber::next(),
            $type = new Type($command->typeProtocol),
            Status::signed(),
            XmlDocument::signedOrganizer(
                $command->xmlFile,
                $command->hash,
                $command->sign,
                $thumbprint,
                new \DateTimeImmutable(),
            ),
            new \DateTimeImmutable(),
            $reason,
            $reason->getLocalizedName()
        );

        $lot = $this->lotRepository->get(new \App\Model\Work\Procedure\Entity\Lot\Id($command->lotId));

        if ($type->isSummarizingResultsReceivingBids()) {
            if (!$procedure->getStatus()->isSummingApplications()) {
                throw new \DomainException('Время подведения итогов приема заявок: ' . $lot->getSummingUpApplications()->format("d.m.Y H:i:s"));
            }
        }

        if ($reason->isLessTwoBids()
            || $reason->isApproveLessTwoBids()
            || $reason->isZeroOffers()
            || $reason->isConfirmedLessTwoBids()) {
            $procedure->failed();

            //Unblocking money Participants
            foreach ($procedure->getLots() as $lot) {
                foreach ($lot->getBids() as $bid) {
                    $getTransactions = $bid->getParticipant()->getPayment()->getTransactions();
                    $criteriaWhere = new Criteria();
                    $expr = new Comparison('bid', Comparison::EQ, $bid->getId());
                    $criteriaWhere->where($expr);
                    $criteriaWhere->andWhere(new Comparison('type', '=', \App\Model\User\Entity\Profile\Payment\Transaction\TransactionType::blocking()->getValue()));
                    $level = $bid->getSubscribeTariff()->getTariff()->getLevel($bid->getLot()->getStartingPrice());
                    $payment = $bid->getParticipant()->getPayment();
                    //Unblocking amount
                    if ($getTransactions->matching($criteriaWhere)->count() > 0) {
                        //Разблокировка замороженных средств
                        $payment->unBlocking($level->getCharged());
                    }
                }
            }
        }


        if ($reason->isApproveMoreTwoBids()) {
            $procedure->tradingWait();
        }


        if ($type->isCancellationProtocolResult()) {
            $procedure->stopOfTrading();
            $lot->paymentWinnerAnnulled();
        }


        if ($type->isResultProtocol()) {

            foreach ($procedure->getLots() as $lot) {
                foreach ($lot->getBids() as $bid) {
                    if ($bid->getStatus()->isReject() || $bid->getStatus()->isApproved()) {
                        $getTransactions = $bid->getParticipant()->getPayment()->getTransactions();

                        $criteriaWhere = new Criteria();
                        $expr = new Comparison('bid', Comparison::EQ, $bid->getId());
                        $criteriaWhere->where($expr);
                        $criteriaWhere->andWhere(new Comparison('type', '=', \App\Model\User\Entity\Profile\Payment\Transaction\TransactionType::blocking()->getValue()));

                        $level = $bid->getSubscribeTariff()->getTariff()->getLevel($bid->getLot()->getStartingPrice());

                        $payment = $bid->getParticipant()->getPayment();

                        //Если не победитель
                        if ($bid->getParticipant()->getUser()->getId() !== $bid->getLot()->getAuction()->getWinner()->getUser()->getId()) {
                            //Unblocking amount
                            if ($getTransactions->matching($criteriaWhere)->count() > 0) {
                                //Разблокировка замороженных средств
                                $payment->unBlocking($level->getCharged());
                            }


                        }

                        //Если победитель
                        if ($bid->getParticipant()->getUser()->getId() === $bid->getLot()->getAuction()->getWinner()->getUser()->getId()) {
                            //Winner
                            if ($getTransactions->matching($criteriaWhere)->count() > 0) {
                                $payment->subtractBlockingMoney($level->getCharged(), $bid);
                                //Если победитель, и средства до этого не были заморожены, попытаемся снять со счета
                                //    if ($payment->getAvailableAmount()->greaterThanOrEqual($level->getCharged())) {
                                /// $payment->blockingMoneyBid($level->getCharged(), $bid);
                                //    }
                            }

                        }


                    }
                }

            }
        }

        $this->flusher->flush();

        $baseUrl = $this->urlGenerator->getContext()->getScheme() . '://' .
            $this->urlGenerator->getContext()->getHost();

        foreach ($procedure->getLots() as $lot) {
            foreach ($lot->getBids() as $bid) {
                $showProcedureUrl = $baseUrl . $this->urlGenerator->generate('procedure.show',
                        ['procedureId' => $bid->getLot()->getProcedure()->getId()]);
                $user = $bid->getParticipant()->getUser();

                if ($bid->getStatus()->isReject()) {
                    if ($type->isSummarizingResultsReceivingBids()) {
                        $message = Message::participantPublishedProtocolSummingRegProcedure(
                            $user->getEmail(),
                            $bid->getLot()->getFullNumber(),
                            $showProcedureUrl
                        );

                        $this->notificationService->emailToOneUser($message);
                        $this->notificationService->sendToOneUser($user, $message);
                    }
                }

                if ($bid->getStatus()->isReject() || $bid->getStatus()->isApproved()) {
                    if ($type->isResultProtocol()) {
                        $level = $bid->getSubscribeTariff()->getTariff()->getLevel($bid->getLot()->getStartingPrice());

                        //Notification winner
                        if ($user->getId() === $bid->getLot()->getAuction()->getWinner()->getUser()->getId()) {

                            $message = Message::payOperatorServices(
                                $bid->getParticipant()->getUser()->getEmail(),
                                $bid->getLot()->getFullNumber(),
                                $this->formatMoney->convertMoneyToString($level->getCharged()),
                                $showProcedureUrl
                            );

                            $this->notificationService->emailToOneUser($message);
                            $this->notificationService->sendToOneUser($user, $message);

                            $message = Message::publishedProtocolWinner(
                                $user->getEmail(),
                                $bid->getLot()->getFullNumber(),
                                $showProcedureUrl
                            );

                            $this->notificationService->emailToOneUser($message);
                            $this->notificationService->sendToOneUser($user, $message);

                        } else {
                            //Не победитель

                            $getTransactions = $bid->getParticipant()->getPayment()->getTransactions();

                            $criteriaWhere = new Criteria();
                            $expr = new Comparison('bid', Comparison::EQ, $bid->getId());
                            $criteriaWhere->where($expr);
                            $criteriaWhere->andWhere(new Comparison('type', '=', \App\Model\User\Entity\Profile\Payment\Transaction\TransactionType::blocking()->getValue()));


                            if ($getTransactions->matching($criteriaWhere)->count() > 0) {
                                //Разблокировка замороженных средств
                                $message = Message::blockedFundsUnblocked(
                                    $user->getEmail(),
                                    $bid->getLot()->getFullNumber(),
                                    $showProcedureUrl,
                                    $bid->getNumber(),
                                    $baseUrl . $this->urlGenerator->generate('bid.show', ['bidId' => $bid->getId()]),
                                    $level->getPercent(),
                                    $this->formatMoney->convertMoneyToString($level->getCharged()),
                                    $bid->getLot()->getNds()->getLocalizedName()
                                );

                                $this->notificationService->emailToOneUser($message);
                                $this->notificationService->sendToOneUser($user, $message);
                            }

                            //Participant winner
                            $message = Message::organizerPublishedProtocolResults(
                                $user->getEmail(),
                                $bid->getLot()->getFullNumber(),
                                $showProcedureUrl
                            );

                            $this->notificationService->emailToOneUser($message);
                            $this->notificationService->sendToOneUser($user, $message);
                        }

                    } else if ($type->isWinnerProtocol()) {

                        $message = Message::participantPublishedProtocolWinner(
                            $user->getEmail(),
                            $bid->getLot()->getFullNumber(),
                            $showProcedureUrl
                        );

                        $this->notificationService->emailToOneUser($message);
                        $this->notificationService->sendToOneUser($user, $message);


                        if ($bid->getStatus()->isApproved() && $bid->getPlace() !== null) {
                            $message = Message::setPlaceParticipant(
                                $user->getEmail(),
                                $bid->getPlace(),
                                $bid->getLot()->getFullNumber(),
                                $showProcedureUrl
                            );

                            $this->notificationService->emailToOneUser($message);
                            $this->notificationService->sendToOneUser($user, $message);
                        }

                    }
                }
            }
        }


        if ($reason->isLessTwoBids()
            || $reason->isApproveLessTwoBids()
            || $reason->isZeroOffers()
            || $reason->isConfirmedLessTwoBids()) {
            foreach ($procedure->getLots() as $lot) {
                foreach ($lot->getBids() as $bid) {
                    $user = $bid->getParticipant()->getUser();
                    if ($bid->getStatus()->isReject() || $bid->getStatus()->isApproved()) {
                        $level = $bid->getSubscribeTariff()->getTariff()->getLevel($bid->getLot()->getStartingPrice());
                        //Не победитель
                        $getTransactions = $bid->getParticipant()->getPayment()->getTransactions();

                        $criteriaWhere = new Criteria();
                        $expr = new Comparison('bid', Comparison::EQ, $bid->getId());
                        $criteriaWhere->where($expr);
                        $criteriaWhere->andWhere(new Comparison('type', '=', \App\Model\User\Entity\Profile\Payment\Transaction\TransactionType::blocking()->getValue()));

                        if ($getTransactions->matching($criteriaWhere)->count() > 0) {
                            //Разблокировка замороженных средств
                            $message = Message::blockedFundsUnblocked(
                                $user->getEmail(),
                                $bid->getLot()->getFullNumber(),
                                $showProcedureUrl,
                                $bid->getNumber(),
                                $baseUrl . $this->urlGenerator->generate('bid.show', ['bidId' => $bid->getId()]),
                                $level->getPercent(),
                                $this->formatMoney->convertMoneyToString($level->getCharged()),
                                $bid->getLot()->getNds()->getLocalizedName()
                            );

                            $this->notificationService->emailToOneUser($message);
                            $this->notificationService->sendToOneUser($user, $message);
                        }
                    }
                }
            }
        }


        $lot = $this->lotRepository->get(new \App\Model\Work\Procedure\Entity\Lot\Id($command->lotId));
        $user = $procedure->getOrganizer()->getUser();
        $showProcedureUrl = $baseUrl . $this->urlGenerator->generate('procedure.show', ['procedureId' => $procedure->getId()]);

        if ($type->isSummarizingResultsReceivingBids()) {
            $message = Message::organizerPublishedProtocolSummingRegProcedure(
                $user->getEmail(),
                $lot->getFullNumber(),
                $showProcedureUrl
            );

            $this->notificationService->emailToOneUser($message);
            $this->notificationService->sendToOneUser($user, $message);
        }

        if ($type->isWinnerProtocol()) {
            $message = Message::organizerPublishedProtocolWinner(
                $user->getEmail(),
                $lot->getFullNumber(),
                $showProcedureUrl
            );

            $this->notificationService->emailToOneUser($message);
            $this->notificationService->sendToOneUser($user, $message);
        }

        if ($type->isResultProtocol()) {
            $message = Message::organizerPublishedProtocol(
                $user->getEmail(),
                $lot->getFullNumber(),
                $showProcedureUrl
            );

            $this->notificationService->emailToOneUser($message);
            $this->notificationService->sendToOneUser($user, $message);
        }
    }

}