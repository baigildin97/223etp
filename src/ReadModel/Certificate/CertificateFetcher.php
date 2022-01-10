<?php
declare(strict_types=1);
namespace App\ReadModel\Certificate;


use App\Model\User\Entity\Certificate\Status;
use App\ReadModel\Certificate\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class CertificateFetcher
 * @package App\ReadModel\Certificate
 */
class CertificateFetcher
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
     * CertificateFetcher constructor.
     * @param Connection $connection
     * @param PaginatorInterface $paginator
     */
    public function __construct(Connection $connection, PaginatorInterface $paginator){
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    /**
     * @param Status $status
     * @param Filter $filter
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    public function existsByStatusCertificates(Status $status, Filter $filter): bool {
        $qb = $this->connection->createQueryBuilder()
                ->select('COUNT (*)')
                ->from('certificates')
                ->where('status = :status')
                ->setParameter(':status', $status->getName());


        if ($filter->user_id) {
            $qb->andWhere('user_id = :user_id');
            $qb->setParameter(':user_id', $filter->user_id);
        }

        return $qb->execute()->fetchColumn(0) > 0;
    }

    /**
     * @param string $thumbprint
     * @return bool
     * @throws \Doctrine\DBAL\Exception
     */
    public function existsByThumbprintCertificates(string $thumbprint): bool {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('certificates')
            ->where('thumbprint = :thumbprint')
            ->setParameter(':thumbprint', $thumbprint);

        return $qb->execute()->fetchColumn(0) > 0;
    }

    /**
     * @param string $user_id
     * @return array|null
     * @throws \Doctrine\DBAL\Exception
     */
    public function activeCertificateListForUser(string $user_id): ? array {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'case when subject_name_ogrn_ip is not null 
                then  TRIM(CONCAT(subject_name_owner, \' - Действителен до: \',   valid_to))
                else TRIM(CONCAT(subject_name_user_name, \' - Действителен до: \', valid_to)) end as name'
            )
            ->from('certificates')
            ->where('user_id = :user_id')
            ->setParameter(':user_id',  $user_id)
            ->andWhere('status = :status')
            ->setParameter('status', Status::active()->getName())
            ->execute();

        $stmt->setFetchMode(FetchMode::ASSOCIATIVE);
        $result =  $stmt->fetchAll();
        return $result ?: null;
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
                'created_at',
                'thumbprint',
                'subject_name_owner',
                'valid_from',
                'valid_to',
                'status'
            )
            ->from('certificates')
            ->orderBy('created_at', 'desc');

        if ($filter->user_id) {
            $qb->andWhere('user_id = :user_id');
            $qb->setParameter(':user_id', $filter->user_id);
        }

        if ($filter->id){
            $qb->andWhere($qb->expr()->like('id',':id'));
            $qb->setParameter(':id', $filter->id);
        }

        if ($filter->thumbprint){
            $qb->andWhere($qb->expr()->like('thumbprint',':thumbprint'));
            $qb->setParameter(':thumbprint', $filter->thumbprint);
        }

        if ($filter->subject_name_owner){
            $qb->andWhere('subject_name_owner = :subject_name_owner');
            $qb->setParameter(':subject_name_owner', $filter->subject_name_owner);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

    public function allForCheck(): ? array {
        $currentDate = new \DateTimeImmutable();
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'created_at',
                'thumbprint',
                'subject_name_owner',
                'valid_from',
                'valid_to',
                'status'
            )
            ->from('certificates')
            ->where('valid_to < :valid_to')
            ->setParameter(':valid_to', $currentDate->format("Y-m-d H:i:s"))
            ->setMaxResults('50')
            ->execute();

        $result = $qb->fetchAll();
        return $result ?: null;

    }

    /**
     * @param string $id
     * @return DetailView|null
     */
    public function findDetail(string $id): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'user_id',
                'thumbprint',
                'created_at',
                'archived_date',
                'valid_from',
                'valid_to',
                'status',
                'subject_name_ogrn',
                'subject_name_ogrn_ip',
                'subject_name_snils',
                'subject_name_inn',
                'subject_name_email',
                'subject_name_owner',
                'subject_name_position',
                'subject_name_user_name',
                'subject_name_region',
                'subject_name_city',
                'subject_name_street',
                'issuer_name_issuer',
                'issuer_name_unit_organization',
                'issuer_name_inn',
                'issuer_name_ogrn',
                'issuer_name_email',
                'issuer_name_region',
                'issuer_name_city',
                'issuer_name_street'
            )
            ->from('certificates')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * @param ?string $thumbprint
     * @return DetailView|null
     */
    public function findDetailByThumbprint(?string $thumbprint):? DetailView{
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'thumbprint',
                'sign',
                'subject_name_user_name',
                'created_at',
                'subject_name_email'
            )
            ->from('certificates')
            ->where('thumbprint = :thumbprint')
            ->setParameter(':thumbprint', $thumbprint)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * @param string $id
     * @return string|null
     */
    public function getThumbprint(string $id): ? string {
        $stmt = $this->connection->createQueryBuilder()
            ->select('thumbprint')
            ->from('certificates')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $result = $stmt->fetch();

        return $result['thumbprint'] ?: null;
    }

    public function getUserEmailByThumbprint(string $thumbprint): ? string {
        $stmt = $this->connection->createQueryBuilder()
            ->select('u.email')
            ->from('certificates', 'c')
            ->innerJoin('c', 'user_users', 'u', 'c.user_id = u.id')
            ->where('c.thumbprint = :thumbprint')
            ->andWhere('c.status = :status')
            ->setParameter(':status', Status::active()->getName())
            ->setParameter(':thumbprint', $thumbprint)
            ->execute();

        return $stmt->fetchOne() ?: '';
    }

    public function getUserByInn(string $inn): ? string {
        $stmt = $this->connection->createQueryBuilder()
            ->select('u.id')
            ->from('certificates', 'c')
            ->innerJoin('c', 'user_users', 'u', 'c.user_id = u.id')
            ->where('subject_name_inn = :inn')
            ->setParameter(':inn', $inn)
            ->execute();

        return ($result = $stmt->fetchOne()) ? $result : null;
    }
}