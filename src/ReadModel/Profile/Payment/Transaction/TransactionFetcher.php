<?php
declare(strict_types=1);

namespace App\ReadModel\Profile\Payment\Transaction;

use App\Model\User\Entity\Profile\Payment\Transaction\Status;
use App\ReadModel\Profile\Payment\Transaction\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class TransactionFetcher
{
    private $connection;
    private $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    /*    public function all(Filter $filter, int $page, int $size): PaginationInterface {
            $qb = $this->connection->createQueryBuilder()
                ->select(
                    'id',
                    'id_number',
                    'payment_id',
                    'type',
                    'status',
                    'money',
                    'updated_at',
                    'created_at'
                )
                ->from('payment_transactions')
                ->orderBy('created_at', 'desc');

            if ($filter->paymentId){
                $qb->andWhere('payment_id = :payment_id');
                $qb->setParameter(':payment_id', $filter->paymentId);
            }

            if ($filter->status){
                $qb->andWhere('status IN (:status)');
                $qb->setParameter(':status', $filter->status,Connection::PARAM_STR_ARRAY);
            }

            return $this->paginator->paginate($qb, $page, $size);
        }*/

    public function all(Filter $filter, int $page, int $size): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                't.id',
                't.id_number',
                't.payment_id',
                't.type',
                't.status',
                't.money',
                't.updated_at',
                't.created_at',
                'p.user_id',
                'u.email',
                'TRIM(CONCAT(re.passport_last_name, \' \', re.passport_first_name, \' \', re.passport_middle_name)) AS user_name'
            )->from('payment_transactions', 't')
            ->leftJoin('t', 'profile_payments', 'pp', 't.payment_id = pp.id')
            ->leftJoin('pp','profile_profiles','p','pp.profile_id = p.id')
            ->leftJoin('p', 'profile_representatives', 're', 'p.representative_id = re.id')
            ->leftJoin('pp', 'user_users', 'u', 'u.id = p.user_id')

            ->orderBy('created_at', 'desc');

        if ($filter->paymentId) {
            $qb->andWhere('t.payment_id = :payment_id');
            $qb->setParameter(':payment_id', $filter->paymentId);
        }

        if ($filter->status) {
            $qb->andWhere('t.status IN (:status)');
            $qb->setParameter(':status', $filter->status, Connection::PARAM_STR_ARRAY);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }


    public function findDetail(string $id): ?DetailView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'type',
                'status',
                'money',
                'updated_at',
                'created_at'
            )
            ->from('payment_transactions')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * @param Status $status
     * @return int|null
     */
    public function countByStatusPending(): ?int
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('payment_transactions')
            ->where('status = :status')
            ->setParameter(':status', \App\Model\User\Entity\Profile\Payment\Transaction\Status::pending()->getName());
        return $qb->execute()->fetchColumn(0);
    }

    /**
     * @return string|null
     * @throws Exception
     */
    public function findLastIdNumber():? string{
        $stmt = $this->connection->createQueryBuilder()
            ->select('id_number')
            ->from('payment_transactions')
            ->orderBy('created_at', 'desc')
            ->execute();

        $stmt->setFetchMode(FetchMode::ASSOCIATIVE);
        $result = $stmt->fetch();

        return $result['id_number'] ?: '0';
    }

}
