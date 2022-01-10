<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Create;


use App\Model\Work\Procedure\Entity\Lot\Bid\Id;
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
    public $lotId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $clientIp;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $bidId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $requisite;

    public function __construct(string $userId, string $lotId, string $clientIp, Id $bidId) {
        $this->userId = $userId;
        $this->lotId = $lotId;
        $this->clientIp = $clientIp;
        $this->bidId = $bidId;
    }
}
