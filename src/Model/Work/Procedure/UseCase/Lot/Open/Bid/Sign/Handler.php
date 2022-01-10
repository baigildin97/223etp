<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Sign;


use App\Helpers\FormatMoney;
use App\Model\Admin\Entity\Settings\Key;
use App\Model\Flusher;
use App\Model\User\Entity\Profile\Payment\PaymentRepository;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\Work\Procedure\Entity\Lot\Bid\BidRepository;
use App\Model\Work\Procedure\Entity\Lot\Bid\Id;
use App\Model\Work\Procedure\Entity\Lot\Bid\XmlDocument\Category;
use App\Model\Work\Procedure\Entity\Lot\Bid\XmlDocument\Status;
use App\Model\Work\Procedure\Services\Bid\Payment\TariffService;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\ReadModel\Procedure\Bid\BidFetcher;
use App\Services\Tasks\Notification;
use Money\Money;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class Handler
 * @package App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Sign
 */
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
     * @var BidFetcher
     */
    private $bidFetcher;

    /**
     * @var PaymentRepository
     */
    private $paymentRepository;

    /**
     * @var TariffService
     */
    private $tariffService;

    /**
     * @var FormatMoney
     */
    private $formatMoney;

    /**
     * @var MoneyFormatter
     */
    private $moneyFormatter;

    /**
     * @var Notification
     */
    private $notificationService;

    /**
     * @var SettingsFetcher
     */
    private $settingsFetcher;

    private $urlGenerator;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param BidRepository $bidRepository
     * @param BidFetcher $bidFetcher
     * @param PaymentRepository $paymentRepository
     * @param TariffService $tariffService
     * @param FormatMoney $formatMoney
     * @param MoneyFormatter $moneyFormatter
     * @param Notification $notificationService
     * @param SettingsFetcher $settingsFetcher
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(Flusher $flusher,
                                BidRepository $bidRepository,
                                BidFetcher $bidFetcher,
                                PaymentRepository $paymentRepository,
                                TariffService $tariffService,
                                FormatMoney $formatMoney,
                                MoneyFormatter $moneyFormatter,
                                Notification $notificationService,
                                SettingsFetcher $settingsFetcher,
                                UrlGeneratorInterface $urlGenerator
    )
    {
        $this->flusher = $flusher;
        $this->bidRepository = $bidRepository;
        $this->bidFetcher = $bidFetcher;
        $this->paymentRepository = $paymentRepository;
        $this->tariffService = $tariffService;
        $this->formatMoney = $formatMoney;
        $this->moneyFormatter = $moneyFormatter;
        $this->notificationService = $notificationService;
        $this->settingsFetcher = $settingsFetcher;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void
    {
        $bid = $this->bidRepository->get(new Id($command->bidId));

        if ($this->bidFetcher->existsActiveBidForLot($bid->getParticipant()->getId()->getValue(), $bid->getLot()->getId()->getValue())) {
            throw new \DomainException('Вы уже подали заявку на этот лот.');
        }

        $bid->sign(
            \App\Model\Work\Procedure\Entity\Lot\Bid\XmlDocument\Id::next(),
            Status::signed(),
            $command->xml,
            $command->hash,
            $command->sign,
            Category::sent(),
            new \DateTimeImmutable()
        );

        $baseUrl = $this->urlGenerator->getContext()->getScheme() . '://' .
            $this->urlGenerator->getContext()->getHost();
        $showProcedureUrl = $baseUrl . $this->urlGenerator->generate('procedure.show',
                ['procedureId' => $bid->getLot()->getProcedure()->getId()]
            );
        $showBidUrl = $baseUrl . $this->urlGenerator->generate('bid.show',
                ['bidId' => $bid->getId()]
            );

        $level = $bid->getSubscribeTariff()->getTariff()->getLevel($bid->getLot()->getStartingPrice());


        $payment = $bid->getParticipant()->getPayment();

        if ($this->settingsFetcher->findDetailByKey(Key::participationWithNegativeBalance()) === Key::participationWithNegativeBalance()->getValue()) {
            $payment->blockingMoneyBid($level->getCharged(), $bid);
        } else {
            if (!$payment->getAvailableAmount()->greaterThanOrEqual($level->getCharged())){
                throw new \DomainException('Недостаточно средств гарантийного обеспечения на балансе Виртуального счета. Необходимая сумма: '.$this->formatMoney->convertMoneyToString($level->getCharged()));
            }

            $payment->blockingMoneyBidNegativeBalance($level->getCharged(), $bid);
        }

        $this->flusher->flush();

        $message = Message::createBid(
            $bid->getParticipant()->getUser()->getEmail(),
            $bid->getLot()->getFullNumber(),
            $showProcedureUrl,
            $bid->getNumber(),
            $showBidUrl
        );

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($bid->getParticipant()->getUser(), $message);

        $message = Message::blockedFunds(
            $bid->getParticipant()->getUser()->getEmail(),
            $bid->getLot()->getFullNumber(),
            $showProcedureUrl,
            $bid->getNumber(),
            $showBidUrl,
            $level->getPercent(),
            $this->formatMoney->convertMoneyToString($level->getCharged()),
            $bid->getLot()->getNds()->getLocalizedName()
        );

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($bid->getParticipant()->getUser(), $message);

        $message = Message::newBidOrganizer(
            $bid->getLot()->getProcedure()->getOrganizer()->getUser()->getEmail(),
            $bid->getLot()->getFullNumber(),
            $showProcedureUrl,
            $bid->getNumber(),
            $showBidUrl
        );

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($bid->getLot()->getProcedure()->getOrganizer()->getUser(), $message);
    }

}
