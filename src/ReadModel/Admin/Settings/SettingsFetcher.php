<?php
declare(strict_types=1);
namespace App\ReadModel\Admin\Settings;


use App\Model\Admin\Entity\Settings\Key;
use App\ReadModel\Admin\Settings\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class SettingsFetcher
 * @package App\ReadModel\Admin\Settings
 */
class SettingsFetcher
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
     * SettingsFetcher constructor.
     * @param Connection $connection
     * @param PaginatorInterface $paginator
     */
    public function __construct(Connection $connection, PaginatorInterface $paginator){
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function existsByKey(string $key): bool {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('settings')
            ->where('key = :key')
            ->setParameter(':key', $key);
        return $qb->execute()->fetchColumn(0) > 0;
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
                'key',
                'value'
            )
            ->from('settings');

        return $this->paginator->paginate($qb, $page, $size);
    }

    /**
     * @return array|null
     */
    public function allArrayKeyList(): ? array {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'key'
            )
            ->from('settings')
            ->execute();

        $stmt->setFetchMode(FetchMode::ASSOCIATIVE);
        $result =  $stmt->fetchAll();
        return $result ?: null;
    }

    public function allArray(): ? array {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'key',
                'value'
            )
            ->from('settings')
            ->execute();

        $stmt->setFetchMode(FetchMode::ASSOCIATIVE);
        $result =  $stmt->fetchAll();
        return array_combine(
            array_column($result, 'key'),
            array_column($result, 'value')
        ) ?: null;
    }

    /**
     * @param string $id
     * @return DetailView|null
     */
    public function findDetail(string $id): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'key',
                'value'
            )
            ->from('settings')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * @param Key $key
     * @return string
     */
    public function findDetailByKey(Key $key) {
         $qb = $this->connection->createQueryBuilder()
            ->select('value')
            ->from('settings')
            ->where('key = :key')
            ->setParameter(':key', $key->getName());

        return $qb->execute()->fetchColumn();
    }

}