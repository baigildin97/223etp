<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Lot;

use App\Model\Work\Procedure\Entity\Lot\Status;
use App\ReadModel\Procedure\Lot\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class LotFetcher
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
            ->select(
                'l.id',
                'l.id_number',
                'l.arrested_property_type',
                'l.lot_name',
                'l.nds',
                'l.tender_basic',
                'l.location_object',
                'l.date_enforcement_proceedings',
                'l.start_date_of_applications',
                'l.closing_date_of_applications',
                'l.summing_up_applications',
                'l.region',
                'l.location_object',
                'l.additional_object_characteristics',
                'l.starting_price',
                'l.procedure_id',
                'l.auction_id',
                'l.deposit_amount',
                'l.advance_payment_time',
                'l.bailiffs_name',
                'l.bailiffs_name_dative_case',
                'l.executive_production_number',
                'l.debtor_full_name_date_case',
                'l.pledgeer',
                'l.requisite',
                'a.auction_step',
                'p.id_number procedure_number',
                'a.start_trading_date'
            )->from('lots', 'l')
            ->innerJoin('l','procedures', 'p', 'l.procedure_id = p.id')
//            ->innerJoin('p','profile_organizations','o', 'o.id = p.organization_id')
            ->innerJoin('l', 'auctions', 'a', 'l.auction_id = a.id');
//            ->orderBy('created_at', 'desc');

        if ($filter->procedureId){
            $qb->andWhere('procedure_id = :procedure_id');
            $qb->setParameter(':procedure_id', $filter->procedureId);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

    public function getProcedureNumber(string $procedure_id)
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('p.id_number')
            ->from('procedures', 'p')
            ->where('p.id = :procedure_id')
            ->setParameter(':procedure_id', $procedure_id)
            ->execute();

        return $qb->fetchOne();
    }

    public function forXmlProtocolsView(Filter $filter, int $page, int $size): ? array {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'l.id',
                'l.id_number lot_number',
                'l.arrested_property_type',
                'l.pledgeer',
                'l.lot_name',
                'l.tender_basic',
                'l.bailiffs_name',
                'l.executive_production_number',
                'l.region',
                'l.reload_lot',
                'l.deposit_amount',
                'l.location_object',
                'l.debtor_full_name',
                'l.additional_object_characteristics',
                'l.starting_price',
                'l.procedure_id',
                'l.auction_id',
                'l.nds',
                'l.requisite',
                'l.bailiffs_name_dative_case',
                'l.date_enforcement_proceedings',
                'l.debtor_full_name_date_case',
                'l.closing_date_of_applications',
                'p.id_number procedure_number',
                'p.price_presentation_form bidding_form',
                'p.info_bidding_process bidding_process',
                'p.type type_procedure',
                'p.profile_id',
                'p.title',
                'o.full_title_organization',
                'o.legal_city',
                'a.start_trading_date',
                'a.winner_id',
                'a.final_cost'
            )
            ->from('lots', 'l')
            ->innerJoin('l', 'procedures', 'p', 'l.procedure_id = p.id')
            ->innerJoin('l', 'auctions', 'a', 'a.lot_id = l.id')
            ->innerJoin('p', 'profile_organizations','o','o.id = p.organization_id');
