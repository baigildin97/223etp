<?php
declare(strict_types=1);
namespace App\Model\User\Entity\User\Notification;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class NotificationRepository
{
    private $entityManager;
    private $repository;
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Notification::class);
    }

    public function add(Notification $notification): void {
        $this->entityManager->persist($notification);
    }

    public function get(Id $id): ? Notification {
        if (!$notification = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Notification is not found.');
        }
        return $notification;
    }

}