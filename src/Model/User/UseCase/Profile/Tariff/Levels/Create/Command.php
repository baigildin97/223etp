<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Tariff\Levels\Create;

use App\Model\User\Entity\Profile\Tariff\Id;
use App\Model\User\Entity\Profile\Tariff\Tariff;
use Money\Money;
use Symfony\Component\Validator\Constraints as Assert;

class Command{

    /**
     * @var Tariff
     * @Assert\NotBlank()
     */
    public $tariff_id;

    /**
     * @var Money
     * @Assert\NotBlank()
     */
    public $amount;

    /**
     * @var float
     * @Assert\NotBlank()
     */
    public $priority;

    /**
     * @var float
     * @Assert\NotBlank
     */
    public $percent;


    public function __construct(string $tariff_id){
        $this->tariff_id = $tariff_id;
    }
}