<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\Payment;

use App\ReadModel\Profile\Payment\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class PaymentFetcher
 * @package App\ReadModel\Profile\Payment
 */
class PaymentFetcher
{
    private $connection;
    private $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator) {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function all(Filter $filter, int $page, int $size): PaginationInterface {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'invoice_number',
                'available_amount',
                'blocked_amount',
                'withdrawal_amount',
                'created_at'
            )
            ->from('profile_payments')
            ->orderBy('created_at', 'desc');

        if ($filter->profileId){
            $qb->andWhere('profile_id = :profile_id');
            $qb->setParameter(':profile_id', $filter->profileId);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

    public function findDetail(string $id): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'p.id',
                'p.invoice_number',
                'p.available_amount',
                'p.blocked_amount',
                'p.withdrawal_amount',
                'p.created_at',
                'p.profile_id',
                'TRIM(CONCAT(pr.passport_first_name, \' \', pr.passport_middle_name, \' \', pr.passport_last_name)) AS user_full_name'
            )
            ->from('profile_payments', 'p')
            ->where('p.id = :id')
            ->innerJoin('p', 'profile_representatives', 'pr', 'pr.profile_id = p.profile_id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}