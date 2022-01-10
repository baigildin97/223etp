<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Accredation\Sign;


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
    public $sign;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $hash;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $xml;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $clientIp;

    /**
     * Command constructor.
     * @param string $profileId
     * @param string $xml
     * @param string $hash
     * @param string $clientIp
     */
    public function __construct(string $profileId, string $xml, string $hash, string $clientIp) {
        $this->profileId = $profileId;
        $this->hash = $hash;
        $this->xml = $xml;
        $this->clientIp = $clientIp;
    }

}