<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Lot\Document;


use App\Model\Work\Procedure\Entity\Lot\Document\Document;
use App\Model\Work\Procedure\Entity\Lot\Document\Status;
use App\ReadModel\Procedure\Lot\Document\Filter\Filter;
use Doctrine\DBAL\Connection;
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
                'lot_id',
                'status',
                'created_at',
                'participant_ip',
                'file_path',
                'file_name',
                'file_size',
                'file_real_name',
                'file_sign',
                'file_hash',
                'document_name'
            )->from('lot_documents')
            ->where('status != :status')
            ->setParameter(':status', Status::archived()->getName())
            ->orderBy('created_at', 'desc');

        if ($filter->lotId){
            $qb->andWhere('lot_id = :lot_id');
            $qb->setParameter(':lot_id', $filter->lotId);
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
                'document_name'
            )
            ->from('lot_documents')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function findDocumentsLots(array $lots){
        $newArr = [];
        array_walk_recursive($lots, function ($item, $key) use (&$newArr) {
            $newArr[] = $item;
        });

            $qb = $this->connection->createQueryBuilder()
                ->select('COUNT (*)')
                ->from('lot_documents', 'l')
                ->where('l.lot_id IN (:lot_id)')
                ->setParameter('lot_id', $newArr, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
                ->andWhere('status = :status')
                ->setParameter(':status', Status::new()->getName());


       // $qb->setFetchMode(FetchMode::ASSOCIATIVE);
      //  $result = $qb->fetchAll();
      //  return $result ?: null;
              return $qb->execute()->fetchColumn() <= 0;

    }
}