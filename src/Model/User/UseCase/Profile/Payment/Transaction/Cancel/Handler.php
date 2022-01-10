<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Payment\Transaction\Cancel;


use App\Model\Admin\Entity\Settings\Key;
use App\Model\Flusher;
use App\Model\User\Entity\Profile\Payment\Transaction\Id;
use App\Model\User\Entity\Profile\Payment\Transaction\TransactionRepository;
use \App\Model\User\Entity\Profile\Payment\Id as PaymentId;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationRepository;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Model\User\Entity\User\User;
use App\Model\User\Entity\User\UserRepository;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\Services\Tasks\Notification;

/**
 * Class Handler
 * @package App\Model\User\UseCase\Profile\Payment\Transaction\Cancel
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
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @var Notification
     */
    private $notificationService;

    /**
     * @var SettingsFetcher
     */
    private $settingsFetcher;

    private $userRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param TransactionRepository $transactionRepository
     * @param NotificationRepository $notificationRepository
     * @param Notification $notificationService
     * @param SettingsFetcher $settingsFetcher
     * @param UserRepository $userRepository
     */
    public function __construct(Flusher $flusher,
                                TransactionRepository $transactionRepository,
                                NotificationRepository $notificationRepository,
                                Notification $notificationService,
                                SettingsFetcher $settingsFetcher,
                                UserRepository $userRepository
    )
    {
        $this->flusher = $flusher;
        $this->transactionRepository = $transactionRepository;
        $this->notificationRepository = $notificationRepository;
        $this->notificationService = $notificationService;
        $this->settingsFetcher = $settingsFetcher;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Command $command
     * @throws \Exception
     */
    public function handle(Command $command): void
    {
        $findWithdrawPeriod = $this->settingsFetcher->findDetailByKey(Key::withdrawPeriod());
        $transaction = $this->transactionRepository->get(new Id($command->transactionId));

        if (!$transaction->getPayment()->getId()->isEqual(new PaymentId($command->paymentId))) {
            throw new \DomainException('You cannot cancel the transaction.');
        }

        if ($command->user_id === $transaction->getPayment()->getProfile()->getUser()->getId()->getValue()){
            $createdAt = $transaction->getCreatedAt()->add(new \DateInterval("PT".$findWithdrawPeriod."M"));
            if (new \DateTimeImmutable() > $createdAt){
                throw new \DomainException("Access denied");
            }
        }

        $transaction->cancel();
        $this->flusher->flush();


        if ($command->user_id !== $transaction->getPayment()->getProfile()->getUser()->getId()->getValue()) {
            $user = $transaction->getPayment()->getProfile()->getUser();

            if ($transaction->getType()->isWithdraw()) {
                $message = Message::virtualBalanceReject(
                    $user->getEmail(),
                    $transaction->getIdNumber()->getValue());

                $this->notificationService->sendToOneUser($user, $message);
                $this->notificationService->emailToOneUser($message);

                $message = Message::virtualBalanceRejectModerator($user->getEmail(),
                    $transaction->getPayment()->getProfile()->getRepresentative()->getPassport()->getFullName());
                $usersAdmins = $this->userRepository->getAllAdminsAndModerators();

                $this->notificationService->sendToMultipleUsers($usersAdmins, $message);
                $this->notificationService->emailToMultipleUsers($usersAdmins, $message);
            }
        }




    }

}