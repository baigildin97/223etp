<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\ChangeCertificate\LegalEntity;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $userId;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $certificate;

    /**
     * @var string
     */
    public $clientIp;

    public function __construct(string $userId, string $clientIp) {
        $this->userId = $userId;
        $this->clientIp = $clientIp;
    }
}