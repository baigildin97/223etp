<?php


namespace App\Model\Admin\Entity\Holidays;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class News
 * @package App\Model\Admin\Entity\Holidays
 * @ORM\Entity()
 * @ORM\Table(name="holidays")
 */

class Holiday
{
    /**
     * @var
     * @ORM\Id()
     * @ORM\Column(type="holidays_id")
     */
    private $id;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="date_start")
     */
    private $dateStart;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", name="date_end")
     */
    private $dateEnd;

    /**
     * @var Status
     * @ORM\Column(type="holidays_status")
     */
    private $status;

    public function __construct(Id $id, \DateTimeImmutable $dateStart, \DateTimeImmutable $dateEnd)
    {
        $this->id = $id;
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->status = Status::active();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @return mixed
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }
}