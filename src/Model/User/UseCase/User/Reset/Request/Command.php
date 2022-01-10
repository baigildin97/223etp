<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\Reset\Request;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $email;
}