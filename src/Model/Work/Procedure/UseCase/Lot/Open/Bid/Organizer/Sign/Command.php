<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Organizer\Sign;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $bidId;

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
     * @Assert\NotBlank
     */
    public $client_ip;

    /**
     * Command constructor.
     * @param string $bidId
     * @param string $xml
     * @param string $hash
     * @param string $client_ip
     */
    public function __construct(string $bidId, string $xml, string $hash, string $client_ip) {
        $this->bidId = $bidId;
        $this->hash = $hash;
        $this->xml = $xml;
        $this->client_ip = $client_ip;
    }

}