//            ->orderBy('created_at', 'desc');

        if ($filter->procedureId){
            $stmt->andWhere('l.procedure_id = :procedure_id');
            $stmt->setParameter(':procedure_id', $filter->procedureId);
        }

        $result = $stmt->execute()->fetch();

        return $result ?? null;
    }

    /**
     * @param string $lotId
     * @return DetailView|null
     */
    public function findDetail(string $lotId): ? DetailView{
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'l.id',
                'l.id_number',
                'l.arrested_property_type',
                'l.lot_name',
                'l.status',
                'l.region',
                'l.pledgeer',
                'l.location_object',
                'l.additional_object_characteristics',
                'l.starting_price',
                'l.bailiffs_name',
                'l.executive_production_number',
                'l.reload_lot',
                'l.tender_basic',
                'l.bailiffs_name_dative_case',
                'l.deposit_amount',
                'l.nds',
                'l.requisite',
                'l.payment_winner_confirm',
                'a.auction_step',
                'a.start_trading_date',
                'a.winner_id',
                'l.start_date_of_applications',
                'l.closing_date_of_applications',
                'l.summing_up_applications',
                'l.procedure_id',
                'l.auction_id',
                'l.debtor_full_name',
                'l.debtor_full_name_date_case',
                'l.date_enforcement_proceedings',
                'p.info_point_entry',
                'p.info_trading_venue',
                'p.info_bidding_process',
                'p.id_number procedure_number',
                'p.id procedure_id',
                'p.profile_id organizer_profile_id',
                'p.title title',
             /*   'r.payment_account',
                'r.bank_name',
                'r.bank_bik',
                'r.correspondent_account'*/
            )
            ->from('lots', 'l')
            ->where('l.id = :id')
            ->setParameter(':id', $lotId)
            ->innerJoin('l', 'procedures','p','p.id = l.procedure_id')
          //  ->innerJoin('l', 'profile_requisites','r','r.id = l.requisite_id')
            ->leftJoin('l', 'auctions','a','a.lot_id = l.id')

            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * @param string $procedureId
     * @return DetailView|null
     * @throws \Doctrine\DBAL\Exception
     */
    public function findDetailByProcedureId(string $procedureId): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'l.id',
                'l.id lot_id',
                'l.id_number',
                'l.arrested_property_type',
                'l.lot_name',
                'l.status',
                'l.region',
                'l.pledgeer',
                'l.location_object',
                'l.additional_object_characteristics',
                'l.starting_price',
                'l.bailiffs_name',
                'l.executive_production_number',
                'l.reload_lot',
                'l.tender_basic',
                'l.bailiffs_name_dative_case',
                'l.deposit_amount',
                'l.deposit_policy',
                'l.nds',
                'l.requisite',
                'l.payment_winner_confirm',
                'a.auction_step',
                'a.start_trading_date',
                'a.winner_id',
                'l.start_date_of_applications',
                'l.closing_date_of_applications',
                'l.summing_up_applications',
                'l.procedure_id',
                'l.advance_payment_time',
                'l.auction_id',
                'l.debtor_full_name',
                'l.debtor_full_name_date_case',
                'l.date_enforcement_proceedings',
                'p.info_point_entry',
                'p.info_trading_venue',
                'p.info_bidding_process',
                'p.tendering_process',
                'p.id_number procedure_number',
                'p.id procedure_id',
                'p.title title',
                'p.type',
                'p.price_presentation_form',
                'p.profile_id organizer_profile_id',

                'p.organizer_full_name',
                'p.organizer_email',
                'p.organizer_phone',


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
                'pr.web_site'
            )
            ->from('lots', 'l')
            ->where('l.procedure_id = :procedure_id')
            ->setParameter(':procedure_id', $procedureId)
            ->innerJoin('l', 'procedures','p','p.id = l.procedure_id')
            ->innerJoin('p', 'profile_organizations', 'o', 'p.organization_id = o.id')
            ->innerJoin('o','profile_profiles', 'pr', 'p.profile_id = pr.id')
            ->leftJoin('pr', 'auctions','a','a.lot_id = l.id')

            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function getAllInProcedure(string $procedure_id, int $size, int $page, string $orderBy = null, string $order = null): PaginationInterface
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('lots', 'l')
            ->where('l.procedure_id = :procedure_id')
            ->setParameter(':procedure_id', $procedure_id);

        if ($orderBy && $order)
            $qb->orderBy($orderBy, $order);
        else
            $qb->orderBy('l.start_date_of_applications', 'desc');

        return $this->paginator->paginate($qb, $page, $size);
    }

    /**
     * @param Filter $filter
     * @param Status $status
     * @return array|null
     */
    public function findByStatus(Filter $filter, Status $status): ? array {
        $stmt = $this->connection->createQueryBuilder()
            ->select("id, procedure_id")
            ->from("lots")
            ->where("status = :status")
            ->setParameter(":status", $status->getName());

        // Было все процедуры у которых дата начала подачи заявок меньше чем текущее время
            if($filter->startDateOfApplications){
                $stmt->andWhere("start_date_of_applications < :date_of_applications");
                $stmt->setParameter(":date_of_applications", $filter->startDateOfApplications->format("Y-m-d H:i:s"));
            }

            if($filter->applicationClosingDate){
                $stmt->andWhere("closing_date_of_applications < :date_of_applications");
                $stmt->setParameter(":date_of_applications", $filter->applicationClosingDate->format("Y-m-d H:i:s"));
            }

            if($filter->statusSummingUpApplications){
                $stmt->andWhere("summing_up_applications < :up_applications");
                $stmt->setParameter(":up_applications", $filter->statusSummingUpApplications->format("Y-m-d H:i:s"));
            }
            
        $result = $stmt->execute()->fetchAll();
        return $result ?: null;
    }

    /**
     * @param string $lotId
     * @param Status $status
     * @return bool
     */
    public function existsLotByStatus(string $lotId, Status $status): bool{
        return $this->connection->createQueryBuilder()
                ->select('COUNT (*)')
                ->from('lots')
                ->where('id = :id')
                ->setParameter(':id', $lotId)
                ->andWhere('status = :status')
                ->setParameter(':status', $status->getName())
                ->execute()
                ->fetchColumn(0) > 0;
    }

    /**
     * @return int|null
     * @throws \Doctrine\DBAL\Exception
     */
    public function findLastIdNumber():? int{
        $stmt = $this->connection->createQueryBuilder()
            ->select('id_number')
            ->from('lots')
            ->orderBy('created_at', 'desc')
            ->execute();

        $stmt->setFetchMode(FetchMode::ASSOCIATIVE);
        $result = $stmt->fetch();

        return $result['id_number'] ?: 0;
    }
}