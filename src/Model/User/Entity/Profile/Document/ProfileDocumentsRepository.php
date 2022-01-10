<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Document;


use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class ProfileDocumentsRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(ProfileDocument::class);
    }

    /**
     * @param ProfileDocument $files
     */
    public function add(ProfileDocument $files): void {
        $this->entityManager->persist($files);
    }

    /**
     * @param Id $id
     * @return File|null|object
     */
    public function get(Id $id): ? ProfileDocument {
        if (!$file = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Profile Document is not found.');
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