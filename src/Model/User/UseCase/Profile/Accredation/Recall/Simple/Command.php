<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Accredation\Recall\Simple;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $profileId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $xmlDocumentId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $xml;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $sign;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $hash;

    /**
     * @var string
     */
    public $clientIp;

    /**
     * Command constructor.
     * @param string $profileId
     * @param string $xmlDocumentId
     * @param string $clientIp
     * @param string $xml
     * @param string $sign
     */
    public function __construct(
        string $profileId,
        string $xmlDocumentId,
        string $clientIp,
        string $xml,
        string $hash
    ){
        $this->profileId = $profileId;
        $this->xmlDocumentId = $xmlDocumentId;
        $this->clientIp = $clientIp;
        $this->xml = $xml;
        $this->hash = $hash;
    }

}