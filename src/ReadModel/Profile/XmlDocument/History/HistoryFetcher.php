<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\XmlDocument\History;

use Doctrine\DBAL\Connection;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\ReadModel\Profile\XmlDocument\History\Filter\Filter;

class HistoryFetcher
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
     * HistoryFetcher constructor.
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
                'h.id',
                'h.action',
                'u.email',
                'h.created_at',
                'h.xml_document_id',
                'u.email moderator',
                'h.client_ip'
            )->from('profile_xml_document_history', 'h')
            ->innerJoin('h', 'user_users', 'u', 'h.moderator_id = u.id')
            ->orderBy('created_at', 'asc');

        if ($filter->xmlDocument){
            $qb->andWhere('h.xml_document_id = :xml_document_id');
            $qb->setParameter(':xml_document_id', $filter->xmlDocument);
        }

        if (!empty($filter->actions)){
            $qb->andWhere('h.action IN (:action)');
            $qb->setParameter(':action', $filter->actions, Connection::PARAM_STR_ARRAY);
        }

//        if ($filter->email){
//            $qb->andWhere('u.email = :email');
//            $qb->setParameter(':email', $filter->email);
//        }

        return $this->paginator->paginate($qb, $page, $size);
    }
}