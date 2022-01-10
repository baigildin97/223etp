<?php


namespace App\Tests\Builder\Profile;


use App\Model\User\Entity\Certificate\Certificate;
use App\Model\User\Entity\Profile\Id;
use App\Model\User\Entity\Profile\Organization\Organization;
use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\Profile\Representative\Representative;
use App\Model\User\Entity\Profile\Requisite\Requisite;
use App\Model\User\Entity\Profile\Status;
use App\Model\User\Entity\Profile\Role;
use App\Model\User\Entity\User\User;
use App\Tests\Builder\Certificate\CertificateBuilder;
use App\Tests\Builder\User\UserBuilder;
use DateTimeImmutable;

class ProfileBuilder
{
    private $type;
    private $createdAt;
    private $user;
    private $certificate;
    private $status;
    private $requisite;
    private $organization;
    private $representative;

    public function __construct(User $user = null, Certificate $certificate = null,
                                DateTimeImmutable $createdAt = null)
    {
        $this->user = $user ?? (new UserBuilder())->viaEmail()->confirmed();
        $this->certificate = $certificate ?? (new CertificateBuilder($this->user))->active()->build();
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->status = null;
        $this->type = null;
    }

    public function organizer(): self {
        $clone = clone $this;
        $clone->type = Type::organizer();

        return $clone;
    }

    public function participant(): self {
        $clone = clone $this;
        $clone->type = Type::participant();

        return $clone;
    }

    public function archived(): self {
        $clone = clone $this;
        $clone->status = Status::archived();

        return $clone;
    }

    public function active(): self {
        $clone = clone $this;
        $clone->status = Status::active();

        return $clone;
    }

    public function moderate(): self {
        $clone = clone $this;
        $clone->status = Status::moderate();

        return $clone;
    }

    public function blocked(): self {
        $clone = clone $this;
        $clone->status = Status::blocked();

        return $clone;
    }

    public function draft(): self {
        $clone = clone $this;
        $clone->status = Status::draft();

        return $clone;
    }

    public function rejected(): self {
        $clone = clone $this;
        $clone->status = Status::rejected();

        return $clone;
    }

    public function wait(): self {
        $clone = clone $this;
        $clone->status = Status::wait();

        return $clone;
    }

    public function build(): Profile {

        return $profile;
    }
}