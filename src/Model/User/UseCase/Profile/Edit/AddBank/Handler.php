<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Edit\AddBank;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Id;
use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\Profile\Requisite\Requisite;
use App\Model\User\Entity\Profile\Requisite\RequisiteRepository;
use DateTimeImmutable;

class Handler
{

    private $profileRepository;
    private $flusher;

    public function __construct(Flusher $flusher, ProfileRepository $profileRepository)
    {
        $this->flusher = $flusher;
        $this->profileRepository = $profileRepository;
    }

    public function handle(Command $command): void
    {
        $profile = $this->profileRepository->get(new Id($command->profileId));

        $requisite = new Requisite(
            $profile,
            $command->paymentAccount,
            $command->bankName,
            $command->bankBik,
            $command->correspondentAccount,
            new DateTimeImmutable()
        );

        $profile->addRequisite($requisite);

        $this->flusher->flush();
    }
}