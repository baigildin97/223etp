<?php
declare(strict_types=1);
namespace App\ReadModel\User;

use App\Model\User\Entity\User\Role;
use App\ReadModel\User\Filter\Filter;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

class UserFetcher
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
     * UserFetcher constructor.
     * @param Connection $connection
     * @param EntityManagerInterface $entityManager
     * @param PaginatorInterface $paginator
     */
    public function __construct(
        Connection $connection,
        EntityManagerInterface $entityManager,
        PaginatorInterface $paginator
    ){
        $this->connection = $connection;
        $this->paginator = $paginator;
    }

    public function existsByResetToken(string $token): bool {
        return $this->connection->createQueryBuilder()
            ->select('COUNT (*)')
            ->from('user_users')
            ->where('reset_token_token = :token')
            ->setParameter(':token', $token)
            ->execute()
            ->fetchColumn(0) > 0;
    }

    public function findForAuth(string $email): ? AuthView {
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
        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, AuthView::class);

        $result = $stmt->fetch();

        return $result ?: null;
    }

    /**
     * @param string $email
     * @return ShortView|null
     */
    public function findByEmail(string $email): ? ShortView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('email = :email')
            ->setParameter(':email', $email)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, ShortView::class);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * @param string $id
     * @return DetailView|null
     */
    public function findDetail(string $id): ? DetailView {
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'u.id',
                'u.email',
                'u.role',
                'u.status',
                'u.created_at',
                'u.client_ip',
                'p.registration_date',
                'p.id profile_id',
                'p.status as profile_status',
                'p.incorporated_form',
                'p.contract_period',
                'r.role_constant'
            )
            ->from('user_users','u')
            ->leftJoin('u','profile_profiles','p','u.profile_id = p.id')
            ->leftJoin('u', 'roles', 'r', 'p.role_id = r.id')
            ->leftJoin('u','profile_organizations','o','p.	organization_id = o.id')
            ->andWhere('u.id = :id')
            ->setParameter(':id', $id)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $view = $stmt->fetch();
        return $view ?: null;
    }


    public function all(Filter $filter, int $page, int $size, string $orderBy = null, string $order = null): PaginationInterface {
        $qb = $this->connection->createQueryBuilder()
            ->select(
                'u.id',
                'u.created_at',
                'u.profile_id',
                'u.email',
                'u.role',
                'p.contract_period',
                'u.status',
                'u.client_ip',
                'r.role_constant',
                'p.id as profile_id',
                'p.created_at as profile_created_at',
                'p.status as profile_status',
                'p.incorporated_form',
                'p.registration_date',
                'c.subject_name_inn',
                're.phone',
                'pr.short_title_organization',
                'TRIM(CONCAT(re.passport_last_name, \' \', re.passport_first_name, \' \', re.passport_middle_name)) AS user_name'
            )
            ->from('user_users', 'u')
            ->leftJoin('u','profile_profiles','p','u.profile_id = p.id')
            ->leftJoin('p', 'certificates', 'c', 'p.certificate_id = c.id')
            ->leftJoin('p','profile_organizations','pr', 'p.organization_id = pr.id')
            ->leftJoin('u', 'profile_representatives', 're', 'p.representative_id = re.id')
            ->leftJoin('u', 'roles', 'r', 'p.role_id = r.id');

            if ($orderBy && $order)
                $qb->orderBy($orderBy, $order);
            else
                $qb->orderBy('u.created_at', 'desc');

        if ($filter->id){
            $qb->andWhere($qb->expr()->like('id',':id'));
            $qb->setParameter(':id', $filter->id);
        }

        if ($filter->userName){
            $qb->andWhere($qb->expr()->like('LOWER(TRIM(CONCAT(re.passport_last_name, \' \', re.passport_first_name, \' \', re.passport_middle_name)))', ':user_name'))
                ->orWhere($qb->expr()->like('LOWER(short_title_organization)', ':user_name'))
                ->setParameter(':user_name', '%' . strtolower($filter->userName) . '%');
        }

        if ($filter->email){
            $qb->andWhere($qb->expr()->like('u.email',':email'));
            $qb->setParameter(':email', '%' . $filter->email . '%');
        }

        if ($filter->role){
            $qb->andWhere('role = :role');
            $qb->setParameter(':role', $filter->role);
        }

        if ($filter->status){
            $qb->andWhere('p.status = :status');
            $qb->setParameter(':status', $filter->status);
        }

        if ($filter->incorporationForm){
            $qb->andWhere('p.incorporated_form = :incorporated_form');
            $qb->setParameter(':incorporated_form', $filter->incorporationForm);
        }

        if ($filter->inn){
            $qb->andWhere('c.subject_name_inn LIKE :subject_name_inn');
            $qb->setParameter(':subject_name_inn', "%" . $filter->inn . "%");
        }

        if ($filter->clientIp){
            $qb->andWhere('u.client_ip LIKE :client_ip');
            $qb->setParameter(':client_ip', "%" . $filter->clientIp . "%");
        }

        if ($filter->phone)
        {
            $qb->andWhere($qb->expr()->like('re.phone', ':phone'));
            $qb->setParameter(':phone', '%' . $filter->phone . '%');
        }

        return $this->paginator->paginate($qb, $page, $size);
    }

    /**
     * @return array|null
     * @throws Exception
     */
    public function findAdminAndModerator(): ?array{
        $stmt = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'email',
                'role',
                'status'
            )
            ->from('user_users')
            ->where('role IN (:role)')
            ->setParameter(':role', [Role::admin()->getName(), Role::moderator()->getName()], \Doctrine\DBAL\Connection::PARAM_STR_ARRAY)
            ->execute();

        $stmt->setFetchMode(FetchMode::CUSTOM_OBJECT, DetailView::class);
        $result = $stmt->fetchAll();

        return $result ?: null;
    }
}
