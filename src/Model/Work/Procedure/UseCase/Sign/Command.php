<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Sign;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $procedureId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $xml;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $hash;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $user_id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $clientIp;

    /**
     * Command constructor.
     * @param string $procedureId
     * @param string $xml
     * @param string $hash
     * @param string $user_id
     * @param string $clientIp
     */
    public function __construct(
        string $procedureId,
        string $xml,
        string $hash,
        string $user_id,
        string $clientIp
    ) {
        $this->procedureId = $procedureId;
        $this->xml = $xml;
        $this->hash = $hash;
        $this->user_id = $user_id;
        $this->clientIp = $clientIp;
    }
}
