<?php
declare(strict_types=1);
namespace App\Model\User\Service;

class PasswordHashGenerator
{
    public function hash(string $password):string {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        if ($hash === false){
            throw new \RuntimeException("Unable to generate hash.");
        }
        return $hash;
    }

    public function validate(string $password, string $passwordHash): bool {
        return password_verify($password, $passwordHash);
    }
}