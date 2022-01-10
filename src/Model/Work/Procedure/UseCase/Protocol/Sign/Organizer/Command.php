<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Protocol\Sign\Organizer;


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
    public $procedureId;

    /**
     * @var string
     */
    public $lotId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $typeProtocol;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $reason;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $xmlFile;

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
     * @var string
     */
    public $requisite;

    /**
     * Command constructor.
     * @param string $userId
     * @param string $procedureId
     * @param string $lotId
     * @param string $typeProtocol
     * @param string $reason
     * @param string $xmlFile
     * @param string $hash
     */
    public function __construct(string $userId,
                                string $procedureId,
                                string $lotId,
                                string $typeProtocol,
                                string $reason,
                                string $xmlFile,
                                string $hash
    ) {
        $this->userId = $userId;
        $this->procedureId = $procedureId;
        $this->lotId = $lotId;
        $this->typeProtocol = $typeProtocol;
        $this->reason = $reason;
        $this->xmlFile = $xmlFile;
        $this->hash = $hash;
    }

}