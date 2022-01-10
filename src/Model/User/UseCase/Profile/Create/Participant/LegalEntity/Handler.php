<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Create\Participant\LegalEntity;


use App\Helpers\Filter;
use App\Model\Flusher;
use App\Model\User\Entity\Certificate\CertificateRepository;
use App\Model\User\Entity\Certificate\Id as CertificateId;
use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\Profile\Role\Role;
use App\Model\User\Entity\Profile\Role\RoleRepository;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;

/**
 * Class Handler
 * @package App\Model\User\UseCase\Profile\Create\Participant\LegalEntity
 */
class Handler
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var CertificateRepository
     */
    private $certificateRepository;

    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * Handler constructor.
     * @param UserRepository $userRepository
     * @param Flusher $flusher
     * @param CertificateRepository $certificateRepository
     * @param RoleRepository $roleRepository
     * @param Filter $filter
     */
    public function __construct(
        UserRepository $userRepository,
        Flusher $flusher,
        CertificateRepository $certificateRepository,
        RoleRepository $roleRepository,
        Filter $filter
    ) {
        $this->userRepository = $userRepository;
        $this->flusher = $flusher;
        $this->certificateRepository = $certificateRepository;
        $this->roleRepository = $roleRepository;
        $this->filter = $filter;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void {
        $user = $this->userRepository->get(new Id($command->userId));
        $certificate = $this->certificateRepository->get(new CertificateId($command->certificate));

        if(!$certificate->isLegalEntity()){
            throw new \DomainException("У данного сертификата тип, не является юридическим лицом");
        }

        if($user->getExistsProfile()){
            throw new \DomainException("Ваш профиль уже заполнен!");
        }

        $role = $this->roleRepository->findByRoleConstant(Role::ROLE_PARTICIPANT);

        $arrUserName = $certificate->getSubjectName()->getUserNameExploded();

        $profile = Profile::createLegalEntity(
            \App\Model\User\Entity\Profile\Id::next(),
            $role,
            $command->position,
            $command->confirmingDocument,
            $this->filter->onlyNumbers($command->phone),
            $arrUserName[1],
            $arrUserName[0],
            $arrUserName[2],
            $certificate->getSubjectName()->getInn(),
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
            $command->shortTitleOrganization,
            $command->fullTitleOrganization,
            $command->ogrn,
            $certificate->getSubjectName()->getEmail(),
            $command->kpp,
            $certificate->getSubjectName()->getSnils(),
            new \DateTimeImmutable(),
            $user,
            $command->webSite,
            $command->clientIp,
            $command->representativeInn
        );

        $profile->attachCertificate($certificate);

        $user->addProfile($profile);

        $this->flusher->flush();
    }
}
