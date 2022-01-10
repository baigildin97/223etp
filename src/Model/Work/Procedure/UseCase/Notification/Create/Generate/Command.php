<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Notification\Create\Generate;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $organizerComment;
}