<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Protocol;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class ProtocolRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Protocol::class);
    }

    /**
     * @param Protocol $files
     */
    public function add(Protocol $files): void
    {
        $this->entityManager->persist($files);
    }

    /**
     * @param Id $id
     * @return Protocol|null
     */
    public function get(Id $id): ?Protocol
    {
        if (!$file = $this->repository->find($id->getValue())) {
            throw new EntityNotFoundException('Protocol is not found.');
        }
        return $file;
    }
}
