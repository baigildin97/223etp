<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Recall;


use App\Helpers\FormatMoney;
use App\Model\Flusher;

use App\Model\Work\Procedure\Entity\Lot\Bid\BidRepository;
use App\Model\Work\Procedure\Entity\Lot\Bid\Id;
use App\Model\Work\Procedure\Entity\Lot\Bid\XmlDocument\Category;
use App\Model\Work\Procedure\Entity\Lot\Bid\XmlDocument\Status;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\Services\Tasks\Notification;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var BidRepository
     */
    private $bidRepository;

    /**
     * @var Notification
     */
    private $notificationService;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    private $formatMoney;

    /**
     * @var ProcedureFetcher
     */
    private $procedureFetcher;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param BidRepository $bidRepository
     * @param Notification $notificationService
     * @param UrlGeneratorInterface $urlGenerator
     * @param FormatMoney $formatMoney
     * @param ProcedureFetcher $procedureFetcher
     */
    public function __construct(
        Flusher $flusher,
        BidRepository $bidRepository,
        Notification $notificationService,
        UrlGeneratorInterface $urlGenerator,
        FormatMoney $formatMoney,
        ProcedureFetcher $procedureFetcher
    ){
        $this->flusher = $flusher;
        $this->bidRepository = $bidRepository;
        $this->notificationService = $notificationService;
        $this->urlGenerator = $urlGenerator;
        $this->formatMoney = $formatMoney;
        $this->procedureFetcher = $procedureFetcher;
    }

    /**
     * @param Command $command
     *
     * Заявку можно отозвать до начала торгов.
     * Отклоненную заявку отзывать нельзя
     * Допущенную заявку можно.
     */
    public function handle(Command $command): void
    {
        $bid = $this->bidRepository->get(new Id($command->bidId));

        if (!$this->validate($bid->getLot()->getProcedure()->getId()->getValue())){
            throw new \DomainException('Запрос на отзыв заявки отклонен.');
        }

        $bid->recall(
            \App\Model\Work\Procedure\Entity\Lot\Bid\XmlDocument\Id::next(),
            Status::signed(),
            $command->xml,
            $command->hash,
            $command->sign,
            Category::recall(),
            new \DateTimeImmutable()
        );

        $level = $bid->getSubscribeTariff()->getTariff()->getLevel($bid->getLot()->getStartingPrice());

        $payment = $bid->getParticipant()->getPayment();
        $payment->unBlocking($level->getCharged());

        $this->flusher->flush();

        $baseUrl = $this->urlGenerator->getContext()->getScheme() . '://' .
            $this->urlGenerator->getContext()->getHost();
        $showBidUrl = $baseUrl . $this->urlGenerator->generate('bid.show', ['bidId' => $bid->getId()->getValue()]);
        $showProcedureUrl = $baseUrl . $this->urlGenerator->generate('procedure.show',
                ['procedureId' => $bid->getLot()->getProcedure()->getId()]);

        $user = $bid->getParticipant()->getUser();
        $message = Message::BidRecall($user->getEmail(), $showBidUrl,
            $bid->getNumber(),
            $bid->getLot()->getProcedure()->getIdNumber(), $showProcedureUrl);

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);

        $message = Message::unBlockedFunds(
            $user->getEmail(),
            $bid->getLot()->getFullNumber(),
            $showProcedureUrl,
            $bid->getNumber(),
            $showBidUrl,
            $level->getPercent(),
            $this->formatMoney->convertMoneyToString($level->getCharged()),
            $bid->getLot()->getNds()->getLocalizedName()
        );

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);

        $user = $bid->getLot()->getProcedure()->getOrganizer()->getUser();
        $message = Message::BidRecallOrganizer(
            $user->getEmail(),
            $bid->getNumber(),
            $showBidUrl,
            $bid->getLot()->getProcedure()->getIdNumber(),
            $showProcedureUrl
        );

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);
    }

    /**
     * @param string $procedureId
     * @return bool
     * Проверяем статус торгов не начались ли торги. Если да то закрываем доступ
     * Проверяем статус заявки. Если не ОТПРАВЛЕН или не Допущен. То закрываем доступ.
     * Проверяем этап во время которого отзывается. Если после этапа, окончания подачи заявок происходит отзыв то
     */
    private function validate(string $procedureId): bool {
        $procedure = $this->procedureFetcher->findDetail($procedureId);
        dd($procedureId);
        if ($procedure->start_trading_time < new \DateTimeImmutable()){
            return false;
        }
    }
}