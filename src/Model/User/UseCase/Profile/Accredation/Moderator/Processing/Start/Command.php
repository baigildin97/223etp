<?php
namespace App\Model\User\UseCase\Profile\Accredation\Moderator\Processing\Start;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Command
 * @package App\Model\User\UseCase\Profile\Accredation\Moderator\Processing\Start
 */
class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $xmlDocumentId;

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
     * Command constructor.
     * @param string $xmlDocumentId
     * @param string $moderatorId
     * @param string $clientIp
     */
    public function __construct(string $xmlDocumentId, string $moderatorId, string $clientIp)
    {
        $this->xmlDocumentId = $xmlDocumentId;
        $this->moderatorId = $moderatorId;
        $this->clientIp = $clientIp;
    }
}