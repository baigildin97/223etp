<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity;


use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class ProcedureRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Procedure::class);
    }

    /**
     * @param Procedure $procedure
     */
    public function add(Procedure $procedure): void {
        $this->entityManager->persist($procedure);
    }

    /**
     * @param Id $id
     * @return Procedure|null|object
     */
    public function get(Id $id): ? Procedure {
        if (!$procedure = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Procedure is not found.');
        }
        return $procedure;
    }

    /**
     * @param string $idNumber
     * @return Procedure|null|object
     */
    public function getIdNumber(string $idNumber): ? Procedure {
        if (!$procedure = $this->repository->findOneBy(['idNumber' => $idNumber])){
            throw new EntityNotFoundException('Procedure is not found.');
        }
        return $procedure;
    }

}