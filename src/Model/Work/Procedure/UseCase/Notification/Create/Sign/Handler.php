<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Notification\Create\Sign;


use App\Helpers\FormatMoney;
use App\Model\Flusher;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\Entity\User\UserRepository;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use App\Model\Work\Procedure\Entity\XmlDocument\IdNumber;
use App\Model\Work\Procedure\Entity\XmlDocument\Status;
use App\Model\Work\Procedure\Entity\XmlDocument\StatusTactic;
use App\Model\Work\Procedure\Entity\XmlDocument\Type;
use App\Model\Work\Procedure\Entity\XmlDocument\XmlDocument;
use App\Model\Work\Procedure\Entity\XmlDocument\XmlDocumentRepository;
use App\Model\Work\Procedure\Services\Procedure\XmlDocument\NumberGenerator;
use App\Model\Work\Procedure\UseCase\Protocol\Sign\Organizer\Message as MessageOrg;
use App\Services\Tasks\Notification;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Handler
{
    private $flusher;

    private $procedureRepository;

    private $xmlDocumentRepository;

    private $notificationService;

    private $userRepository;

    private $urlGenerator;

    private $numberGenerator;

    private $formatMoney;

    public function __construct(
        Flusher $flusher,
        ProcedureRepository $procedureRepository,
        XmlDocumentRepository $xmlDocumentRepository,
        Notification $notificationService,
        UserRepository $userRepository,
        UrlGeneratorInterface $urlGenerator,
        NumberGenerator $numberGenerator,
        FormatMoney $formatMoney
    ){
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
        $this->xmlDocumentRepository = $xmlDocumentRepository;
        $this->notificationService = $notificationService;
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
        $this->numberGenerator = $numberGenerator;
        $this->formatMoney = $formatMoney;
    }

    public function handle(Command $command): void
    {
        $procedure = $this->procedureRepository->get(new Id($command->procedureId));

        $notification = new XmlDocument(
            \App\Model\Work\Procedure\Entity\XmlDocument\Id::next(),
            $number = $this->numberGenerator->next(),
            Status::signed(),
            $type = new Type($command->notificationType),
            $command->xml,
            $command->hash,
            $procedure,
            new \DateTimeImmutable(),
            StatusTactic::published()
        );

        $notification->addSign($command->sign);

        if ($type->isNotifyCancel()) {
            $procedure->cancel();
        }

        if ($type->isNotifyPause()) {
            $procedure->pause();
        }

        if ($type->isNotifyResume()) {
            $procedure->resume();
        }

        $this->xmlDocumentRepository->add($notification);
        $this->flusher->flush();

        $showProcedureUrl = $this->urlGenerator->getContext()->getScheme() . '://' .
            $this->urlGenerator->getContext()->getHost() . $this->urlGenerator->generate('procedure.show',
                ['procedureId' => $procedure->getId()->getValue()]);


        $baseUrl = $this->urlGenerator->getContext()->getScheme() . '://' .
            $this->urlGenerator->getContext()->getHost();

        if ($type->isNotifyCancel()) {

            $participants = [];
            foreach ($procedure->getLots() as $lot){
                foreach ($lot->getBids() as $bid){
                    if ($bid->getStatus()->isReject() || $bid->getStatus()->isApproved() || $bid->getStatus()->isSent()) {

                        $participants[] = $bid->getParticipant()->getUser();

                        $getTransactions = $bid->getParticipant()->getPayment()->getTransactions();
                        $criteriaWhere = new Criteria();
                        $expr = new Comparison('bid', Comparison::EQ, $bid->getId());
                        $criteriaWhere->where($expr);
                        $criteriaWhere->andWhere(new Comparison('type', '=', \App\Model\User\Entity\Profile\Payment\Transaction\TransactionType::blocking()->getValue()));
                        $level = $bid->getSubscribeTariff()->getTariff()->getLevel($bid->getLot()->getStartingPrice());
                        $payment = $bid->getParticipant()->getPayment();


                            if ($getTransactions->matching($criteriaWhere)->count() > 0) {
                                //Разблокировка замороженных средств
                                $payment->unBlocking($level->getCharged());

                                $message = MessageOrg::blockedFundsUnblocked(
                                    $bid->getParticipant()->getUser()->getEmail(),
                                    $bid->getLot()->getFullNumber(),
                                    $showProcedureUrl,
                                    $bid->getNumber(),
                                    $baseUrl . $this->urlGenerator->generate('bid.show', ['bidId' => $bid->getId()]),
                                    $level->getPercent(),
                                    $this->formatMoney->convertMoneyToString($level->getCharged()),
                                    $bid->getLot()->getNds()->getLocalizedName()
                                );

                                $this->notificationService->emailToOneUser($message);
                                $this->notificationService->sendToOneUser($bid->getParticipant()->getUser(), $message);

                            }



                    }



                }
            }


         //   $usersAdmins = $this->userRepository->getAllAdminsAndModerators();

            $message = Message::procedureRejectOrganizer(
                $procedure->getOrganizer()->getUser()->getEmail(),
                $procedure->getIdNumber(),
                $showProcedureUrl,
                $number
            );

            // Организатору
            $this->notificationService->emailToOneUser($message);
            $this->notificationService->sendToOneUser($procedure->getOrganizer()->getUser(), $message);

            // Участникам
            $this->notificationService->emailToMultipleUsers($participants, $message);
            $this->notificationService->sendToMultipleUsers($participants, $message);


        }
    }

}