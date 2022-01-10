<?php
declare(strict_types=1);
namespace App\Security;

use App\Model\User\Entity\User\Status;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserIdentity implements UserInterface, EquatableInterface
{

    private $id;
    private $email;
    private $password;
    private $role;
    private $status;
    private $profile;
    private $ip;

    public function __construct(
        string $id,
        string $email,
        string $password,
        string $role,
        string $status,
        string $ip,
        string $profile = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->status = $status;
        $this->profile = $profile;
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getId(): string {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStatus(): string {
        return $this->status;
    }

    public function isActive(): bool {
        return $this->status === Status::active()->getName();
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getPassword(): string {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getProfileId(): ? string {
        return $this->profile;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public final function getUsername()
    {
       return $this->getEmail();
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function addProfile(string $profile)
    {
        $this->profile = $profile;
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if (!$user instanceof self){
            return false;
        }

        return $this->id === $user->id &&
                $this->email === $user->email &&
                $this->status === $user->status &&
                $this->password === $user->password;
    }
}