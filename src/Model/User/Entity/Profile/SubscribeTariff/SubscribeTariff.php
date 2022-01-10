<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\SubscribeTariff;

use App\Model\User\Entity\Profile\Profile;
use App\Model\User\Entity\Profile\Tariff\Tariff;
use App\Model\Work\Procedure\Entity\Lot\Bid\Bid;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class SubscribeTariff
 * @package App\Model\User\Entity\Profile\SubscribeTariff
 * @ORM\Entity()
 * @ORM\Table(name="subscribe_tariffs")
 */
class SubscribeTariff
{
    /**
     * @var Id
     * @ORM\Column(type="subscribe_tariff_id")
     * @ORM\Id()
     */
    private $id;

    /**
     * @var Tariff
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Tariff\Tariff")
     * @ORM\JoinColumn(name="tariff_id", referencedColumnName="id", nullable=true)
     */
    private $tariff;

    /**
     * @var Profile
     * @ORM\ManyToOne(targetEntity="App\Model\User\Entity\Profile\Profile", inversedBy="subscribeTariff")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     */
    private $profile;

    /**
     * @var Bid
     * @ORM\OneToMany(targetEntity="App\Model\Work\Procedure\Entity\Lot\Bid\Bid", mappedBy="subscribeTariff")
     */
    private $bids;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $expires;

    public function __construct(Id $id,
                                Tariff $tariff,
                                \DateTimeImmutable $createdAt,
                                \DateTimeImmutable $expires,
                                Profile $profile)
    {
        $this->id = $id;
        $this->tariff = $tariff;
        $this->createdAt = $createdAt;
        $this->expires = $expires;
        $this->profile = $profile;
    }

    /**
     * @param \DateTimeImmutable $date
     * @return bool
     */
    public function isExpiredTo(\DateTimeImmutable $date): bool {
        return $this->expires <= $date;
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
     * @return Profile
     */
    public function getProfile(): Profile
    {
        return $this->profile;
    }

    /**
     * @return Tariff
     */
    public function getTariff(): Tariff
    {
        return $this->tariff;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool {
        return empty($this->tariff);
    }
}