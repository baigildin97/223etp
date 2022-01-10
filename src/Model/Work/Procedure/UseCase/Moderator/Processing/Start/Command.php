<?php
namespace App\Model\Work\Procedure\UseCase\Moderator\Processing\Start;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $moderatorId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $clientIp;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $xmlDocumentId;

    /**
     * Command constructor.
     * @param string $moderatorId
     * @param string $xmlDocumentId
     * @param string $clientIp
     */
    public function __construct(string $moderatorId, string $xmlDocumentId, string $clientIp)
    {
        $this->moderatorId = $moderatorId;
        $this->xmlDocumentId = $xmlDocumentId;
        $this->clientIp = $clientIp;
    }
}