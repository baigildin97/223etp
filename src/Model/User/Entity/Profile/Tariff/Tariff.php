<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Tariff;


use App\Model\User\Entity\Profile\Tariff\Levels\Level;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Mapping as ORM;
use Money\Money;


/**
 * Class Tariff
 * @package App\Model\User\Entity\Profile\Tariff
 * @ORM\Entity()
 * @ORM\Table(name="tariff")
 */
class Tariff
{
    /**
     * @var Id
     * @ORM\Column(type="tariff_id")
     * @ORM\Id()
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @var Money
     * @ORM\Column(type="money")
     */
    private $cost;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $period;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $unlimited;

    /**
     * @var Status
     * @ORM\Column(type="tariff_status_type")
     */
    private $status;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $archivedAt;

    /**
     * @var ArrayCollection|Level[]
     * @ORM\OneToMany(targetEntity="App\Model\User\Entity\Profile\Tariff\Levels\Level", mappedBy="tariff", orphanRemoval=true, cascade={"all"})
     */
    private $levels;
    
    /**
     * @var float
     * @ORM\Column(type="float")
     */
    private $defaultPercent;


    public function __construct(
        Id $id,
        string $title,
        Money $cost,
        int $period,
        Status $status,
        \DateTimeImmutable $createdAt,
        float $defaultPercent
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->cost = $cost;
        $this->period = $period;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->unlimited = false;
        $this->defaultPercent = $defaultPercent;
    }


    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return Status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getArchivedAt(): \DateTimeImmutable
    {
        return $this->archivedAt;
    }

    /**
     * @return int
     */
    public function getPeriod(): int
    {
        return $this->period;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Money
     */
    public function getCost(): Money
    {
        return $this->cost;
    }

    /**
     * @return bool
     */
    public function isUnlimited(): bool
    {
        return $this->unlimited;
    }

    /**
     * @return float
     */
    public function getDefaultPercent(): float
    {
        return $this->defaultPercent;
    }

    /**
     * @param string $title
     * @param string $cost
     * @param int $period
     * @param Status $status
     * @param Float $defaultPercent
     */
    public function edit(string $title, string $cost, int $period, Status $status, Float $defaultPercent): void {
        if ($this->status->isArchived()){
            throw new \DomainException('Tariff is in archive.');
        }
        $this->title = $title;
        $this->cost = $cost;
        $this->period = $period;
        $this->status = $status;
        $this->defaultPercent = $defaultPercent;
    }

    /**
     * @return Level[]|ArrayCollection
     */
    public function getLevels()
    {

        return $this->levels;
    }


    public function getLevel(Money $starCostLot): Level{

        $criteria = new Criteria();
        $criteria->orderBy(['priority' => Criteria::ASC]);
        $levelsSorted = $this->getLevels()->matching($criteria);
        foreach ($levelsSorted as $level){
            if ($level->getAmount()->greaterThanOrEqual($starCostLot)){
                $level->setStartCostLot($starCostLot);
                return $level;
            }
        }

        throw new \DomainException("Error summa");
    }



}