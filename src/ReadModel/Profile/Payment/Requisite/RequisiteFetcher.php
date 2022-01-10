<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\Payment\Requisite;


use App\Model\User\Entity\Profile\Requisite\Status;
use App\ReadModel\Profile\Payment\Requisite\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class RequisiteFetcher
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
                'payment_id',
                'payment_account',
                'bank_name',
                'bank_bik',
                'bank_address',
                'personal_account',
                'correspondent_account',
                'changed_at',
                'created_at',
                'status'
            )
            ->from('profile_requisites')
            ->orderBy('created_at', 'desc');

        if ($filter->paymentId){
            $qb->andWhere('payment_id = :payment_id');
            $qb->setParameter(':payment_id', $filter->paymentId);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

    /**
     * @return array|null
     */
    public function findAllActiveSelectOptionsList(string $paymentId): ? array {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'payment_account',
                'bank_name'
            )
            ->from('profile_requisites')
            ->where('status = :status')
            ->andWhere('payment_id = :payment_id')
            ->setParameter(':status', Status::active()->getName())
            ->setParameter(':payment_id', $paymentId)
            ->execute();

        $stmt->setFetchMode(FetchMode::ASSOCIATIVE);
        $result =  $stmt->fetchAll();
        return $result ?: null;
    }

    public function findDetail(string $id): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'payment_id',
                'payment_account',
                'bank_name',
                'bank_bik',
                'personal_account',
                'bank_address',
                'correspondent_account',
                'changed_at',
                'created_at',
                'status'
            )
            ->from('profile_requisites')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }


    public function allArray(string $paymentId): ? array {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'bank_name')
            ->from('profile_requisites')
            ->where('payment_id = :payment_id')
            ->setParameter(':payment_id', $paymentId)
            ->execute();

        $stmt->setFetchMode(FetchMode::ASSOCIATIVE);
        $result = $stmt->fetchAll();
        return array_combine(
            array_column($result, 'id'),
            array_column($result, 'bank_name')
        ) ?: null;
    }


    public function existsActiveRequisiteForPaymentId(string $paymentId): bool {
        return $this->connection->createQueryBuilder()
                ->select('COUNT (*)')
                ->from('profile_requisites')
                ->where('status = :status')
                ->setParameter(':status', Status::active()->getName())
                ->andWhere('payment_id = :payment_id')
                ->setParameter(':payment_id', $paymentId)
                ->execute()
                ->fetchColumn(0) > 0;
    }
}
