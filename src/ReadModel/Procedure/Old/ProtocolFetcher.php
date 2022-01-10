<?php
namespace App\ReadModel\Procedure\Old;


use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ProtocolFetcher
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
            )->from('old_records.protocols')
            ->where('procedure_id = :procedure_id')
            ->setParameter('procedure_id', $procedureId);

        return $this->paginator->paginate($qb, $page, $size);
    }
}