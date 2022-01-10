<?php
declare(strict_types=1);
namespace App\ReadModel\User\Notification;


use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Notification\Category;
use App\ReadModel\Profile\FileView;
use App\ReadModel\User\Notification\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class NotificationFetcher
{
    private $connection;
    private $paginator;

    public function __construct(Connection $connection, PaginatorInterface $paginator)
    {
        $this->connection = $connection;
        $this->paginator = $paginator;
    }


    public function all(Filter $filter, int $page, int $size): PaginationInterface {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'id',
                "content_subject as subject",
                "content_text as text",
                'user_id',
                'created_at',
                'updated_at'
            )
            ->from('notifications')
            ->orderBy('created_at', 'desc');

        if ($filter->user_id){
            $qb->andWhere('user_id = :user_id');
            $qb->setParameter(':user_id', $filter->user_id);
        }

        if ($filter->text){
            $qb->andWhere($qb->expr()->like('LOWER(text)', ':text'));
            $qb->setParameter(':text', '%' . mb_strtolower($filter->text) . '%');
        }

        return $this->paginator->paginate($qb, $page, $size);
    }
    /**
     * @param string $id
     * @return \App\ReadModel\Profile\FileView|null
     */
    public function getById(string $id): ? FileView
    {
        $stmnt = $this->connection->createQueryBuilder()
            ->select('*')->from('profile_files')
            ->where('id = :id')->setParameter(':id', $id)->execute();

        $stmnt->setFetchMode(FetchMode::CUSTOM_OBJECT, FileView::class);
        $result = $stmnt->fetch();

        return $result ? $result : NULL;
    }

    /**
     * @param Id $userId
     * @return int
     */
    public function countUnreadNotificationUser(Id $userId): int{
        $qb = $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('notifications')
            ->where('user_id = :user_id')
            ->andWhere('updated_at IS NULL')
            ->setParameters([':user_id' => $userId]);
        return $qb->execute()->fetchColumn(0);
    }

    /**
     * @return int
     */
//    public function countUnreadNotificationModerator(): int{
//        $qb = $this->connection->createQueryBuilder()
//            ->select('COUNT (*)')
//            ->from('notifications')
//            ->where('category_type = :category_type')
//            ->andWhere('updated_at IS NULL')
//            ->setParameters([':category_type' => Category::categoryAdmin()->getName()]);
//        return $qb->execute()->fetchColumn(0);
//    }
}