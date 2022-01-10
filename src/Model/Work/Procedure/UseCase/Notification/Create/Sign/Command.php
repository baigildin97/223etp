<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Notification\Create\Sign;


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
     * @Assert\NotBlank()
     */
    public $sign;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $notificationType;

    public function __construct(string $procedureId, string $xml, string $hash, string $notificationType)
    {
        $this->procedureId = $procedureId;
        $this->xml = $xml;
        $this->hash = $hash;
        $this->notificationType = $notificationType;
    }

}