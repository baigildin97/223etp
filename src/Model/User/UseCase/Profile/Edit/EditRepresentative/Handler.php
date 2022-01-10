<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Edit\EditRepresentative;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\Profile\Representative\Passport;
use App\Model\User\Entity\Profile\Representative\Representative;
use App\Model\User\Entity\Profile\Status;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\User\Entity\Profile\Id;

class Handler
{
    private $profileRepository;
    private $flusher;
    private $em;

    public function __construct(EntityManagerInterface $em, Flusher $flusher, ProfileRepository $profileRepository)
    {
        $this->flusher = $flusher;
        $this->em = $em;
        $this->profileRepository = $profileRepository;
    }

    public function handle(Command $command): void
    {
        /*$profile = $this->profileRepository->get(new Id($command->profile_id));

        $passport = new Passport($command->passportSeries, $command->passportNumber
            , $command->passportIssuer, $command->passportIssuanceDate,
            $command->citizenship, $command->unitCode, $command->birthDate);

        if (in_array($profile->getStatus()->getName(), [Status::rejected()->getName(), Status::active()->getName()])) {
            $representative = new Representative($profile, $command->email, $command->position,
                $command->confirmingDocument, $command->phone, new \DateTimeImmutable(), $command->fio);
            $representative->setPassport($passport);

            $profile->setRepresentative($representative);
            $profile->setStatus(Status::wait());
        }
        elseif (in_array($profile->getStatus()->getName(), [Status::draft()->getName(), Status::wait()->getName()]))
        {
            $representative = $profile->getRepresentative();

            $representative->update($command->position, $command->email, $command->phone, $command->confirmingDocument, $command->fio);
            $representative->setPassport($passport);
        }
        else
            throw new \DomainException('Нельзя редактировать профиль находящийся на модерации
             или который был заблокирован или удален');

        $this->em->persist($profile);
        $this->em->flush();*/
    }
}