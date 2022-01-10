<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\XmlDocument;

use App\Model\Work\Procedure\Entity\XmlDocument\Status;
use App\ReadModel\Procedure\XmlDocument\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
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
            ->select('d.id',
                'd.status',
                'd.status_tactic',
                'd.created_at',
                'd.procedure_id',
                'd.type',
                'd.number',
                'd.moderator_id',
                'd.moderator_comment',
                'd.signed_at',
                'p.id_number'
            )
            ->from('procedure_xml_documents', 'd')
            ->innerJoin('d','procedures', 'p', 'd.procedure_id = p.id')
            ->orderBy('created_at', 'desc');

        if ($filter->procedureId){
            $qb->andWhere('d.procedure_id = :procedure_id');
            $qb->setParameter(':procedure_id', $filter->procedureId);
        }

        if ($filter->type){
            $qb->andWhere('d.type = :type');
            $qb->setParameter(':type', $filter->type);
        }

        if ($filter->status){
            $qb->andWhere('d.status = :status');
            $qb->setParameter(':status', $filter->status);
        }

        if ($filter->statusTactic){
            $qb->andWhere('d.status_tactic = :status_tactic');
            $qb->setParameter(':status_tactic', $filter->statusTactic);
        }

        if ($filter->moderator){
            $qb->andWhere('d.moderator_id = :moderator_id');
            $qb->setParameter(':moderator_id', $filter->moderator);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

    /**
     * @param string $xmlFileId
     * @return mixed
     */
    public function findDetailXmlFile(string $xmlFileId): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'file',
                'type',
                'number',
                'status',
                'procedure_id',
                'created_at',
                'signed_at',
                'status_tactic',
                'sign',
                'hash',
                'moderator_comment'
            )
            ->from('procedure_xml_documents')
            ->where('id = :id')
            ->setParameter(':id', $xmlFileId)
            ->execute();
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * @param string $procedureId
     * @return bool
     * @throws Exception
     */
    public function existsXmlDocumentByProcedureId(string $procedureId): bool{
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('procedure_xml_documents')
            ->where('procedure_id = :procedure_id')
            ->setParameter(':procedure_id', $procedureId)
            ->andWhere('status IN (:status)')
            ->setParameter(':status', [
               Status::approve()->getName(),
               Status::rejected()->getName(),
               Status::cancellingPublication()->getName(),
            ], Connection::PARAM_STR_ARRAY);


        return $qb->execute()->fetchColumn(0) > 0;
    }

    /**
     * @param string $procedureId
     * @return bool
     * @throws Exception
     */
    public function existsXmlDocumentApproveByProcedureId(string $procedureId): bool{
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('procedure_xml_documents')
            ->where('procedure_id = :procedure_id')
            ->setParameter(':procedure_id', $procedureId)
            ->andWhere('status = :status')
            ->setParameter(':status', Status::approve()->getName());

        return $qb->execute()->fetchColumn(0) > 0;
    }

    /**
     * @param string $procedureId
     * @return DetailView|null
     * @throws Exception
     */
    public function findLastNotificationByProcedureId(string $procedureId): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id, status, file')
            ->from('procedure_xml_documents')
            ->where('procedure_id = :procedure_id')
            ->setParameter(':procedure_id', $procedureId)
            ->orderBy('created_at', 'desc')
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }


/*    public function findApprovedNotification(string $procedureId){
        $stmt = $this->connection->createQueryBuilder()
            ->select('id, status, file')
            ->from('procedure_xml_documents')
            ->where('procedure_id = :procedure_id')
            ->setParameter(':procedure_id', $procedureId)
            ->andWhere('status = :status')
            ->setParameter(':status', Status::signed()->getName())
            ->orderBy('created_at', 'desc');
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetchAll();
        return $result ?: null;
    }*/


    public function findApprovedNotification(Filter $filter, int $page, int $size): PaginationInterface {
        $qb = $this->connection->createQueryBuilder()
            ->select('id, status, file, signed_at, number, type')
            ->from('procedure_xml_documents')
            ->where('procedure_id = :procedure_id')
            ->setParameter(':procedure_id', $filter->procedureId)
            ->andWhere('status = :status')
            ->setParameter(':status', Status::signed()->getName())
            ->orderBy('created_at', 'desc');


        return $this->paginator->paginate($qb, $page, $size);
    }



    public function findLastNumber(): int{
        $stmt = $this->connection->createQueryBuilder()
            ->select('number')
            ->from('procedure_xml_documents')
            ->orderBy('created_at', 'desc')
            ->execute();

        $stmt->setFetchMode(FetchMode::ASSOCIATIVE);
        $result = $stmt->fetch();

        return (int)$result['number'] ?: 0;
    }

}