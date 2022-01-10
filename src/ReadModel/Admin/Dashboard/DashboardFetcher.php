<?php
declare(strict_types=1);

namespace App\ReadModel\Admin\Dashboard;

use App\Model\User\Entity\Profile\Status;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\Exception;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class DashboardFetcher
 * @package App\ReadModel\Admin\Dashboard
 */
class DashboardFetcher
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
     * DashboardFetcher constructor.
     * @param Connection $connection
     * @param PaginatorInterface $paginator
     */
    public function __construct(Connection $connection, PaginatorInterface $paginator){
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    /**
     * @return false|mixed
     * @throws Exception
     */
    public function countAllUsers() {
        return $this->connection->createQueryBuilder()
                ->select('COUNT (*)')
                ->from('user_users')
                ->execute()
                ->fetchColumn();
    }

    /**
     * @return false|mixed
     * @throws Exception
     */
    public function countActiveProfileUsers() {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('user_users', 'u')
            ->leftJoin('u','profile_profiles','p','u.profile_id = p.id')
            ->andWhere('p.status = :status')
            ->setParameter(':status', Status::active()->getName())
            ->execute()
            ->fetchColumn();
    }



    /**
     * @return false|mixed
     * @throws Exception
     */
    public function countModerateProfileUsers() {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('user_users', 'u')
            ->leftJoin('u','profile_profiles','p','u.profile_id = p.id')
            ->andWhere('p.status = :status')
            ->setParameter(':status', Status::moderate()->getName())
            ->execute()
            ->fetchColumn();
    }

    /**
     * @return ResultStatement|int
     * @throws Exception
     */
    public function countBlockedProfileUsers() {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('user_users')
            ->andWhere('status = :status')
            ->setParameter(':status', Status::blocked()->getName())
            ->execute()
            ->fetchColumn();
    }

    /**
     * @return false|mixed
     * @throws Exception
     */
    public function sumAvailableAmount(){
        return $this->connection->createQueryBuilder()
            ->select("sum(cast(overlay(available_amount placing '' from 1 for 4) as INTEGER))")
            ->from('profile_payments')
            ->execute()
            ->fetchColumn();
    }

    /**
     * @return false|mixed
     * @throws Exception
     */
    public function sumBlockedAmount(){
        return $this->connection->createQueryBuilder()
            ->select("sum(cast(overlay(blocked_amount placing '' from 1 for 4) as INTEGER))")
            ->from('profile_payments')
            ->execute()
            ->fetchColumn();
    }

    /**
     * @return false|mixed
     * @throws Exception
     */
    public function sumWithdrawalAmount(){
        return $this->connection->createQueryBuilder()
            ->select("sum(cast(overlay(withdrawal_amount placing '' from 1 for 4) as INTEGER))")
            ->from('profile_payments')
            ->execute()
            ->fetchColumn();
    }

    /**
     * @return false|mixed
     * @throws Exception
     */
    public function countAllProcedures() {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('procedures')
            ->execute()
            ->fetchColumn();
    }

    /**
     * @return false|mixed
     * @throws Exception
     */
    public function countModerateProcedures() {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('procedures')
            ->andWhere('status = :status')
            ->setParameter(':status', \App\Model\Work\Procedure\Entity\Status::moderate()->getName())
            ->execute()
            ->fetchColumn();
    }

    /**
     * @return false|mixed
     * @throws Exception
     */
    public function countFailedProcedures() {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('procedures')
            ->andWhere('status = :status')
            ->setParameter(':status', \App\Model\Work\Procedure\Entity\Status::failed()->getName())
            ->execute()
            ->fetchColumn();
    }

    /**
     * @return false|mixed
     * @throws Exception
     */
    public function countAllBids() {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('bids')
            ->execute()
            ->fetchColumn();
    }

    /**
     * @return false|mixed
     * @throws Exception
     */
    public function countApprovedBids() {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('bids')
            ->andWhere('status = :status')
            ->setParameter(':status', \App\Model\Work\Procedure\Entity\Lot\Bid\Status::approved()->getName())
            ->execute()
            ->fetchColumn();
    }

    /**
     * @return false|mixed
     * @throws Exception
     */
    public function countSentBids() {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('bids')
            ->andWhere('status = :status')
            ->setParameter(':status', \App\Model\Work\Procedure\Entity\Lot\Bid\Status::sent()->getName())
            ->execute()
            ->fetchColumn();
    }


}