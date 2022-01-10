<?php
declare(strict_types=1);

namespace App\Model\Admin\Entity\News;

use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;

/**
 * Class NewsRepository
 * @package App\Model\Admin\Entity\News
 */
class NewsRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ObjectRepository
     */
    private $repository;

    /**
     * NewsRepository constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(News::class);
    }

    /**
     * @param News $news
     */
    public function add(News $news): void
    {
        $this->entityManager->persist($news);
    }

    /**
     * @param Id $id
     * @return News|null|object
     */
    public function get(Id $id): ?News
    {
        if (!$settings = $this->repository->find($id->getValue())) {
            throw new EntityNotFoundException('News is not found.');
        }
        return $settings;
    }

}