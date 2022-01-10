<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Certificate\Create;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $userId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $sign;

    public function __construct(string $userId) {
        $this->userId = $userId;
    }

}