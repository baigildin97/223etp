<?php
declare(strict_types=1);
namespace App\ReadModel\Auction;

use App\Model\Work\Procedure\Entity\Lot\Auction\Status;
use App\ReadModel\Auction\Filter\Filter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use phpDocumentor\Reflection\Types\Integer;

class AuctionFetcher
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
                'l.starting_price',
                'a.start_trading_date',
                'l.id_number',
                'l.id as id_lot'
            )->from('auctions', 'a')
            ->orderBy('created_at', 'desc');

        if ($filter->winner_id){
            $qb->andWhere('a.winner_id = :winner_id');
            $qb->setParameter(':winner_id', $filter->winner_id);
            $qb->innerJoin('a', 'lots', 'l', 'a.lot_id = l.id');
        }

        return $this->paginator->paginate($qb, $page, $size);
    }


    /**
     * @param string $id
     * @return DetailView|null
     */
    public function findDetail(string $id): ? DetailView{
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'a.id',
                'a.lot_id',
                'a.default_closed_time',
                'a.current_cost',
                'a.auction_step',
                'a.status',
                'l.starting_price',
                'l.id_number lot_id_number',
                'l.procedure_id'
            )
            ->from('auctions', 'a')
            ->where('a.id = :id')
            ->setParameter(':id', $id)
            ->innerJoin('a', 'lots','l','a.lot_id = l.id')
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * @return array|null
     */
    public function findAllByStatusWait(): ? array {
        $currentDate = new \DateTimeImmutable();
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'a.id',
                'a.lot_id',
                'a.start_trading_date',
                'l.status',
                'l.procedure_id'
            )
            ->from('auctions', 'a')
            ->orderBy('a.created_at', 'desc')
            ->innerJoin('a', 'lots','l','a.lot_id = l.id')
            ->where('a.status = :status')
            ->andWhere('a.start_trading_date < :start_trading_date')
            ->andWhere('l.status = :statusLot')
            ->setParameters(
                [
                    ':status' => Status::wait()->getName(),
                    ':start_trading_date' => $currentDate->format("Y-m-d H:i:s"),
                    ':statusLot' => \App\Model\Work\Procedure\Entity\Lot\Status::statusStartOfTrading()->getName()
                ])
            ->setMaxResults('50')
            ->execute();

        $result = $qb->fetchAll();
        return $result ?: null;
    }

    public function findAllByStatusActive(): ? array {
        $currentDate = new \DateTimeImmutable();
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'a.id',
                'a.default_closed_time',
                'l.status',
                'l.procedure_id'
            )
            ->from('auctions', 'a')
            ->orderBy('a.created_at', 'desc')
            ->innerJoin('a', 'lots','l','a.lot_id = l.id')
            ->where('a.status = :status')
            ->andWhere('l.status = :statusLot')
            ->andWhere('a.default_closed_time < :default_closed_time')
            ->setParameters([':status' => Status::active()->getName(),
                ':default_closed_time' => $currentDate->format("Y-m-d H:i:s"),
                ':statusLot' => \App\Model\Work\Procedure\Entity\Lot\Status::statusBiddingProcess()->getName()
            ])
            ->setMaxResults('50')
            ->execute();

        $result = $qb->fetchAll();
        return $result ?: null;

    }

    public function findWinner(string $auction_id){


    }


}