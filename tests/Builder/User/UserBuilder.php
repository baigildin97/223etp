<?php
declare(strict_types=1);
namespace App\Tests\Builder\User;


use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\Name;
use App\Model\User\Entity\User\Role;
use App\Model\User\Entity\User\User;

class UserBuilder
{
    private $id;
    private $createdAt;
    private $email;
    private $passwordHash;
    private $confirmToken;
    private $status;
    private $network;
    private $identity;
    private $confirmed;
    private $role;
    private $name;

    public function __construct()
    {
        $this->id = Id::next();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function viaEmail(Email $email = null, string $passwordHash = null, string $confirmToken = null): self {
        $clone = clone $this;
        $clone->email = $email ?? new Email("test@mail.ru");
        $clone->name = $name ?? new Name('first', 'last', 'middle');
        $clone->passwordHash = $passwordHash ?? "passwordHash";
        $clone->confirmToken = $confirmToken ?? "confirmToken";
        return $clone;
    }

    public function withRole(Role $role): self {
        $clone = clone $this;
        $clone->role = $role;
        return $clone;
    }

    public function confirmed(): self {
        $clone = clone $this;
        $clone->confirmed = true;
        return $clone;
    }

    public function build(): User{
        $user = null;
        if ($this->email){
            $user = User::signUpByEmail(
                $this->id,
                $this->createdAt,
                $this->email,
                $this->name,
                $this->passwordHash,
                $this->confirmToken
            );
            if ($this->confirmed){
                $user->confirmSignUp();
            }
        }

        if (!$user){
            throw new \BadMethodCallException('Specify via method.');
        }

        return $user;
    }
}