<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Payment\Transaction\Withdraw\Sign;


use App\Helpers\FormatMoney;
use App\Model\Flusher;
use App\Model\User\Entity\Profile\Payment\Id;
use App\Model\User\Entity\Profile\Payment\PaymentRepository;
use App\Model\User\Entity\Profile\Payment\Transaction\IdNumber;
use App\Model\User\Entity\Profile\Requisite\RequisiteRepository;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Service\Profile\Payment\Transaction\NumberGenerator;
use App\Services\Tasks\Notification;
use Doctrine\DBAL\Exception;
use Money\Currency;
use Money\Money;

/**
 * Class Handler
 * @package App\Model\User\UseCase\Profile\Payment\Transaction\Withdraw\Sign
 */
class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var PaymentRepository
     */
    private $paymentRepository;

    /**
     * @var RequisiteRepository
     */
    private $requisiteRepository;

    /**
     * @var Notification
     */
    private $notificationService;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var FormatMoney
     */
    private $formatMoney;

    /**
     * @var NumberGenerator
     */
    private $numberGenerator;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param PaymentRepository $paymentRepository
     * @param RequisiteRepository $requisiteRepository
     * @param Notification $notificationService
     * @param UserRepository $userRepository
     * @param FormatMoney $formatMoney
     * @param NumberGenerator $numberGenerator
     */
    public function __construct(Flusher $flusher,
                                PaymentRepository $paymentRepository,
                                RequisiteRepository $requisiteRepository,
                                Notification $notificationService,
                                UserRepository $userRepository,
                                FormatMoney $formatMoney,
                                NumberGenerator $numberGenerator
    )
    {
        $this->flusher = $flusher;
        $this->paymentRepository = $paymentRepository;
        $this->requisiteRepository = $requisiteRepository;
        $this->notificationService = $notificationService;
        $this->userRepository = $userRepository;
        $this->formatMoney = $formatMoney;
        $this->numberGenerator = $numberGenerator;
    }

    /**
     * @param Command $command
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        $payment = $this->paymentRepository->get(new Id($command->paymentId));

        $requisite = $this->requisiteRepository->get(new \App\Model\User\Entity\Profile\Requisite\Id($command->requisiteId));

        $numberGenerator = $this->numberGenerator->next();

        $payment->withdraw(
            $money = new Money($command->money, new Currency('RUB')),
            $idTransaction = $numberGenerator,
            $requisite,
            $command->xmlDocument,
            $command->hash,
            $command->sign,
            $createdAt = new \DateTimeImmutable()
        );
        $this->flusher->flush();

        $user = $payment->getProfile()->getUser();
        $usersAdmins = $this->userRepository->getAllAdminsAndModerators();

        $message = Message::profileWithdrawModerator($user->getEmail(),
            $payment->getProfile()->getRepresentative()->getPassport()->getFullName());

        //$this->notificationService->emailToMultipleUsers($usersAdmins, $message);
        $this->notificationService->sendToMultipleUsers($usersAdmins, $message);

        $money = $this->formatMoney->convertMoneyToString($money);

        $message = Message::profileWithdrawUser(
            $payment->getProfile()->getUser()->getEmail(),
            $idTransaction,
            $createdAt,
            $money
        );

        $this->notificationService->emailToOneUser($message);
        $this->notificationService->sendToOneUser($user, $message);

    }

}