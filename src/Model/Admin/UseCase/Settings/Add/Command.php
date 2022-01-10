<?php
declare(strict_types=1);
namespace App\Model\Admin\UseCase\Settings\Add;


use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Command
 * @package App\Model\Admin\UseCase\Settings\Add
 */
class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $key;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $value;

}