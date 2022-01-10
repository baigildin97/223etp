<?php
declare(strict_types=1);
namespace App\ReadModel\Admin\Roles;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;

class RoleFetcher
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }


    public function findDetail(string $id): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name',
                'role_constant',
                'permissions'
            )
            ->from('roles')
            ->where('id = :id')
            ->setParameter(':id',$id)
            ->orderBy('name')
            ->execute();
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetch();
        $result->permissions = json_decode($result->permissions);
        return $result ?: null;
    }

    public function allList(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name'
            )
            ->from('roles')
            ->orderBy('name')
            ->execute();

        return $stmt->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    public function all(): array
    {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'name',
                'permissions'
            )
            ->from('roles')
            ->orderBy('name')
            ->execute();

        return array_map(static function (array $role) {
            return array_replace($role, [
                'permissions' => json_decode($role['permissions'], true)
            ]);
        }, $stmt->fetchAll(FetchMode::ASSOCIATIVE));
    }
}