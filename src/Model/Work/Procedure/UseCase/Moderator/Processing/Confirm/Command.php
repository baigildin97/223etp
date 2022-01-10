<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Moderator\Processing\Confirm;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $procedureXmlDocumentId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $moderatorId;

    /**
     * @var string
     */
    public $cause;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $clientIp;


    public function __construct(string $procedureXmlDocumentId, string $moderatorId, string $clientIp){
        $this->procedureXmlDocumentId = $procedureXmlDocumentId;
        $this->moderatorId = $moderatorId;
        $this->clientIp = $clientIp;
    }
}