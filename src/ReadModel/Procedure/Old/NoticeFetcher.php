<?php
namespace App\ReadModel\Procedure\Old;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class NoticeFetcher
{

    private $connection;
    private $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator){
        $this->connection = $connection;
        $this->paginator = $paginator;
    }


    public function all(int $page, int $size, string $procedureId): PaginationInterface {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'procedure_id',
                'name',
                'text'
            )
            ->from('old_records.notice')
            ->where('procedure_id = :procedure_id')
            ->setParameter('procedure_id', $procedureId);

        return $this->paginator->paginate($qb, $page, $size);
    }

    public function findDetail(string $id): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'procedure_id',
                'name',
                'text'
            )
            ->from('old_records.notice')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

}