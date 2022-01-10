<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Bid\XmlDocument;


use App\ReadModel\Procedure\Bid\XmlDocument\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class XmlDocumentFetcher
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
                'x.id',
                'x.bid_id',
                'x.status',
                'x.file',
                'x.hash',
                'x.sign',
                'x.category',
                'x.created_at',
                'b.lot_id',
                'l.procedure_id'
            )->from('bid_xml_documents', 'x')
            ->innerJoin('x','bids','b','b.id = x.bid_id')
            ->innerJoin('b','lots','l','b.lot_id = l.id')
            ->orderBy('created_at', 'desc');

        if ($filter->bidId){
            $qb->andWhere('x.bid_id = :bid_id');
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
                'x.id',
                'x.bid_id',
                'x.status',
                'x.file',
                'x.hash',
                'x.sign',
                'x.category',
                'x.created_at',
                'b.lot_id',
                'l.procedure_id'
            )
            ->from('bid_xml_documents', 'x')
            ->where('x.id = :id')
            ->setParameter(':id', $id)
            ->innerJoin('x','bids','b','b.id = x.bid_id')
            ->innerJoin('b','lots','l','b.lot_id = l.id')
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findDetailXmlFile(string $xmlFileId){
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'file', 'hash', 'sign')
            ->from('bid_xml_documents')
            ->where('id = :id')
            ->setParameter(':id', $xmlFileId)
            ->execute()->fetch(FetchMode::STANDARD_OBJECT);

        return $stmt;
    }
}