<?php
declare(strict_types=1);
namespace App\ReadModel\Auction\Offers;

use App\ReadModel\Auction\Offers\Filter\Filter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class OffersFetcher
{
    private $connection;
    private $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator){
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function all(Filter $filter, int $page, int $size): PaginationInterface {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'a.id',
                'a.cost',
                'a.created_at',
                'b.number bid_number',
                'b.participant_id',
                "CONCAT(pr.passport_last_name, ' ', pr.passport_first_name, ' ', pr.passport_middle_name) AS owner",
            )->from('auction_offers', 'a')
            ->innerJoin('a','bids','b', 'a.bid_id = b.id')
            ->innerJoin('b', 'profile_profiles','profile','b.participant_id = profile.id')
            ->innerJoin('profile', 'profile_representatives','pr','profile.representative_id = pr.id')
            ->orderBy('created_at', 'desc');

        if ($filter->auction_id){
            $qb->andWhere('auction_id = :auction_id');
            $qb->setParameter(':auction_id', $filter->auction_id);
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

    /**
     * Возвращает предложения участника
     * @param string $bidId
     * @return DetailView|null
     */
    public function findMyOffer(string $bidId): ? DetailView{
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'cost'
            )->from('auction_offers')
            ->where('bid_id = :bid_id')
            ->setParameter(':bid_id', $bidId)
            ->execute();

        $qb->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $qb->fetch();

        return $result ?: null;

    }

    /**
     * Возвращает последнию ставку|или null
     * @param string $auctionId
     * @return DetailView|null
     */
    public function findLastOffer(string $auctionId): ? DetailView{
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'bid_id',
                'cost'
            )->from('auction_offers')
            ->orderBy('created_at', 'desc')
            ->where('auction_id = :auction_id')
            ->setParameter(':auction_id', $auctionId)
            ->execute();

        $qb->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $qb->fetch();

        return $result ?: null;
    }


    /**
     * @param string $auctionId
     * @param string $bidId
     * @return int|null
     */
    public function findCurrentPosition(string $auctionId, string $bidId): ? int {
        $qb = $this->connection->createQueryBuilder()
            ->select('bid_id')
            ->from('auction_offers')
            ->where('auction_id = :auction_id')
            ->setParameter(':auction_id', $auctionId)
            ->orderBy('created_at', 'desc')
            ->execute();

        $qb->setFetchMode(FetchMode::ASSOCIATIVE);

        $result = $qb->fetchAll();
        $result = new ArrayCollection(array_column($result, 'bid_id'));
        $position = $result->indexOf($bidId);
        if($position !== false){
            return $position + 1;
        }
        return null;
    }

    /**
     * @param string $auctionId
     * @return int
     * @throws Exception
     */
    public function countOffersByAuctionId(string $auctionId): int{
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('auction_offers')
            ->where('auction_id = :auction_id')
            ->setParameter(':auction_id', $auctionId);

        return $qb->execute()->fetchColumn(0) ?: 0;
    }

}