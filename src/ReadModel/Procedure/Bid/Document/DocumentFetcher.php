<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Bid\Document;


use App\Model\Work\Procedure\Entity\Lot\Bid\Document\Status;
use App\ReadModel\Procedure\Bid\Document\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class DocumentFetcher
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * LotFetcher constructor.
     * @param Connection $connection
     * @param PaginatorInterface $paginator
     */
    public function __construct(Connection $connection, PaginatorInterface $paginator){
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    /**
     * @param Filter $filter
     * @param int $page
     * @param int $size
     * @return PaginationInterface
     */
    public function all(Filter $filter, int $page, int $size): PaginationInterface {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'bid_id',
                'status',
                'created_at',
                'participant_ip',
                'file_path',
                'file_name',
                'file_size',
                'file_real_name',
                'file_sign',
                'file_hash',
                'signed_at',
                'document_name'
            )->from('bid_documents')
            ->where('status != :status')
            ->setParameter(':status', Status::archived()->getName())
            ->orderBy('created_at', 'desc');

        if ($filter->bidId){
            $qb->andWhere('bid_id = :bid_id');
            $qb->setParameter(':bid_id', $filter->bidId);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

    public function allDoc(Filter $filter, int $page, int $size): PaginationInterface {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'bid_id',
                'status',
                'created_at',
                'participant_ip',
                'file_path',
                'file_name',
                'file_size',
                'file_real_name',
                'file_sign',
                'file_hash',
                'signed_at',
                'document_name'
            )->from('bid_documents')
            ->where('status != :status')
            ->setParameter(':status', Status::archived()->getName())
            ->orderBy('created_at', 'desc');

        if ($filter->bidId){
            $qb->andWhere('bid_id = :bid_id');
            $qb->setParameter(':bid_id', $filter->bidId);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }


    /**
     * @param string $id
     * @return DetailView|null
     */
    public function findDetail(string $id): ? DetailView{
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'bid_id',
                'status',
                'created_at',
                'participant_ip',
                'file_path',
                'file_name',
                'file_size',
                'file_real_name',
                'file_sign',
                'file_hash',
                'signed_at',
                'document_name'
            )
            ->from('bid_documents')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }


    /**
     * @param string $id
     * @param string $procedureId
     * @param string $fileType
     * @return DetailView|null
     */
    public function findDetailByProcedureFileType(string $procedureId, string $fileType): ? DetailView {
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
                'signed_at',
                'file_real_name',
                'file_hash',
                'file_sign'
            )
            ->from('procedure_documents')
            ->where('procedure_id = :procedure_id')
            ->andWhere('file_type = :file_type')
            ->setParameter(':file_type', $fileType)
            ->setParameter(':procedure_id', $procedureId)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * @param string $bidId
     * @return int
     * @throws Exception
     */
    public function countNewFiles(string $bidId): int{
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('bid_documents')
            ->where('bid_id = :bid_id')
            ->setParameter(':bid_id', $bidId)
            ->andWhere('status = :status')
            ->setParameter(':status', Status::NEW);

        return $qb->execute()->fetchColumn(0) ?: 0;
    }
}