<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Tariff\Levels;

use App\Model\User\Entity\Profile\Tariff\Tariff;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;


/**
 * Class Level
 * @package App\Model\User\Entity\Profile\Tariff\Levels
 * @ORM\Entity
 * @ORM\Table(name="levels")
 */
class Level
{
    /**
     * @var Id
     * @ORM\Column(type="level_id")
     * @ORM\Id
     */
    private $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $priority;

    /**
     * @var Tariff
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Tariff\Tariff", inversedBy="level")
     * @ORM\JoinColumn(name="id_tariff", referencedColumnName="id", nullable=false)
     */
    private $tariff;

    /**
     * @var Money
     * @ORM\Column(type="money")
     */
    private $amount;

    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $percent;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var Money
     */
    private $startCostLot;

    /**
     * Level constructor.
     * @param Id $id
     * @param float $priority
     * @param Tariff $tariff
     * @param Money $amount
     * @param float $percent
     * @param \DateTimeImmutable $createdAt
     */
    public function __construct(Id $id,
                                float $priority,
                                Tariff $tariff,
                                Money $amount,
                                float $percent,
                                \DateTimeImmutable $createdAt)
    {
        $this->id = $id;
        $this->priority = $priority;
        $this->tariff = $tariff;
        $this->amount = $amount;
        $this->percent = $percent;
        $this->createdAt = $createdAt;
    }

    /**
     * @return Money
     */
    public function getAmount(): Money
    {
        return $this->amount;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getPercent(): float
    {
        return $this->percent;
    }


    /**
     * @param Money $startLotCost
     */
    public function setStartCostLot(Money $startLotCost): void
    {
        $this->startCostLot = $startLotCost;
    }


    public function getCharged(): Money {
        list($my_cut, $investorsCut) = $this->startCostLot->allocate([
            100 - $this->percent,
            $this->percent
        ]);
        return $investorsCut;
    }
}