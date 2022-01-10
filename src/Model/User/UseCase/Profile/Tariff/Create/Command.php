<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Tariff\Create;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $title;

    /**
     * @var int
     * @Assert\NotBlank()
     */
    public $cost;

    /**
     * @var int
     * @Assert\NotBlank()
     */
    public $period;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $status;

    /**
     * @var Float
     * @Assert\NotBlank
     */
    public $defaultPercent;

}