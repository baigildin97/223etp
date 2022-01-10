<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Create\Organizer\LegalEntity;


use App\Helpers\Filter;
use App\Model\Flusher;
use App\Model\User\Entity\Certificate\CertificateRepository;
use App\Model\User\Entity\Certificate\Id as CertificateId;
use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\Profile\Role;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\User\Entity\Profile\Id as ProfileId;

/**
 * Class Handler
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
     * @var Role\RoleRepository
     */
    private $roleRepository;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * Handler constructor.
     * @param UserRepository $userRepository
     * @param CertificateRepository $certificateRepository
     * @param Flusher $flusher
     * @param Role\RoleRepository $roleRepository
     * @param Filter $filter
     */
    public function __construct(
        UserRepository $userRepository,
        CertificateRepository $certificateRepository,
        Flusher $flusher,
        Role\RoleRepository $roleRepository,
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

        $arrUserName = $certificate->getSubjectName()->getUserNameExploded();

        if(!$certificate->isLegalEntity()){
            throw new \DomainException("У данного сертификата тип, не является юридическим лицом");
        }

        if($user->getExistsProfile()){
            throw new \DomainException("Ваш профиль уже заполнен!");
        }

        $role = $this->roleRepository->findByRoleConstant(Role\Role::ROLE_ORGANIZER);

        $profile = Profile::createLegalEntity(
            ProfileId::next(),
            $role,
            $certificate->getSubjectName()->getPosition(),
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
