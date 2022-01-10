<?php
declare(strict_types=1);

namespace App\ReadModel\Procedure\Document;


use App\Model\Work\Procedure\Entity\Document\Status;
use App\ReadModel\Procedure\FileView;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class DocumentFetcher
{
    private $connection;
    private $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function all(Filter $filter, int $page, int $size): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'procedure_id',
                'file_type',
                'status',
                'created_at',
                'client_ip',
                'file_path',
                'file_name',
                'file_size',
                'file_real_name',
                'file_hash',
                'file_sign'
            )->from('procedure_documents')
            ->orderBy('created_at', 'desc');

        if ($filter->procedureId) {
            $qb->andWhere('procedure_id = :procedure_id');
            $qb->setParameter(':procedure_id', $filter->procedureId);
        }


        return $this->paginator->paginate($qb, $page, $size);
    }

    /**
     * @param string $procedureId
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAll(string $procedureId): array
    {
        $stmnt = $this->connection->createQueryBuilder()
            ->select('*')->from('procedure_documents')
            ->where('procedure_id = :procedure_id', 'status != :deleted')
            ->setParameters([':procedure_id' => $procedureId, ':deleted' => Status::deleted()->getName()])
            ->orderBy('created_at', 'DESC')
            ->execute();

        $stmnt->setFetchMode(FetchMode::CUSTOM_OBJECT, FileView::class);

        return $stmnt->fetchAll();
    }

    public function findDetail(string $id): ?DetailView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'procedure_id',
                'file_type',
                'status',
                'created_at',
                'client_ip',
                'file_path',
                'file_name',
                'file_size',
                'file_real_name',
                'file_hash',
                'file_sign',
                'file_signed_at'
            )
            ->from('procedure_documents')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }


    /**
     * @param string $procedureId
     * @return array
     */
    public function getAllByProcedureId(string $procedureId): array
    {
        $stmnt = $this->connection->createQueryBuilder()
            ->select('*')->from('procedure_documents')
            ->where('procedure_id = :procedure_id', 'status != :deleted')
            ->setParameters([':procedure_id' => $procedureId, ':deleted' => Status::deleted()->getName()])
            ->orderBy('created_at', 'DESC')
            ->execute();

        $stmnt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);

        return $stmnt->fetchAll();
    }
}