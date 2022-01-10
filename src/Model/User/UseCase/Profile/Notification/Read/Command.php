<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Notification\Read;

use App\Model\User\Entity\User\Notification\Id;
use Symfony\Component\Validator\Constraints as Assert;

class Command{

    /**
     * @var Id
     * @Assert\NotBlank()
     */
    public $notification_id;

    public function __construct(Id $notification_id){
        $this->notification_id = $notification_id;
    }
}