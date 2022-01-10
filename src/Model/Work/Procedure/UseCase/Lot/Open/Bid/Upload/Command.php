<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Upload;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $bidId;

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

    public function __construct(string $bidId, string $clientIp) {
        $this->bidId = $bidId;
        $this->clientIp = $clientIp;
    }

}