<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\Commission;



use App\Model\User\Entity\Commission\Commission\Status;
use App\ReadModel\Profile\Commission\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class CommissionFetcher
{

    private $connection;
    private $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator) {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function all(Filter $filter, int $page, int $size): PaginationInterface {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'title',
                'created_at',
                'archived_at',
                'status'
            )
            ->where('status != :status')
            ->setParameter(':status', Status::archived()->getValue())
            ->from('commissions')
            ->orderBy('created_at', 'desc');

        if ($filter->title){
            $qb->andWhere($qb->expr()->like('title',':title'));
            $qb->setParameter(':title', $filter->title);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

    public function findDetail(string $id): ? array {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'title',
                'created_at',
                'status',
                'changed_at'
            )
            ->from('commissions')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

//        $stmt->setFetchMode(FetchMode::ASSOCIATIVE);
        $view = $stmt->fetch(FetchMode::ASSOCIATIVE);
        return $view ?: null;
    }

}