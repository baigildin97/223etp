<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Requisite;

use App\Model\EntityNotFoundException;
use App\Model\User\Entity\Profile\Requisite\Requisite;
use Doctrine\ORM\EntityManagerInterface;
use App\Model\User\Entity\Profile\Id as ProfileId;

class RequisiteRepository
{

    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Requisite::class);
    }

    /**
     * @param Requisite $requisite
     */
    public function add(Requisite $requisite): void {
        $this->entityManager->persist($requisite);
    }

    /**
     * @param Id $id
     * @return Requisite|null|object
     */
    public function get(Id $id): ? Requisite {
        if (!$requisite = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Requisite is not found.');
        }
        return $requisite;
    }

    /**
     * @param ProfileId $id
     * @return Requisite|null|object
     */
    public function getByProfile(ProfileId $id): ? Requisite {
        return $this->repository->findOneBy(['profile' => $id->getValue()]);
    }
}