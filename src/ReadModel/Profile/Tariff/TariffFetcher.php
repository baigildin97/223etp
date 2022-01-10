<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\Tariff;


use App\Model\User\Entity\Profile\Tariff\Status;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class TariffFetcher
{

    private $connection;
    private $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator) {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function all(int $page, int $size): PaginationInterface {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'title',
                'cost',
                'period',
                'unlimited',
                'status',
                'created_at',
                'archived_at'
            )
            ->from('tariff')
            ->orderBy('created_at', 'desc');

        return $this->paginator->paginate($qb, $page, $size);
    }

    /**
     * @param string $id
     * @return DetailView|null
     */
    public function findDetail(string $id): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'title',
                'cost',
                'period',
                'unlimited',
                'status',
                'default_percent',
                'created_at',
                'archived_at'
            )
            ->from('tariff')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function findLevelsByTariffId(string $tariff_id){
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'id_tariff',
                'amount',
                'percent',
                'priority',
                'created_at'
            )
            ->from('levels')
            ->where('id_tariff = :id_tariff')
            ->setParameter(':id_tariff', $tariff_id)
            ->orderBy('priority', 'asc')
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, \App\ReadModel\Profile\Tariff\Levels\DetailView::class);
        $result = $stmt->fetchAll();
        return $result ?: null;
    }

}