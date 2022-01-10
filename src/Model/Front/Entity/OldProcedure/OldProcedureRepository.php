<?php
declare(strict_types=1);
namespace App\Model\Front\Entity\OldProcedure;


use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class OldProcedureRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(OldProcedure::class);
    }

    /**
     * @param OldProcedure $procedure
     */
    public function add(OldProcedure $procedure): void {
        $this->entityManager->persist($procedure);
    }

    /**
     * @param Id $id
     * @return OldProcedure|null|object
     */
    public function get(Id $id): ? OldProcedure {
        if (!$procedure = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('OldProcedure is not found.');
        }
        return $procedure;
    }

    /**
     * @param string $idNumber
     * @return OldProcedure|null|object
     */
    public function getIdNumber(string $idNumber): ? OldProcedure {
        if (!$procedure = $this->repository->findOneBy(['idNumber' => $idNumber])){
            throw new EntityNotFoundException('Procedure is not found.');
        }
        return $procedure;
    }

}