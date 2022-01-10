<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Representative;

use App\Model\EntityNotFoundException;
use App\Model\User\Entity\Profile\Representative\Representative;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\User\Entity\Profile\Id as ProfileId;

class RepresentativeRepository
{

    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Representative::class);
    }

    /**
     * @param Representative $representative
     */
    public function add(Representative $representative): void {
        $this->entityManager->persist($representative);
    }

    /**
     * @param Id $id
     * @return Representative|null|object
     */
    public function get(Id $id): ? Representative {
        if (!$representative = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Profile is not found.');
        }
        return $representative;
    }

    /**
     * @param ProfileId $id
     * @return Representative|null|object
     */
    public function getByProfile(ProfileId $id): ? Representative {
        return $this->repository->findOneBy(['profile' => $id->getValue()]);
    }
}