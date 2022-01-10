<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Role;

use App\Model\User\Entity\Profile\Profile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;


/**
 * Class Role
 * @package App\Model\User\Entity\Profile\Role
 * @ORM\Entity()
 * @ORM\Table(name="roles")
 */
class Role
{
    public const ROLE_ORGANIZER = 'ROLE_ORGANIZER';
    public const ROLE_PARTICIPANT = 'ROLE_PARTICIPANT';

    /**
     * @var Id
     * @ORM\Column(type="role_id")
     * @ORM\Id
     */
    private $id;

    /**
     * @var Profile
     * @ORM\OneToMany(targetEntity="App\Model\User\Entity\Profile\Profile", mappedBy="role")
     */
    private $profiles;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $roleConstant;

    /**
     * @var ArrayCollection|Permission[]
     * @ORM\Column(type="role_permissions")
     */
    private $permissions;

    /**
     * @ORM\Version()
     * @ORM\Column(type="integer")
     */
    private $version;

    /**
     * Role constructor.
     * @param Id $id
     * @param string $name
     * @param string $roleConstant
     * @param string[] $permissions
     */
    public function __construct(Id $id, string $name, string $roleConstant, array $permissions)
    {
        $this->id = $id;
        $this->name = $name;
        $this->roleConstant = $roleConstant;
        $this->setPermissions($permissions);
    }

    /**
     * @param string $name
     * @param string $roleConstant
     * @param string[] $permissions
     */
    public function edit(string $name, string $roleConstant, array $permissions): void
    {
        $this->name = $name;
        $this->roleConstant = $roleConstant;
        $this->setPermissions($permissions);
    }

    /**
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions->exists(static function (int $key, Permission $current) use ($permission) {
            return $current->isNameEqual($permission);
        });
    }

    /**
     * @param Id $id
     * @param string $name
     * @param string $roleConstant
     * @return $this
     */
    public function clone(Id $id, string $name, string $roleConstant): self
    {
        return new self($id, $name, $roleConstant,  array_map(static function (Permission $permission) {
            return $permission->getName();
        }, $this->permissions->toArray()));
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Permission[]
     */
    public function getPermissions(): array
    {
        return $this->permissions->toArray();
    }

    /**
     * @return string
     */
    public function getRoleConstant(): string
    {
        return $this->roleConstant;
    }

    /**
     * @return mixed
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param array $names
     */
    public function setPermissions(array $names): void
    {
        $this->permissions = new ArrayCollection(array_map(static function (string $name) {
            return new Permission($name);
        }, array_unique($names)));
    }

    public function isEqual(self $type): bool {
        return $this->getName() === $type->getName();
    }

    /**
     * @return bool
     */
    public function isOrganizer(): bool {
        return $this->roleConstant === self::ROLE_ORGANIZER;
    }

    /**
     * @return bool
     */
    public function isParticipant(): bool {
        return $this->roleConstant === self::ROLE_PARTICIPANT;
    }
}
