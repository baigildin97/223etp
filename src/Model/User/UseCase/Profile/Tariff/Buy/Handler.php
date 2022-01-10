<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Tariff\Buy;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\Profile\SubscribeTariff\SubscribeTariff;
use App\Model\User\Entity\Profile\SubscribeTariff\SubscribeTariffRepository;
use App\Model\User\Entity\Profile\Tariff\Id as TariffId;
use App\Model\User\Entity\Profile\Tariff\TariffRepository;
use App\Model\User\Entity\Profile\SubscribeTariff\Id as SubscribeTariffId;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Notification\Category;
use App\Model\User\Entity\User\Notification\NotificationType;
use App\Services\Tasks\Notification;

class Handler
{

    private $flusher;

    private $profileRepository;

    private $tariffRepository;

    private $subscribeTariffRepository;


    private $notificationService;

    public function __construct(
        Flusher $flusher,
        ProfileRepository $profileRepository,
        TariffRepository $tariffRepository,
        SubscribeTariffRepository $subscribeTariffRepository,
        Notification $notificationService
    ) {
        $this->flusher = $flusher;
        $this->profileRepository = $profileRepository;
        $this->tariffRepository = $tariffRepository;
        $this->subscribeTariffRepository = $subscribeTariffRepository;
        $this->notificationService = $notificationService;
    }

    /**
     * @param Command $command
     * @throws \Exception
     */
    public function handle(Command $command): void {
        $tariff = $this->tariffRepository->get(new TariffId($command->tariffId));
        $profile = $this->profileRepository->getByUser(new Id($command->userId));

        $subscribeTariff = new SubscribeTariff(
            SubscribeTariffId::next(),
            $tariff,
            $createdAt = new \DateTimeImmutable(),
            $createdAt->add(new \DateInterval('P'.$tariff->getPeriod().'M')),
            $profile
        );

        $profile->subscribeToTariff($subscribeTariff, $createdAt);
        $this->flusher->flush();

   /*     $this->notificationService->createOne(
            NotificationType::buyTariff(
                $profile->getRepresentative()->getPassport()->getFullName(),
                $tariff->getTitle()
            ),
            Category::categoryOne(),
            $profile->getUser()
        );*/
    }

}