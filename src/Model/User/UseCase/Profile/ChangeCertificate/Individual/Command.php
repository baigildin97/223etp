<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\ChangeCertificate\Individual;

use App\Model\Profile\Entity\Profile\Files\CopyFileInn;
use App\Model\Profile\Entity\Profile\Files\CopyFilePassport;
use App\Model\Profile\Entity\Profile\Files\CopyFileSnils;
use App\Model\Profile\Entity\Profile\Files\FileQuestion;
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
        $this->clientIp = $clientIp;
        $this->userId = $userId;
    }
}
