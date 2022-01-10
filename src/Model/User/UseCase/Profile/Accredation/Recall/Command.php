<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Accredation\Recall;


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
     * Command constructor.
     * @param string $bidId
     * @param string $xml
     * @param string $hash
     */
    public function __construct(string $profileId, string $xml, string $hash) {
        $this->profileId = $profileId;
        $this->hash = $hash;
        $this->xml = $xml;
    }

}