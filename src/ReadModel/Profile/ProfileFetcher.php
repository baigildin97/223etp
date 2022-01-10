<?php
declare(strict_types=1);

namespace App\ReadModel\Profile;


use App\Model\User\Entity\Profile\Status;
use App\Model\User\Entity\Profile\Requisite\Status as RequisiteStatus;
use App\Model\User\Entity\Profile\XmlDocument\StatusTactic;
use App\Model\User\Entity\Profile\XmlDocument\TypeStatement;
use App\ReadModel\NotFoundException;
use App\ReadModel\Profile\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class ProfileFetcher
 * @package App\ReadModel\Profile
 */
class ProfileFetcher
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
     * ProfileFetcher constructor.
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
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'p.id',
                'p.role_id',
                'p.user_id',
                'p.incorporated_form',
                'p.client_ip',
                'p.organization_id',
                'p.representative_id',
                're.phone phone',
                'u.email',
                'r.name role_name',
                'p.created_at',
                'o.full_title_organization',
                'c.subject_name_inn',
                'TRIM(CONCAT(re.passport_last_name, \' \', re.passport_first_name, \' \', re.passport_middle_name)) AS user_name'
            )
            ->from('profile_profiles', 'p')
            ->leftJoin('p', 'profile_organizations', 'o', 'p.organization_id = o.id')
            ->innerJoin('p', 'profile_representatives', 're', 'p.representative_id = re.id')
            ->innerJoin('p', 'roles', 'r', 'p.role_id = r.id')
            ->innerJoin('p', 'certificates', 'c', 'p.certificate_id = c.id')
            ->innerJoin('p', 'user_users', 'u', 'p.user_id = u.id')
            ->orderBy('created_at', 'desc');

        if ($filter->status) {
            $qb->andWhere($qb->expr()->like('p.status', ':status'));
            $qb->setParameter(':status', $filter->status);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

    /**
     * @param string $userId
     * @return bool
     * @throws Exception
     */
    public function existsByUser(string $userId): bool
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('profile_profiles')
            ->where('user_id = :user_id')
            ->setParameter(':user_id', $userId);
        return $qb->execute()->fetchColumn(0) > 0;
    }

    /**
     * @param string $userId
     * @return bool
     * @throws Exception
     */
    public function existsActiveProfileByUser(string $userId): bool
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('profile_profiles')
            ->where('user_id = :user_id')
            ->andWhere('status = :status')
            ->setParameter(':status', Status::active()->getName())
            ->setParameter(':user_id', $userId);
        return $qb->execute()->fetchColumn(0) > 0;
    }

    /**
     * @param string $profileId
     * @return bool
     * @throws Exception
     */
    public function existsNotSignDocument(string $profileId): bool {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('profile_documents')
            ->where('profile_id = :profile_id')
            ->andWhere('status = :status')
            ->setParameter(':status', \App\Model\User\Entity\Profile\Document\Status::new()->getName())
            ->setParameter(':profile_id', $profileId);
        return $qb->execute()->fetchColumn(0) > 0;
    }

    /**
     * @param string $userId
     * @return false|mixed
     * @throws Exception
     */
    public function getCertificateThumbprint(string $userId)
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('c.thumbprint certificate_thumbprint')
            ->from('profile_profiles', 'p')
            ->where('p.user_id = :user_id')
            ->setParameter(':user_id', $userId)
            ->innerJoin('p', 'certificates', 'c', 'c.id = p.certificate_id');
        return $qb->execute()->fetchColumn(0);
    }

    /**
     * @param string $id
     * @param bool $fetchRequisites
     * @return DetailView|null
     * @throws Exception
     */
    public function findDetailByUserId(string $id, bool $fetchRequisites = true): ?DetailView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('t.id',
                't.status',
                't.created_at',
                't.payment_id',
                't.subscribe_tariff_id',
                'r.role_constant role_constant',
                'r.permissions role_permissions',
                'r.name role_name',
                't.incorporated_form',
                're.position',
                're.confirming_document',
                're.phone',
                're.passport_number',
                're.passport_snils',
                're.passport_series',
                're.passport_issuer',
                're.passport_issuance_date',
                're.passport_citizenship',
                're.passport_unit_code',
                're.passport_birth_day',
                're.passport_first_name repr_passport_first_name',
                're.passport_middle_name repr_passport_middle_name',
                're.passport_last_name repr_passport_last_name',
                're.passport_fact_country',
                're.passport_fact_region',
                're.passport_fact_city',
                're.passport_fact_index',
                're.passport_fact_street',
                're.passport_fact_house',
                're.passport_legal_country',
                're.passport_legal_region',
                're.passport_legal_city',
                're.passport_legal_index',
                're.passport_legal_street',
                're.passport_legal_house',
                'o.full_title_organization',
                'o.short_title_organization',
                'o.kpp',
                'o.ogrn',
                'o.email org_email',
                'o.inn',
                'o.fact_country fact_country',
                'o.fact_region fact_region',
                'o.fact_city fact_city',
                'o.fact_index fact_index',
                'o.fact_street fact_street',
                'o.fact_house fact_house',
                'o.legal_country legal_country',
                'o.legal_region legal_region',
                'o.legal_city legal_city',
                'o.legal_index legal_index',
                'o.legal_street legal_street',
                'o.legal_house legal_house',
                'c.thumbprint certificate_thumbprint',
                'c.id certificate_id',
                'c.subject_name_owner certificate_owner',
                'c.valid_from certificate_valid_from',
                'c.valid_to certificate_valid_to',
                'c.subject_name_inn certificate_subject_name_inn',
                'u.email user_email',
                'u.id user_id',
                'TRIM(CONCAT(re.passport_last_name, \' \', re.passport_first_name, \' \', re.passport_middle_name)) AS user_name'
            )
            ->from('profile_profiles', 't')
            ->where('t.user_id = :id')
            ->setParameter(':id', $id)
            ->innerJoin('t', 'roles', 'r', 't.role_id = r.id')
            ->innerJoin('t', 'certificates', 'c', 't.certificate_id = c.id')
            ->innerJoin('t', 'user_users', 'u', 't.user_id = u.id')
            ->leftJoin('t', 'profile_organizations', 'o', 't.organization_id = o.id')
            ->innerJoin('t', 'profile_representatives', 're', 't.representative_id = re.id')
            ->execute();


        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * @param string $id
     * @param bool $fetchRequisites
     * @return DetailView|null
     * @throws Exception
     */
    public function find(string $id, bool $fetchRequisites = true): ?DetailView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('t.id',
                't.status',
                't.created_at',
                't.web_site',
                't.contract_period',
                'r.role_constant role_constant',
                'r.permissions role_permissions',
                'r.name role_name',
                't.incorporated_form',
                're.position',
                're.confirming_document',
                're.phone',
                're.passport_number',
                're.passport_series',
                're.passport_issuer',
                're.passport_issuance_date',
                're.passport_citizenship',
                're.passport_unit_code',
                're.passport_birth_day',
                're.passport_first_name repr_passport_first_name',
                're.passport_middle_name repr_passport_middle_name',
                're.passport_last_name repr_passport_last_name',
                're.passport_fact_country',
                're.passport_fact_region',
                're.passport_fact_city',
                're.passport_fact_index',
                're.passport_fact_street',
                're.passport_fact_house',
                're.passport_fact_country',
                're.passport_legal_country',
                're.passport_legal_region',
                're.passport_legal_city',
                're.passport_legal_index',
                're.passport_legal_street',
                're.passport_legal_house',
                're.passport_inn',
                're.passport_inn repr_passport_inn',
                're.passport_snils',
                'o.full_title_organization',
                'o.short_title_organization',
                'o.kpp',
                'o.ogrn',
                'o.email org_email',
                'o.inn',
                'o.fact_country',
                'o.fact_region',
                'o.fact_city',
                'o.fact_index',
                'o.fact_street',
                'o.fact_house',
                'o.fact_country',
                'o.legal_country',
                'o.legal_region',
                'o.legal_city',
                'o.legal_index',
                'o.legal_street',
                'o.legal_house',
                'c.thumbprint certificate_thumbprint',
                'c.id certificate_id',
                'c.subject_name_owner certificate_owner',
                'c.valid_from certificate_valid_from',
                'c.valid_to certificate_valid_to',
                'c.subject_name_inn certificate_subject_name_inn',
                'c.subject_name_snils certificate_subject_name_snils',
                'u.email user_email',
                'u.id user_id',
                'TRIM(CONCAT(re.passport_first_name, \' \', re.passport_middle_name, \' \', re.passport_last_name)) AS user_name'
            )
            ->from('profile_profiles', 't')
        ->where('t.id = :id')
        ->setParameter(':id', $id)
        ->innerJoin('t', 'roles', 'r', 't.role_id = r.id')
        ->innerJoin('t', 'certificates', 'c', 't.certificate_id = c.id')
        ->innerJoin('t', 'user_users', 'u', 't.user_id = u.id')
        ->leftJoin('t', 'profile_organizations', 'o', 't.organization_id = o.id')
        ->innerJoin('t', 'profile_representatives', 're', 't.representative_id = re.id')
        ->execute();


        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * @param string $id
     * @param string|null $requisite_id
     * @return mixed|mixed[]
     * @throws Exception
     */
    public function getRequisites(string $id, string $requisite_id = null)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('r.id', 'r.bank_bik', 'r.bank_name', 'r.correspondent_account', 'r.payment_account')
            ->from('profile_requisites', 'r')
            ->where('r.profile_id = :profile_id')
            ->setParameter(':profile_id', $id);

        if (!is_null($requisite_id))
            return $stmt->andWhere('r.id = :requisite_id')
                ->setParameter(':requisite_id', $requisite_id)
                ->execute()->fetch();
        else
            return $stmt->andWhere('r.status = :status')
                ->setParameter(':status', RequisiteStatus::active()->getName())
                ->execute()->fetchAll();
    }

    /**
     * @param string $id
     * @return DetailView|null
     * @throws Exception
     */
    public function findShortDetailByUserId(string $id): ?DetailView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('profile_profiles')
            ->where('user_id = :user_id')
            ->setParameter(':user_id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * @param string $profileId
     * @return array|null
     * @throws Exception
     */
    public function findPermissions(string $profileId): ?array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id', 'name', 'role_constant', 'permissions')
            ->from('profile_profiles')
            ->where('user_id = :user_id')
            ->setParameter(':user_id', $profileId)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * @return int|null
     * @throws Exception
     */
    public function countModerateProfile(): ? int
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('profile_xml_documents')
            ->where('status = :status')
            ->setParameter(':status', \App\Model\User\Entity\Profile\XmlDocument\Status::signed()->getName())
            ->andWhere('status_tactic = :status_tactic')
            ->setParameter(':status_tactic', StatusTactic::published()->getName())
            ->andWhere('type = :type')
            ->setParameter(':type', TypeStatement::registration()->getName());
            return $qb->execute()->fetchColumn(0);
    }

    /**
     * @param string $moderatorId
     * @return int|null
     * @throws Exception
     */
    public function countModerateProfileProcessing(string $moderatorId): ?int
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('profile_xml_documents')

            ->where('status = :status')
            ->setParameter(':status', \App\Model\User\Entity\Profile\XmlDocument\Status::signed()->getName())

            ->andWhere('status_tactic = :status_tactic')
            ->setParameter(':status_tactic', StatusTactic::processing()->getName())

            ->andWhere('moderator_id = :moderator_id')
            ->setParameter(':moderator_id', $moderatorId);
        return $qb->execute()->fetchColumn(0);
    }

    /**
     * @return DetailView|null
     * @throws Exception
     */
    public function findSignedProfiles(): ?array{
        $stmt = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('profile_profiles')
            ->where('contract_period IS NOT NULL')
            ->execute();

       // $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetchAll();

        return $result ?: null;
    }
}
