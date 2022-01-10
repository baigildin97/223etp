<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Duplicate;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $userId;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $clientIp;

    /**
     * Command constructor.
     * @param string $id
     * @param string $userId
     * @param string $clientIp
     */
    public function __construct(string $id, string $userId, string $clientIp)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->clientIp = $clientIp;
    }

}
