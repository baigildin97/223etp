<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Document;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class ProcedureDocumentRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(ProcedureDocument::class);
    }

    /**
     * @param ProcedureDocument $files
     */
    public function add(ProcedureDocument $files): void
    {
        $this->entityManager->persist($files);
    }

    /**
     * @param Id $id
     * @return File|null|object
     */
    public function get(Id $id): ?ProcedureDocument
    {
        if (!$file = $this->repository->find($id->getValue())) {
            throw new EntityNotFoundException('ProcedureFile is not found.');
        }
        return $file;
    }

    /**
     * @param Id $id
     */
    public function delete(Id $id): void
    {
        $this->repository->find($id->getValue())->setStatus(Status::deleted());
    }

    /**
     * @param string $sign
     */
    public function updateSign(Id $id, string $sign): void
    {

    }
}