<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\Commission\Member;


use App\Model\User\Entity\Commission\Members\Status;
use App\ReadModel\Profile\Commission\Member\Filter\Filter;
use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class MemberFetcher
{

    private $connection;
    private $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator) {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function all(Filter $filter, int $page, int $size, string $commissionId): PaginationInterface {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'first_name',
                'last_name',
                'middle_name',
                'positions',
                'role',
                'created_at',
                'archived_at',
                'status'
            )
            ->where('commission_id = :commission_id')
            ->setParameter(':commission_id', $commissionId)
            ->andWhere('status != :status')
            ->setParameter(':status', Status::archived()->getValue())
            ->from('commission_members')
            ->orderBy('created_at', 'desc');

        if ($filter->role){
            $qb->andWhere($qb->expr()->like('role',':role'));
            $qb->setParameter(':role', $filter->role);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

//    public function findDetail(string $id): ? array {
//        $stmt = $this->connection->createQueryBuilder()
//            ->select(
//                't.id',
//                't.title',
//                't.created_at',
//                't.status',
//                't.changed_at',
//                'qw.created_at as member_created_at'
//            )
//            ->from('commissions','t')
//            ->rightJoin('t','commission_members','qw','t.id = qw.commission_id')
//            ->where('t.id = :id')
//            ->setParameter(':id', $id)
//            ->execute();
//
////        $stmt->setFetchMode(FetchMode::ASSOCIATIVE);
//        $view = $stmt->fetchAll(FetchMode::ASSOCIATIVE);
//        return $view ?: null;
//    }

}