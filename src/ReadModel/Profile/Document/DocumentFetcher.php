<?php
declare(strict_types=1);

namespace App\ReadModel\Profile\Document;

use App\Model\User\Entity\Profile\Document\Status as FileStatus;
use App\Model\User\Entity\Profile\XmlDocument\Status;
use App\ReadModel\Profile\Document\Filter\Filter;
use App\ReadModel\Profile\FileView;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class DocumentFetcher
{
    private $connection;
    private $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator){
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    /**
     * @param string $profileId
     * @return array
     */
    public function getAll(string $profileId): array
    {
        $stmnt = $this->connection->createQueryBuilder()
            ->select('*')->from('profile_documents')
            ->where('profile_id = :profile_id', 'status != :deleted')
            ->setParameters([':profile_id' => $profileId, ':deleted' => FileStatus::deleted()->getName()])
            ->orderBy('created_at', 'DESC')
            ->execute();

        $stmnt->setFetchMode(FetchMode::CUSTOM_OBJECT, FileView::class);

        return $stmnt->fetchAll();
    }

    public function getAllPreview(string $profile_id): array {
        $stmt = $this->connection->createQueryBuilder()
            ->select('d.id', 'd.created_at', 'a.status', 'a.commentary')
            ->from('profile_documents', 'd')
            ->where('d.profile_id = :profile_id')
            ->setParameter(':profile_id', $profile_id)
            ->innerJoin('d', 'accreditation_requests', 'a', 'd.accreditation_id = a.id')
            ->orderBy('d.created_at', 'ASC')
            ->execute()->fetchAll(FetchMode::ASSOCIATIVE);

        return $stmt;
    }

    /**
     * @param string $id
     * @return DocumentView|null
     * @throws \Doctrine\DBAL\Exception
     * TODO - не работает
     */
    public function getForDownload(string $id): ? DocumentView {
        $stmt = $this->connection->createQueryBuilder()
            ->select('d.hash', 'd.sign', 'd.xml', 'd.created_at')
            ->from('profile_documents', 'd')
            ->where('d.id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DocumentView::class);

        return ($result = $stmt->fetch()) ? $result : null;
    }

    public function getDetail($id): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'profile_id',
                'file_type',
                'status',
                'client_ip',
                'created_at',
                'file_path',
                'file_name',
                'file_size',
                'file_real_name',
                'file_sign',
                'file_hash',
                'file_sign_at'
            )
            ->from('profile_documents')
            ->where('id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();
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
                'profile_id',
                'status',
                'created_at',
                'file_path',
                'file_name',
                'file_size',
                'file_real_name',
                'file_sign',
                'file_hash',
                'file_sign_at',
                'file_type'
            )->from('profile_documents')
            ->where('status != :status')
            ->setParameter(':status', \App\Model\User\Entity\Profile\Document\Status::deleted()->getName())
            ->orderBy('created_at', 'desc');

        if ($filter->profileId){
            $qb->andWhere('profile_id = :profile_id');
            $qb->setParameter(':profile_id', $filter->profileId);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

    /**
     * @param string $id
     * @return \App\ReadModel\Profile\FileView|null
     */
    public function getById(string $id): ? FileView
    {
        $stmnt = $this->connection->createQueryBuilder()
            ->select('*')->from('profile_documents')
            ->where('id = :id')->setParameter(':id', $id)->execute();

        $stmnt->setFetchMode(FetchMode::CUSTOM_OBJECT, FileView::class);
        $result = $stmnt->fetch();

        return $result ? $result : NULL;
    }
}