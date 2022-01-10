<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Notification\Sign;


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
    public $sign;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $hash;

    /**
     * Command constructor.
     * @param string $notificationId
     * @param string $hash
     */
    public function __construct(string $notificationId, string $hash){
        $this->notificationId = $notificationId;
        $this->hash = $hash;
    }

}