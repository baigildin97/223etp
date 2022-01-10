<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile;

interface ProfileRepositoryInterface
{
    public function add(Profile $profile): void;

    public function get(Id $id): ? Profile;
}