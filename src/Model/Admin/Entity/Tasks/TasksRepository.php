<?php
declare(strict_types=1);
namespace App\Model\Admin\Entity\Tasks;


use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class TasksRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Tasks::class);
    }

    /**
     * @param Tasks $settings
     */
    public function add(Tasks $settings): void {
        $this->entityManager->persist($settings);
    }

    /**
     * @param Id $id
     * @return Tasks|null|object
     */
    public function get(Id $id): ? Tasks {
        if (!$settings = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Tasks is not found.');
        }
        return $settings;
    }

}