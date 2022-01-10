<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Create\Organizer\IndividualEntrepreneur;

use App\Helpers\Filter;
use App\Model\Flusher;
use App\Model\User\Entity\Certificate\CertificateRepository;
use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\Profile\Role;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Entity\Certificate\Id as CertificateId;
use App\Model\User\Entity\Profile\Id as ProfileId;
use App\ReadModel\Certificate\CertificateFetcher;
use App\ReadModel\Profile\ProfileFetcher;

class Handler
{
    private $userRepository;
    private $flusher;
    private $certificateRepository;
    private $certificateFetcher;
    private $roleRepository;
    private $filter;

    public function __construct(
        UserRepository $userRepository,
        Flusher $flusher,
        CertificateRepository $certificateRepository,
        CertificateFetcher $certificateFetcher,
        Role\RoleRepository $roleRepository,
        Filter $filter
    ) {
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
        $this->certificateRepository = $certificateRepository;
        $this->certificateFetcher = $certificateFetcher;
        $this->roleRepository = $roleRepository;
        $this->filter = $filter;
    }

    public function handle(Command $command): void {
        $user = $this->userRepository->get(new Id($command->userId));

        $certificate = $this->certificateRepository->get(new CertificateId($command->certificate));

        if(!$certificate->isIndividualEntrepreneur()){
            throw new \DomainException("У данного сертификата тип, не является индивидуальным предпринимателем");
        }

        if($user->getExistsProfile()){
            throw new \DomainException("Ваш профиль уже заполнен!");
        }

        $arrUserName = $certificate->getSubjectName()->getUserNameExploded();

        $role = $this->roleRepository->findByRoleConstant(Role\Role::ROLE_ORGANIZER);

        $profile = Profile::createIndividualEntrepreneur(
            ProfileId::next(),
            $role,
            $command->position,
            $this->filter->onlyNumbers($command->phone),
            $arrUserName[1],
            $arrUserName[0],
            $arrUserName[2],
            $certificate->getSubjectName()->getInn(),
            $certificate->getSubjectName()->getOwner(),
            $command->ogrnip,
            $certificate->getSubjectName()->getEmail(),
            $certificate->getSubjectName()->getSnils(),
            $command->passportSeries,
            $command->passportNumber,
            $command->issuer,
            new \DateTimeImmutable($command->issuanceDate),
            $command->citizenship,
            $command->unitCode,
            new \DateTimeImmutable($command->birthDay),
            $command->factCountry,
            $command->factRegion,
            $command->factCity,
            $command->factIndex,
            $command->factStreet,
            $command->factHouse,
            $command->legalCountry,
            $command->legalRegion,
            $command->legalCity,
            $command->legalIndex,
            $command->legalStreet,
            $command->legalHouse,
            new \DateTimeImmutable(),
            $user,
            $command->webSite,
            $command->clientIp
        );



        $profile->attachCertificate($certificate);

        $user->addProfile($profile);

        $this->flusher->flush();
    }
}
