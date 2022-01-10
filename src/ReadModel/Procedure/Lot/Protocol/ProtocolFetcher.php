<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Lot\Protocol;


use App\ReadModel\Procedure\Lot\Protocol\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class ProtocolFetcher
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
                'id_number',
                'type',
                'procedure_id',
                'status',
                'created_at',
                'xml_signed_at_organizer'
            )->from('protocols')
            ->orderBy('created_at', 'desc');

        if ($filter->procedureId){
            $qb->andWhere('procedure_id = :procedure_id');
            $qb->setParameter(':procedure_id', $filter->procedureId);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

    public function findDetail(string $id): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'procedure_id',
                'id_number',
                'type',
                'status',
                'created_at',
                'xml_file',
                'xml_sign_organizer',
                'xml_hash_organizer',
                'xml_signed_at_organizer',
                'xml_certificate_thumbprint_organizer',
                'xml_sign_participant',
                'xml_hash_participant',
                'xml_signed_at_participant',
                'xml_certificate_thumbprint_participant',
                'reason',
                'organizer_comment',
            )
            ->from('protocols')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }
}
