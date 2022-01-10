<?php
declare(strict_types=1);
namespace App\Model\User\Entity\User;


use Doctrine\ORM\EntityManagerInterface;
use App\Model\EntityNotFoundException;

class UserRepository implements UserRepositoryInterface
{
    private $entityManager;
    private $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository(User::class);
    }

    public function hasByEmail(Email $email): bool {
        return $this->repository->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->andWhere('t.email = :email')
                ->setParameter(':email', $email->getValue())
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function add(User $user): void {
        $this->entityManager->persist($user);
    }

    /**
     * @param string $confirmToken
     * @return User|null|object
     */
    public function findByConfirmToken(string $confirmToken): ? User {
        return $this->repository->findOneBy(['confirmToken.token' => $confirmToken]);
    }

    /**
     * @param string $resetToken
     * @return User|null|object
     */
    public function findByResetToken(string $resetToken): ? User {
        return $this->repository->findOneBy(['resetToken.token' => $resetToken]);
    }

    public function hasByNetworkIdentity(string $network, string $identity): bool {
        return $this->repository->createQueryBuilder('t')
                ->select('COUNT(t.id)')
                ->innerJoin('t.networks', 'n')
                ->andWhere('n.network = :network and n.identity = :identity')
                ->setParameter(':network', $network)
                ->setParameter(':identity', $identity)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    /**
     * @param Email $email
     * @return User|null|object
     * @throws EntityNotFoundException
     */
    public function getByEmail(Email $email): ? User {
        if (!$user = $this->repository->findOneBy(['email' => $email->getValue()])){
            throw new EntityNotFoundException('User is not found.');
        }
        return $user;
    }

    /**
     * @param Id $id
     * @return User|null|object
     * @throws EntityNotFoundException
     */
    public function get(Id $id): ? User {
        if (!$user = $this->repository->find($id->getValue())){
            throw new EntityNotFoundException('User is not found.');
        }
        return $user;
    }

    public function getAllAdminsAndModerators(): array
    {
        return $this->repository->findBy([
            'role' => [Role::ADMIN, Role::MODERATOR],
            'status' => Status::active()
        ]);
    }
}