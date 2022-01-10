<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Commission\Members;


use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class MemberRepository
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(Member::class);
    }

    /**
     * @param Member $member
     */
    public function add(Member $member): void {
        $this->entityManager->persist($member);
    }

    /**
     * @param Id $id
     * @return Member|null|object
     */
    public function get(Id $id): ? Member {
        if (!$member = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('Commission is not found.');
        }
        return $member;
    }
}