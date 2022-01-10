<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Document\Upload;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $lotId;

    /**
     * @var File
     * @Assert\NotBlank()
     */
    public $file;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $fileTitle;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $clientIp;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $userId;

    public function __construct(string $lotId, string $clientIp, string $userId) {
        $this->lotId = $lotId;
        $this->clientIp = $clientIp;
        $this->userId = $userId;
    }

}