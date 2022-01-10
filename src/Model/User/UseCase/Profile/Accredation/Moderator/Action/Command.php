<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Accredation\Moderator\Action;

use App\Model\User\Entity\User\User;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank();
     */
    public $profile_id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $user_id;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $xmlDocumentId;

    /**
     * @var string
     */
    public $cause;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $clientIp;

    /**
     * Command constructor.
     * @param string $xmlDocumentId
     * @param string $profile_id
     * @param string $user_id
     */
    public function __construct(string $xmlDocumentId, string $profile_id, string $user_id, string $clientIp){
        $this->profile_id = $profile_id;
        $this->xmlDocumentId = $xmlDocumentId;
        $this->user_id = $user_id;
        $this->clientIp = $clientIp;
    }
}