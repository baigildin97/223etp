<?php
declare(strict_types=1);

namespace App\ReadModel\Procedure;

use App\Model\User\Entity\Profile\XmlDocument\StatusTactic;
use App\Model\Work\Procedure\Entity\Document\Status;
use App\ReadModel\Procedure\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Model\Work\Procedure\Entity\Status as ProcedureStatus;

class ProcedureFetcher
{
    private $connection;
    private $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function all(Filter $filter, int $page, int $size): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'p.id',
                'p.id_number',
                'p.status',
                'p.title',
                'l.starting_price',
                'l.start_date_of_applications',
                'l.closing_date_of_applications',
                'l.region',
                'o.full_title_organization',
                'o.short_title_organization',
                'a.start_trading_date'
            )->from('procedures', 'p')
            ->innerJoin('p', 'profile_organizations', 'o', 'p.organization_id = o.id')
            ->innerJoin('p', 'lots', 'l', 'p.id = l.procedure_id')
            ->innerJoin('l', 'auctions', 'a', 'l.id = a.lot_id')
            ->orderBy('p.created_at', 'desc');

        if ($filter->id_number) {
            $qb->andWhere($qb->expr()->like('LOWER(p.id_number)', ':id_number'));
            $qb->setParameter(':id_number', '%' . mb_strtolower($filter->id_number) . '%');
        }

        if ($filter->title) {
            $qb->andWhere($qb->expr()->like('LOWER(p.title)', ':title'));
            $qb->setParameter(':title', '%' . mb_strtolower($filter->title) . '%');
        }

        if ($filter->profile_id) {
            $qb->andWhere('p.profile_id = :profile_id');
            $qb->setParameter(':profile_id', $filter->profile_id);
        }

        if ($filter->status) {
            $qb->andWhere('p.status NOT IN (:status)');
            $qb->setParameter(':status', $filter->status, Connection::PARAM_STR_ARRAY);
        }

        if ($filter->statusFilter) {
            $qb->andWhere('p.status = :statusFilter');
            $qb->setParameter(':statusFilter', $filter->statusFilter);
        }

        if ($filter->nameOrgInn) {
            if ((int)$filter->nameOrgInn) {
                $qb->andWhere($qb->expr()->like('LOWER(o.inn)', ':inn'));
                $qb->setParameter(':inn', '%' . mb_strtolower($filter->nameOrgInn) . '%');
            } else {
                $qb->andWhere($qb->expr()->like('LOWER(o.full_title_organization)', ':full_title_organization'));
                $qb->setParameter(':full_title_organization', '%' . mb_strtolower($filter->nameOrgInn) . '%');
            }
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

    public function allOld(int $page, int $size): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'id_number',
                'organizer',
                'organizer_short_name short_title_organization',
                'start_date_of_applications',
                'closing_date_of_applications',
                'start_trading_date',
                'status',
                'start_cost starting_price',
                'lot_name title',
                'region',
                'price_presentation_form'
            )->from('old_records.procedures')->orderBy('start_trading_date', 'desc');;

        return $this->paginator->paginate($qb, $page, $size);
    }

    public function getAllProcedures(Filter $filter, int $page, int $size): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'p.id',
                'p.id_number',
                'p.status',
                'p.title',
                'l.starting_price',
                'l.start_date_of_applications',
                'l.closing_date_of_applications',
                'l.region',
                'o.full_title_organization',
                'o.short_title_organization',
                'a.start_trading_date'
            )->from('procedures', 'p')
            ->innerJoin('p', 'profile_organizations', 'o', 'p.organization_id = o.id')
            ->innerJoin('p', 'lots', 'l', 'p.id = l.procedure_id')
            ->innerJoin('l', 'auctions', 'a', 'l.id = a.lot_id')
            ->orderBy('p.created_at', 'desc');

        if ($filter->id_number) {
            $qb->andWhere($qb->expr()->like('LOWER(p.id_number)', ':id_number'));
            $qb->setParameter(':id_number', '%' . mb_strtolower($filter->id_number) . '%');
        }

        if ($filter->title) {
            $qb->andWhere($qb->expr()->like('LOWER(p.title)', ':title'));
            $qb->setParameter(':title', '%' . mb_strtolower($filter->title) . '%');
        }

        if ($filter->profile_id) {
            $qb->andWhere('p.profile_id = :profile_id');
            $qb->setParameter(':profile_id', $filter->profile_id);
        }

        if ($filter->status) {
            $qb->andWhere('p.status NOT IN (:status)');
            $qb->setParameter(':status', $filter->status, Connection::PARAM_STR_ARRAY);
        }

        if ($filter->statusFilter) {
            $qb->andWhere('p.status = :statusFilter');
            $qb->setParameter(':statusFilter', $filter->statusFilter);
        }

        if ($filter->nameOrgInn) {
            if ((int)$filter->nameOrgInn) {
                $qb->andWhere($qb->expr()->like('LOWER(o.inn)', ':inn'));
                $qb->setParameter(':inn', '%' . mb_strtolower($filter->nameOrgInn) . '%');
            } else {
                $qb->andWhere($qb->expr()->like('LOWER(o.full_title_organization)', ':full_title_organization'));
                $qb->setParameter(':full_title_organization', '%' . mb_strtolower($filter->nameOrgInn) . '%');
            }
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

    public function findDetail(string $id): ?DetailView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'p.id',
                'p.id_number',
                'p.type',
                'p.profile_id',
                'p.price_presentation_form',
                'p.status',
                'p.info_point_entry',
                'p.info_trading_venue',
                'p.info_bidding_process',
                'p.tendering_process',
                'p.created_at',
                'p.title',
                'p.organizer_full_name',
                'p.organizer_email',
                'p.organizer_phone',
                'o.inn organization_inn',
                're.passport_snils organization_snils',
                'o.legal_country organization_legal_country',
                'o.legal_region organization_legal_region',
                'o.legal_city organization_legal_city',
                'o.legal_index organization_legal_index',
                'o.legal_street organization_legal_street',
                'o.fact_country organization_fact_country',
                'o.fact_region organization_fact_region',
                'o.fact_city organization_fact_city',
                'o.fact_index organization_fact_index',
                'o.fact_street organization_fact_street',
                'o.fact_house organization_fact_house',
                'o.short_title_organization organization_short_title',
                'o.full_title_organization organization_full_title',
                'o.kpp organization_kpp',
                'o.ogrn organization_ogrn',
                'o.email organization_email',
                're.passport_first_name representative_first_name',
                're.passport_middle_name representative_middle_name',
                're.passport_last_name representative_last_name',
                're.phone representative_phone',
                'cert.thumbprint certificate_thumbprint',
                'profile.incorporated_form',
                'profile.web_site'
            )
            ->from('procedures', 'p')
            ->where('p.id = :id')
            ->setParameter(':id', $id)
            ->innerJoin('p', 'profile_organizations', 'o', 'p.organization_id = o.id')
            ->innerJoin('p', 'profile_representatives', 're', 'p.representative_id = re.id')
            ->innerJoin('p', 'profile_profiles', 'profile', 'p.profile_id = profile.id')
            ->innerJoin('profile', 'certificates', 'cert', 'profile.certificate_id = cert.id')
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function getLots(string $procedureId)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id',
                'id_number',
                'lot_name',
                'status',
                'arrested_property_type',
                'starting_price',
                'reload_lot',
                'tender_basic',
                //  'offer_auction_time',
                'nds',
                //   'auction_step',
                'start_date_of_applications',
                'closing_date_of_applications',
                'summing_up_applications',
                //   'start_trading_date',
                'debtor_full_name',
                'debtor_full_name_date_case',
                //  'advance_payment_time',
                'region',
                'location_object',
                'additional_object_characteristics',
                //    'deposit_amount',
                'bailiffs_name',
                'executive_production_number'
            )
            ->from('lots')
            ->where('procedure_id = :procedure_id')
            ->setParameter(':procedure_id', $procedureId)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetchAll();

        return $result ?: null;
    }

    public function findDetailLot(string $procedureId, string $lotId)
    {

        $stmt = $this->connection->createQueryBuilder()
            ->select('id',
                'id_number',
                'arrested_property_type',
                'lot_name',
                'region',
                'location_object',
                'additional_object_characteristics',
                'starting_price'
            )
            ->from('lots')
            ->where('id = :id', 'procedure_id = :procedure_id')
            ->setParameters([':id' => $lotId, ':procedure_id' => $procedureId])
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function existsByTypeFile(string $fileType, string $procedureId): bool
    {
        return $this->connection->createQueryBuilder()
                ->select('COUNT (*)')
                ->from('procedure_documents')
                ->where('procedure_id = :procedure_id', 'file_type = :file_type', 'status != :status')
                ->setParameters([':procedure_id' => $procedureId, ':file_type' => $fileType, ':status' => Status::deleted()->getName()])
                ->execute()
                ->fetchColumn(0) > 0;
    }


    public function findIdNumberByProcedure(string $procedureId): ?DetailView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id_number', 'id')
            ->from('procedures')
            ->where('id = :id')
            ->setParameter(':id', $procedureId)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function findIdLotsByProcedure(string $procedureId)
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id')
            ->from('lots')
            ->where('procedure_id = :procedure_id')
            ->setParameter(':procedure_id', $procedureId)
            ->execute();

        $stmt->setFetchMode(FetchMode::ASSOCIATIVE);
        $result = $stmt->fetchAll();

        return $result ?: null;
    }

    public function countModerateProcedures(): ?int
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('procedure_xml_documents')
            ->where('status = :status')
            ->setParameter(':status', \App\Model\Work\Procedure\Entity\XmlDocument\Status::moderate()->getName())
            ->andWhere('status_tactic = :status_tactic')
            ->setParameter(':status_tactic', StatusTactic::published()->getName());
        return $qb->execute()->fetchColumn(0);
    }

    public function findLastIdNumber(): ?int
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select('id_number')
            ->from('procedures')
            ->orderBy('created_at', 'desc')
            ->execute();

        $stmt->setFetchMode(FetchMode::ASSOCIATIVE);
        $result = $stmt->fetch();

        return $result['id_number'] ?: 0;
    }

    public function countModerateProceduresProcessing(string $userId): ?int
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('procedure_xml_documents')
            ->where('status = :status')
            ->setParameter(':status', \App\Model\Work\Procedure\Entity\XmlDocument\Status::moderate()->getName())
            ->andWhere('status_tactic = :status_tactic')
            ->setParameter(':status_tactic', StatusTactic::processing()->getName())
            ->andWhere('moderator_id = :moderator_id')
            ->setParameter(':moderator_id', $userId);

        return $qb->execute()->fetchColumn(0);
    }

    public function findDetailOld(string $procedureId): ?OldDetailView
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'id_number',
                'tender_basic',
                'price_presentation_form',
                'organizer',
                'start_date_of_applications',
                'closing_date_of_applications',
                'start_trading_date',
                'status',
                'reload_lot',
                'auction_step',
                'start_cost',
                'nds',
                'deposit_amount',
                'arrested_property_type',
                'additional_object_characteristics',
                'region',
                'location_object',
                'organizer_short_name',
                'organizer_full_name',
                'organizer_contact_full_name',
                'organizer_phone',
                'organizer_email',
                'organizer_legal_address',
                'debtor_full_name',
                'procedure_info_applications',
                'procedure_info_place',
                'procedure_info_location',
                'lot_name',
                'text_notification'
            )
            ->from('old_records.procedures')
            ->where('id = :id')
            ->setParameter(':id', $procedureId)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, OldDetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

}
