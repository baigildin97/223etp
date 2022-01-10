<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\News\Create;

use App\Model\Admin\Entity\News\Status;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Command
 * @package App\Model\Admin\UseCase\News\Create
 */
class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $subject;

    /**
     * @var string|null
     */
    public $delayedPublication;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $text;

}