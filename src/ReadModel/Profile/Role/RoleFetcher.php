<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\Role;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Knp\Component\Pager\PaginatorInterface;

class RoleFetcher
{
    private $connection;

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }

    public function findForAuth(string $email): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'password_hash',
                'role',
                'status',
                'profile_id'
            )
            ->from('user_users')
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute();
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);

        $result = $stmt->fetch();

        return $result ?: null;
    }
}