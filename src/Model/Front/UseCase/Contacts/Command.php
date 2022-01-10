<?php
declare(strict_types=1);
namespace App\Model\Front\UseCase\Contacts;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Command
 * @package App\Model\Front\UseCase\Contacts
 */
class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $name;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $email;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $phone;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $text;

    /**
     * @var string|null
     */
    public $captcha;
}