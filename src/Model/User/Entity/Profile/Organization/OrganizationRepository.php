<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Organization;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\User\Entity\Profile\Id as ProfileId;

class OrganizationRepository
{

    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Organization::class);
    }

    /**
     * @param Organization $organization
     */
    public function add(Organization $organization): void {
        $this->entityManager->persist($organization);
    }

    /**
     * @param Id $id
     * @return Organization|null|object
     */
    public function get(Id $id): ? Organization {
        if (!$organization = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Profile is not found.');
        }
        return $organization;
    }

    /**
     * @param ProfileId $id
     * @return Organization|null|object
     */
    public function getByProfile(ProfileId $id): ? Organization {
        return $this->repository->findOneBy(['profile' => $id->getValue()]);
    }
}