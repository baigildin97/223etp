<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Document;


use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class DocumentRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Document::class);
    }

    /**
     * @param Document $document
     */
    public function add(Document $document): void {
        $this->entityManager->persist($document);
    }

    /**
     * @param Id $id
     * @return Document|null|object
     */
    public function get(Id $id): ? Document {
        if (!$lot = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Document is not found.');
        }
        return $lot;
    }
}