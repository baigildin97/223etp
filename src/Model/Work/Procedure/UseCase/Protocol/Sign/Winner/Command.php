<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Protocol\Sign\Winner;

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
    public $protocolId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $procedureId;

    /**
     * @var string
     */
    public $lotId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $hash;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $sign;

    /**
     * Command constructor.
     * @param string $userId
     * @param string $protocolId
     * @param string $procedureId
     * @param string $lotId
     * @param string $hash
     */
    public function __construct(string $userId, string $protocolId, string $procedureId, string $lotId, string $hash){
        $this->userId = $userId;
        $this->protocolId = $protocolId;
        $this->procedureId = $procedureId;
        $this->lotId = $lotId;
        $this->hash = $hash;
    }
}