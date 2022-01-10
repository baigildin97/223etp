<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Certificate;


use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class CertificateRepository
{

    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Certificate::class);
    }

    /**
     * @param Id $id
     * @return Certificate|null|object
     */
    public function get(Id $id): ? Certificate {
        if (!$certificate = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Certificate is not found.');
        }
        return $certificate;
    }

    public function remove(Certificate $task): void
    {
        $this->entityManager->remove($task);
    }

    /**
     * @param string $token
     * @return Certificate|null|object
     */
    public function getByConfirmToken(string $token): ? Certificate {
        if (!$certificate = $this->repository->findOneBy(['confirmToken.token' => $token]))
            throw new EntityNotFoundException('Certificate is not found.');

        return $certificate;
    }
}