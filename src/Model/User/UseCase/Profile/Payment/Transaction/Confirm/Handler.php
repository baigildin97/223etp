<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Payment\Transaction\Confirm;

use App\Model\Flusher;
use App\Model\User\Entity\Profile\Payment\Transaction\Id;
use App\Model\User\Entity\Profile\Payment\Transaction\TransactionRepository;
use App\Model\User\Entity\Profile\Payment\Id as PaymentId;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\Entity\User\UserRepository;
use App\Services\Tasks\Notification;
use Tbbc\MoneyBundle\Formatter\MoneyFormatter;

/**
 * Class Handler
 * @package App\Model\User\UseCase\Profile\Payment\Transaction\Confirm
 */
class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var MoneyFormatter
     */
    private $moneyFormatter;

    /**
     * @var Notification
     */
    private $notificationService;

    private $userRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param TransactionRepository $transactionRepository
     * @param MoneyFormatter $moneyFormatter
     * @param Notification $notificationService
     * @param UserRepository $userRepository
     */
    public function __construct(Flusher $flusher,
                                TransactionRepository $transactionRepository,
                                MoneyFormatter $moneyFormatter,
                                Notification $notificationService,
                                UserRepository $userRepository
    )
    {
        $this->flusher = $flusher;
        $this->transactionRepository = $transactionRepository;
        $this->moneyFormatter = $moneyFormatter;
        $this->notificationService = $notificationService;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void
    {
        $transaction = $this->transactionRepository->get(new Id($command->transactionId));

        if (!$transaction->getPayment()->getId()->isEqual(new PaymentId($command->paymentId))) {
            throw new \DomainException('You cannot confirm the transaction.');
        }

        $transaction->confirm();
        $this->flusher->flush();

        $user = $transaction->getPayment()->getProfile()->getUser();
        $usersAdmins = $this->userRepository->getAllAdminsAndModerators();

        //Deposit
        if ($transaction->getType()->isDeposit()) {
            $message = Message::virtualBalanceReplenished($user->getEmail(),
                $this->moneyFormatter->formatMoney($transaction->money));

            $this->notificationService->sendToOneUser($user, $message);
            $this->notificationService->emailToOneUser($message);

            $message = Message::virtualBalanceReplenishedModerator($user->getEmail(),
                $transaction->getPayment()->getProfile()->getRepresentative()->getPassport()->getFullName());

            //$this->notificationService->sendToMultipleUsers($usersAdmins, $message);
            $this->notificationService->emailToMultipleUsers($usersAdmins, $message);
        }


        if ($transaction->getType()->isWithdraw()) {
            $message = Message::virtualBalanceWithdraw($user->getEmail(),
                $this->moneyFormatter->formatMoney($transaction->money));

            $this->notificationService->sendToOneUser($user, $message);
            $this->notificationService->emailToOneUser($message);

            $message = Message::virtualBalanceWithdrawModerator($user->getEmail(),
                $transaction->getPayment()->getProfile()->getRepresentative()->getPassport()->getFullName());

            //$this->notificationService->emailToMultipleUsers($usersAdmins, $message);
            $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
        }


    }
}
