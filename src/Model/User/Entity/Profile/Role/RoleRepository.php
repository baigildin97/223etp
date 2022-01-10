<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Role;


use App\Model\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

class RoleRepository
{
    /**
     * @var \Doctrine\ORM\EntityRepository
     */
    private $repo;
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Role::class);
        $this->em = $em;
    }

    public function hasByName(string $name, string $roleConstant): bool
    {
        return $this->repo->createQueryBuilder('t')
                ->select('COUNT(t.name)')
                ->andWhere('t.name = :name')
                ->andWhere('t.roleConstant = :roleConstant')
                ->setParameter(':name', $name)
                ->setParameter(':roleConstant', $roleConstant)
                ->getQuery()->getSingleScalarResult() > 0;
    }

    public function get(Id $id): Role
    {
        /** @var Role $role */
        if (!$role = $this->repo->find($id->getValue())) {
            throw new EntityNotFoundException('Role is not found.');
        }
        return $role;
    }

    /**
     * @param string $roleConstant
     * @return Role|null|object
     */
    public function findByRoleConstant(string $roleConstant): ? Role {
        return $this->repo->findOneBy(['roleConstant' => $roleConstant]);
    }

    public function add(Role $role): void
    {
        $this->em->persist($role);
    }

    public function remove(Role $role): void
    {
        $this->em->remove($role);
    }
}