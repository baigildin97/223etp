<?php


namespace App\ReadModel\Admin\Holidays;


use App\Model\Admin\Entity\Holidays\Status;
use Doctrine\DBAL\Connection;

class HolidaysFetcher
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function allActive(): ? array
    {
        $qb = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('holidays')
            ->where('status = :status')
            ->setParameter(':status', Status::active()->getName())
            ->execute();

        return ($result = $qb->fetchAllAssociative()) ? $result : null;
    }
}