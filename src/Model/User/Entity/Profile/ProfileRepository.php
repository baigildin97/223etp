<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile;

use App\Model\EntityNotFoundException;
use App\Model\User\Entity\Profile\Requisite\Requisite;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\User\Entity\User\Id as UserId;
use App\Model\Work\Entity\Role\Id as RoleId;
class ProfileRepository
{

    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Profile::class);
    }

    // TODO - дописать метод
    public function hasWithRole(RoleId $id): bool {
        return false;
    }

    /**
     * @param Profile $profile
     */
    public function add(Profile $profile): void {
        $this->entityManager->persist($profile);
    }

    /**
     * @param Id $id
     * @return Profile|null|object
     */
    public function get(Id $id): ? Profile {
        if (!$profile = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Profile is not found.');
        }
        return $profile;
    }

    /**
     * @param UserId $id
     * @return Profile|null|object
     */
    public function getByUser(UserId $id): ? Profile {
        return $this->repository->findOneBy(['user' => $id->getValue()]);
    }

}