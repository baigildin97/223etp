<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Edit\EditBank;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\Profile\Requisite\Requisite;
use App\Model\User\Entity\Profile\Status;
use App\Model\User\Entity\User\Id;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\User\Entity\Profile\Requisite\Id as RequisiteId;
use App\Model\User\Entity\Profile\Requisite\Status as RequisiteStatus;

class Handler
{
    private $requisiteRepository;
    private $flusher;
    private $em;
    private $profileRepository;

    public function __construct(EntityManagerInterface $em, Flusher $flusher, ProfileRepository $profileRepository)
    {
        $this->flusher = $flusher;
        $this->em = $em;
        $this->requisiteRepository = $em->getRepository(Requisite::class);
        $this->profileRepository = $profileRepository;
    }

    public function handle(Command $command): void
    {
        //При изменении реквизитов старые не сохраняются
        //$requisite = $this->requisiteRepository->find($command->requisite_id);
        //$requisite->update($command->bankName, $command->bankBik,
        //    $command->correspondentAccount, $command->paymentAccount);

        $profile = $this->profileRepository->getByUser(new Id($command->user_id));
        $this->requisiteRepository->find(new RequisiteId($command->requisite_id))->setStatus(RequisiteStatus::inactive());

        $requisite = new Requisite($profile, $command->paymentAccount, $command->bankName,
            $command->bankBik, $command->correspondentAccount, new \DateTimeImmutable());

        $profile->setRequisite($requisite);

        $this->em->persist($profile);

        $this->flusher->flush();
    }
}