<?php
declare(strict_types=1);

namespace App\ReadModel\Admin\News;

use App\ReadModel\Admin\News\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class NewsFetcher
 * @package App\ReadModel\Admin\News
 */
class NewsFetcher
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
    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    /**
     * @param Filter $filter
     * @param int $page
     * @param int $size
     * @return PaginationInterface
     */
    public function all(Filter $filter, int $page, int $size): PaginationInterface
    {
        $currentDate = new \DateTimeImmutable();
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'subject',
                'text',
                'delayed_publication',
                'created_at'
            )
            ->from('news')
            ->orderBy('created_at', 'desc')
            ->andWhere('delayed_publication < :delayed_publication')
            ->setParameter(':delayed_publication', $currentDate->format("Y-m-d H:i:s"));


        if ($filter->status) {
            $qb->andWhere($qb->expr()->like('status', ':status'));
            $qb->setParameter(':status', $filter->status);
        }


        return $this->paginator->paginate($qb, $page, $size);
    }

    public function findDetail(string $id): ?DetailView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'subject',
                'text',
                'created_at'
            )
            ->from('news')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }


}