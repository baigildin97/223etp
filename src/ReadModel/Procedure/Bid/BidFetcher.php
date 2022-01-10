<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Bid;


use App\Model\Work\Procedure\Entity\Lot\Bid\Status;
use App\ReadModel\Procedure\Bid\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class BidFetcher
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

    public function existsActiveBidForLot(string $participantId, string $lotId): bool {
        return $this->connection->createQueryBuilder()
                ->select('COUNT (*)')
                ->from('bids')
                ->where('status IN (:status)')
                ->setParameter(':status', [
                    Status::sent()->getName(),
                    Status::approved()->getName(),
                    Status::reject()->getName()
                ], Connection::PARAM_STR_ARRAY)
                ->andWhere('participant_id = :participant_id and lot_id = :lot_id')
                ->setParameter(':participant_id', $participantId)
                ->setParameter(':lot_id', $lotId)
                ->execute()
                ->fetchColumn(0) > 0;
    }


    /**
     * @param string $profileId
     * @return array|null
     * @throws Exception
     *
     */
    public function findAllWinningBidsByUser(string $profileId): ? array {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'number',
                'participant_id',
                'lot_id',
                'place'
            )->from('bids')
            ->where('place = :place')
            ->setParameter(':place', 1)
            ->andWhere('participant_id = :participant_id')
            ->setParameter('participant_id', $profileId)
            ->execute();

        $result = $qb->fetchAll();
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
                'b.id',
                'b.number',
                'b.participant_id',
                'b.lot_id',
                'b.status',
                'b.place',
                'b.participant_ip',
                'b.organizer_ip',
                'b.organizer_ip_created_at',
                'b.created_at',
                'l.id_number lot_number',
                'p.id_number procedure_number',
                'p.id procedure_id',
                'profile.user_id',
                'profile.incorporated_form',
                'TRIM(CONCAT(pr.passport_first_name, \' \', pr.passport_middle_name, \' \', pr.passport_last_name)) AS participant_full_name'
            )->from('bids', 'b')
            ->innerJoin('b', 'profile_profiles', 'profile', 'b.participant_id = profile.id')
            ->innerJoin('profile','profile_representatives','pr','profile.representative_id = pr.id')
            ->innerJoin('b', 'lots','l','b.lot_id = l.id')
            ->innerJoin('l', 'procedures','p','p.id = l.procedure_id')
            ->innerJoin('profile', 'user_users','u','u.id = profile.user_id')
            ->orderBy('created_at', 'desc');

        if ($filter->participantId){
            $qb->andWhere('b.participant_id = :participant_id');
            $qb->setParameter(':participant_id', $filter->participantId);
        }

        if ($filter->statuses){
            $qb->andWhere('b.status IN (:statuses)');
            $qb->setParameter(':statuses', $filter->statuses,Connection::PARAM_STR_ARRAY);
        }

        if ($filter->email){
            $qb->andWhere('u.email = :email');
            $qb->setParameter(':email', $filter->email);
        }

        if ($filter->status){
            $qb->andWhere('b.status = :status');
            $qb->setParameter(':status', $filter->status);
        }


        if ($filter->lotId){
            $qb->andWhere('b.lot_id = :lot_id');
            $qb->setParameter(':lot_id', $filter->lotId);
        }

        if ($filter->lotNumber){
            $qb->andWhere('l.id_number = :id_number');
            $qb->setParameter(':id_number', $filter->lotNumber);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

/*    public function findBidsForLot(Filter $filter, int $page, int $size): PaginationInterface {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'b.id',
                'b.number',
                'b.participant_id',
                'b.lot_id',
                'b.status',
                'b.place',
                'b.participant_ip',
                'b.organizer_ip',
                'b.organizer_ip_created_at',
                'b.created_at',
                'b.deposit_agreement_file_path',
                'b.deposit_agreement_file_name',
                'b.deposit_agreement_file_size',
                'b.deposit_agreement_file_hash',
                'b.deposit_agreement_file_sign',
                'b.deposit_agreement_file_real_name',
                'l.id_number lot_number',
                'p.id_number procedure_number'
            )->from('bids', 'b')
            ->innerJoin('b', 'lots','l','b.lot_id = l.id')
            ->innerJoin('l', 'procedures','p','p.id = l.procedure_id')
            ->orderBy('created_at', 'desc');

        if ($filter->participantId){
            $qb->andWhere('b.participant_id = :participant_id');
            $qb->setParameter(':participant_id', $filter->participantId);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }*/


    /**
     * @param string $id
     * @return DetailView|null
     */
    public function findDetail(string $id): ? DetailView{
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'b.id',
                'b.confirm_xml',
                'b.number',
                'b.participant_id',
                'b.signed_at',
                'b.lot_id',
                'b.status',
                'b.organizer_comment',
                'b.place',
                'b.participant_ip',
                'b.organizer_ip',
                'b.organizer_ip_created_at',
                'b.created_at',
                'b.organizer_comment cause_reject',
                'b.requisite_id',
                'pr.passport_first_name first_name',
                'pr.passport_middle_name middle_name',
                'pr.passport_last_name last_name',
                'pr.passport_last_name last_name',
                'pr.passport_snils passport_snils',
                'pr.passport_inn passport_inn',
                'pr.position',
                'pr.passport_fact_country',
                'pr.passport_fact_region',
                'pr.passport_fact_city',
                'pr.passport_fact_index',
                'pr.passport_fact_street',
                'pr.passport_fact_house',
                'pr.passport_legal_country',
                'pr.passport_legal_region',
                'pr.passport_legal_city',
                'pr.passport_legal_index',
                'pr.passport_legal_street',
                'pr.passport_legal_house',
                'pr.phone user_phone',
                'org.full_title_organization full_title_organization',
                'org.short_title_organization short_title_organization',
                'org.inn inn_organization',
                'org.kpp kpp_organization',
                'org.ogrn ogrn_organization',
                'org.email email_organization',
                'org.fact_country',
                'org.fact_region',
                'org.fact_city',
                'org.fact_index',
                'org.fact_street',
                'org.fact_house',
                'org.legal_country',
                'org.legal_region',
                'org.legal_city',
                'org.legal_index',
                'org.legal_street',
                'org.legal_house',
                'p.id_number procedure_number',
                'p.id procedure_id',
                'cert.thumbprint certificate_thumbprint',
                'cert.subject_name_inn',
                'l.id_number lot_number',
                'l.bailiffs_name_dative_case',
                'l.executive_production_number',
                'l.debtor_full_name',
                'l.debtor_full_name_date_case',
                'p.title procedure_title',
                'l.status lot_status',
                'l.created_at as lot_created_at',
                'l.summing_up_applications',
                'l.closing_date_of_applications',
                'a.start_trading_date',
                'a.id auction_id',
                'p.profile_id organizer_profile_id',
                'profile.incorporated_form',
                'organizer_organization.full_title_organization organizer_full_title_organization',
                'user_q.email user_email'
            )
            ->from('bids', 'b')
            ->where('b.id = :id')
            ->setParameter(':id', $id)
            ->innerJoin('b', 'lots','l','b.lot_id = l.id')
            ->innerJoin('l', 'auctions', 'a', 'a.lot_id = l.id')
            ->innerJoin('l', 'procedures','p','p.id = l.procedure_id')
            ->innerJoin('p','profile_profiles','organizer_profile','p.profile_id = organizer_profile.id')
            ->innerJoin('organizer_profile','profile_organizations','organizer_organization','organizer_profile.organization_id = organizer_organization.id')
            ->innerJoin('b', 'profile_profiles','profile','b.participant_id = profile.id')
            ->innerJoin('profile', 'user_users', 'user_q', 'profile.user_id = user_q.id')
            ->innerJoin('profile', 'profile_representatives','pr','profile.representative_id = pr.id')
            ->innerJoin('profile', 'certificates','cert','profile.certificate_id = cert.id')
            ->leftJoin('profile', 'profile_organizations','org','profile.organization_id = org.id')
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function findApprovedBidByParticipant(string $lotId, string $participantId): ?  DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'confirm_xml',
                'confirm_hash',
                'confirm_sign',
                'requisite_id',
                'status'
            )
            ->from('bids')
            ->where('status = :status')
            ->setParameter(':status', Status::approved()->getName())
            ->andWhere('participant_id = :participant_id')
            ->setParameter(':participant_id', $participantId)
            ->andWhere('lot_id = :lot_id')
            ->setParameter(':lot_id', $lotId)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function findMyBid(string $lot_id, string $participant_id):? DetailView{
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'b.id',
                'b.confirm_xml',
                'b.number',
                'b.participant_id',
                'b.lot_id',
                'b.status',
                'b.organizer_comment',
                'b.place',
                'b.participant_ip',
                'b.organizer_ip',
                'b.organizer_ip_created_at',
                'b.created_at',
                'b.organizer_comment cause_reject',
                'b.requisite_id',
                'pr.passport_first_name first_name',
                'pr.passport_middle_name middle_name',
                'pr.passport_last_name last_name',
                'pr.position',
                'org.full_title_organization full_title_organization',
                'org.inn inn_organization',
                'org.kpp kpp_organization',
                'p.id_number procedure_number',
                'p.id procedure_id',
                'cert.thumbprint certificate_thumbprint',
                'l.id_number lot_number',
                'l.bailiffs_name_dative_case',
                'l.executive_production_number',
                'l.debtor_full_name',
              //  'l.title_of_item',
                'l.status lot_status',
                'l.created_at as lot_created_at',
                'l.summing_up_applications',
                'l.closing_date_of_applications',
                'a.start_trading_date',
                'a.id auction_id',
                'p.profile_id organizer_profile_id'
            )
            ->from('bids', 'b')
            ->where('b.lot_id = :lot_id', 'b.participant_id = :participant_id')
            ->setParameters([':lot_id' => $lot_id, ':participant_id' => $participant_id])
            ->innerJoin('b', 'lots','l','b.lot_id = l.id')
            ->innerJoin('l', 'auctions', 'a', 'a.lot_id = l.id')
            ->innerJoin('l', 'procedures','p','p.id = l.procedure_id')
            ->innerJoin('b', 'profile_profiles','profile','b.participant_id = profile.id')
            ->innerJoin('profile', 'profile_representatives','pr','profile.representative_id = pr.id')
            ->innerJoin('profile', 'certificates','cert','profile.certificate_id = cert.id')
            ->innerJoin('profile', 'profile_organizations','org','p.organization_id = org.id')
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    public function findLastIdNumber():? int{
        $stmt = $this->connection->createQueryBuilder()
            ->select('number')
            ->from('bids')
            ->orderBy('created_at', 'desc')
            ->execute();

        $stmt->setFetchMode(FetchMode::ASSOCIATIVE);
        $result = $stmt->fetch();

        return $result['number'] ?: 0;
    }

    /**
     * @param string $lotId
     * @return int
     * @throws Exception
     */
    public function countConfirmBidsByAuctionId(string $lotId): int{
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('bids', 'b')
            ->where('b.status = :status')
            ->setParameter(':status', Status::approved()->getName())
            ->andWhere('b.lot_id = :lot_id')
            ->setParameter(':lot_id', $lotId)
            ->andWhere('b.confirm_xml IS NOT NULL')
            ->innerJoin('b', 'lots','l','b.lot_id = l.id')
            ->innerJoin('l', 'auctions', 'a', 'a.lot_id = l.id');

        return $qb->execute()->fetchColumn(0) ?: 0;
    }
}
