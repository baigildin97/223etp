<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\XmlDocument;


use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class XmlDocumentRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(XmlDocument::class);
    }

    /**
     * @param XmlDocument $files
     */
    public function add(XmlDocument $files): void {
        $this->entityManager->persist($files);
    }

    /**
     * @param Id $id
     * @return XmlDocument|null
     */
    public function get(Id $id): ? XmlDocument {
        if (!$file = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('XmlDocument is not found.');
        }
        return $file;
    }

}