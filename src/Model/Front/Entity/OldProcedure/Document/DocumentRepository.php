<?php
declare(strict_types=1);
namespace App\Model\Front\Entity\OldProcedure\Document;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class DocumentRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Document::class);
    }

    /**
     * @param Document $files
     */
    public function add(Document $files): void
    {
        $this->entityManager->persist($files);
    }

    /**
     * @param Id $id
     * @return Document|null
     */
    public function get(Id $id): ?Document
    {
        if (!$file = $this->repository->find($id->getValue())) {
            throw new EntityNotFoundException('OldProcedureFile is not found.');
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
}