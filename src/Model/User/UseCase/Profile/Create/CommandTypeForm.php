<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Create;


use Symfony\Component\Validator\Constraints as Assert;

class CommandTypeForm
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $typeProfile;
}