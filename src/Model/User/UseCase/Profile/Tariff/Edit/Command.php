<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Tariff\Edit;


use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $title;

    /**
     * @var string
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
     * @var string
     * @Assert\NotBlank()
     */
    public $tariffId;

    public static function edit(string $title, string $cost, int $period, string $status, string $tariffId): self{
        $cm = new self();
        $cm->title = $title;
        $cm->cost = $cost;
        $cm->period = $period;
        $cm->status = $status;
        $cm->tariffId = $tariffId;
        return $cm;
    }
}