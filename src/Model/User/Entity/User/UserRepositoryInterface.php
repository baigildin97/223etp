<?php
declare(strict_types=1);
namespace App\Model\User\Entity\User;

interface UserRepositoryInterface
{
    public function hasByEmail(Email $email): bool;

    public function add(User $user): void;

    public function findByConfirmToken(string $confirmToken): ?User;

    public function findByResetToken(string $resetToken): ?User;

    public function hasByNetworkIdentity(string $network, string $identity): bool;

    public function getByEmail(Email $email): ?User;

    public function get(Id $id): ?User;
}