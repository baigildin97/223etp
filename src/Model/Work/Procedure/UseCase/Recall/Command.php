<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Recall;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $notificationId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $procedureId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $clientIp;

    /**
     * Command constructor.
     * @param string $notificationId
     * @param string $procedureId
     */
    public function __construct(string $notificationId, string $procedureId, string $clientIp) {
        $this->notificationId = $notificationId;
        $this->procedureId = $procedureId;
        $this->clientIp = $clientIp;
    }

}