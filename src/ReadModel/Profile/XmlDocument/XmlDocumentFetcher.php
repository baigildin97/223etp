<?php
declare(strict_types=1);

namespace App\ReadModel\Profile\XmlDocument;
use App\Model\User\Entity\Profile\XmlDocument\Status;
use App\Model\User\Entity\Profile\XmlDocument\StatusTactic;
use App\Model\User\Entity\Profile\XmlDocument\TypeStatement;
use App\ReadModel\Profile\XmlDocument\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class XmlDocumentFetcher
{
    private $connection;
    private $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator){
        $this->connection = $connection;
        $this->paginator = $paginator;
    }


    public function find(string $id): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'xml.id',
                'xml.profile_id',
                'xml.id_number',
                'xml.status',
                'xml.status_tactic',
                'xml.created_at',
                'xml.file',
                'xml.hash',
                'xml.sign',
                'xml.moderator_comment',
                'xml.type',
                'c.subject_name_owner certificate_owner',
                'c.subject_name_user_name user_name',
                'c.thumbprint certificate_thumbprint',
                'p.incorporated_form user_incorporated_form',
                'o.full_title_organization organization_full_title',
                're.position user_position',
                're.confirming_document confirming_document',
                're.passport_series passport_series',
                're.passport_number passport_number',
                're.passport_issuer passport_issuer',
                're.passport_issuance_date passport_issuance_date'
            )
            ->from('profile_xml_documents', 'xml')
            ->where('xml.id = :id')
            ->setParameter(':id', $id)
            ->innerJoin('xml', 'profile_profiles', 'p','xml.profile_id = p.id')
            ->innerJoin('p','certificates', 'c','p.certificate_id = c.id')
            ->leftJoin('p', 'profile_organizations', 'o', 'p.organization_id = o.id')
            ->innerJoin('p','profile_representatives','re','p.representative_id = re.id')
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * @param string $xmlFileId
     * @return DetailView|null
     */
    public function findDetailXmlFile(string $xmlFileId): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'id_number', 'file', 'status', 'status_tactic', 'profile_id', 'created_at', 'type', 'moderator_comment')
            ->from('profile_xml_documents')
            ->where('id = :id')
            ->setParameter(':id', $xmlFileId)
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
    public function all(Filter $filter, int $page, int $size, string $order = null): PaginationInterface {
        $qb = $this->connection->createQueryBuilder()
            ->select(
               'x.id',
                'x.id_number',
                'x.status',
                'x.profile_id',
                'x.status_tactic',
                'x.created_at',
                'x.moderator_comment',
                'x.type',
                'p.incorporated_form',
                'p.client_ip',
                're.phone',
                're.position',
                'c.subject_name_inn',
                'c.subject_name_owner',
                'u.email',
                'ro.name role_name',
                'o.short_title_organization full_title_organization',
                'TRIM(CONCAT(re.passport_last_name, \' \', re.passport_first_name, \' \', re.passport_middle_name)) AS user_name'
            )
            ->from('profile_xml_documents', 'x')
            ->innerJoin('x', 'profile_profiles', 'p', 'x.profile_id = p.id')
            ->innerJoin('x', 'profile_representatives', 're', 'p.representative_id = re.id')
            ->innerJoin('p', 'roles', 'ro', 'p.role_id = ro.id')
            ->innerJoin('p', 'user_users', 'u', 'p.user_id = u.id')
	        ->innerJoin('p', 'certificates', 'c', 'p.certificate_id = c.id')
	        ->leftJoin('p', 'profile_organizations', 'o', 'p.organization_id = o.id');

            if (in_array($order, ['desc', 'asc']))
                $qb->orderBy('created_at', $order);
            else
                $qb->orderBy('created_at', 'desc');

        if ($filter->profileId){
            $qb->andWhere('x.profile_id = :profile_id');
            $qb->setParameter(':profile_id', $filter->profileId);
        }

        if ($filter->statusTactic){
            $qb->andWhere('x.status_tactic = :status_tactic');
            $qb->setParameter(':status_tactic', $filter->statusTactic);
        }

        if (!empty($filter->type)){
            $qb->andWhere('x.type IN (:type)');
            $qb->setParameter(':type', $filter->type, Connection::PARAM_STR_ARRAY);
        }

        if ($filter->userName)
        {
            $qb->andWhere($qb->expr()->like('LOWER(TRIM(CONCAT(re.passport_last_name, \' \', re.passport_first_name, \' \', re.passport_middle_name)))', ':user_name'))
                ->orWhere($qb->expr()->like('LOWER(full_title_organization)', ':user_name'))
                ->setParameter(':user_name', '%' . strtolower($filter->userName) . '%');
        }

        if ($filter->subjectNameInn)
        {
            $qb->andWhere($qb->expr()->like('c.subject_name_inn', ':subject_name_inn'));
            $qb->setParameter(':subject_name_inn', '%' . $filter->subjectNameInn . '%');
        }

        if ($filter->email)
        {
            $qb->andWhere($qb->expr()->like('LOWER(u.email)', ':email'));
            $qb->setParameter(':email', '%' . strtolower($filter->email) . '%');
        }

        if ($filter->phone)
        {
            $qb->andWhere($qb->expr()->like('re.phone', ':phone'));
            $qb->setParameter(':phone', '%' . $filter->phone . '%');
        }

        if ($filter->clientIp)
        {
            $qb->andWhere($qb->expr()->like('p.client_ip', ':client_ip'));
            $qb->setParameter(':client_ip', '%' . $filter->clientIp . '%');
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

    /**
     * @param string $profile_id
     * @return bool
     * TODO - нужно проверять что есть модерированные заявления а не все
     */
    public function existsXmlDocumentByProfileId(string $profile_id): bool {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('profile_xml_documents')
            ->where('profile_id = :profile_id')
            ->andWhere('status = :status')
            ->setParameter(':status', Status::approve()->getName())
            ->setParameter(':profile_id', $profile_id);
        return $qb->execute()->fetchColumn(0) > 0;
    }

    public function existsXmlDocumentByProfileModerateProcessing(string $profile_id): bool{
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('profile_xml_documents')
            ->where('profile_id = :profile_id')
            ->andWhere('status_tactic IN (:status_tactic)')
            ->andWhere('type != :type')
            ->setParameter(':type', TypeStatement::recall()->getName())
            ->setParameter(':status_tactic', [
                StatusTactic::published()->getName(),
                StatusTactic::processing()->getName()
            ], Connection::PARAM_STR_ARRAY)
            ->setParameter(':profile_id', $profile_id);
        return $qb->execute()->fetchColumn(0) > 0;
    }


    public function findLastIdNumber():? int{
        $stmt = $this->connection->createQueryBuilder()
            ->select('id_number')
            ->from('profile_xml_documents')
            ->orderBy('created_at', 'desc')
            ->execute();

        $stmt->setFetchMode(FetchMode::ASSOCIATIVE);
        $result = $stmt->fetch();

        return $result['id_number'] ?: 0;
    }

    /**
     * @param string $profile_id
     * @return bool
     * @throws Exception
     */
    public function issetStatementForRegistration(string $profile_id): bool {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('profile_xml_documents')
            ->where('profile_id = :profile_id')
            ->andWhere('status = :status')
            ->andWhere('type = :type')
            ->setParameter(':type', TypeStatement::registration()->getName())
            ->setParameter(':status', \App\Model\User\Entity\Profile\XmlDocument\Status::approve()->getName())
            ->setParameter(':profile_id', $profile_id);
        return $qb->execute()->fetchColumn(0) > 0;
    }

    public function issetDocuments(string $profile_id): bool {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('profile_xml_documents')
            ->where('profile_id = :profile_id')
            ->setParameter(':profile_id', $profile_id);
        return $qb->execute()->fetchColumn(0) > 0;
    }

    /**
     * @param string|null $profile_id
     * @return int|null
     * @throws Exception
     */
    public function countXmlDocuments(?string $profile_id): ?int {
        $statusArr = [Status::approve()->getName(), Status::rejected()->getName()];

        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('profile_xml_documents')
            ->where('status IN (:status)')
            ->setParameter('status', $statusArr, \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->andWhere('profile_id = :profile_id')
            ->setParameter(':profile_id', $profile_id);
            

        return $qb->execute()->fetchColumn(0) ?: 0;
    }

    /**
     * @param string|null $profile_id
     * @return DetailView|null
     * @throws Exception
     */
    public function dateApplicationSubmission(?string $profile_id): ? DetailView{
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'created_at')
            ->from('profile_xml_documents')
            ->orderBy('created_at', 'desc')
            ->where('profile_id = :profile_id')
            ->setParameter(':profile_id', $profile_id)
            ->andWhere('status = :status')
            ->setParameter(':status', Status::approve()->getName())
            ->execute();
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
