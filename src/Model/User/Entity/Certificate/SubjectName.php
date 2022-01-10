<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Certificate;


use App\Model\User\Service\Certificate\SubjectConverter\SubjectConverterInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class SubjectName
 * @package App\Model\Certificate\Entity
 * @ORM\Embeddable()
 */
class SubjectName
{
    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $ogrn;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="ogrn_ip", nullable=true)
     */
    private $ogrnIp;

    /**
     * @var string | null
     * @ORM\Column(type="string")
     */
    private $snils;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $inn;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $owner;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $position;

    /**
     * @var string|null
     * @ORM\Column(type="string", name="user_name")
     */
    private $userName;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $region;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $city;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $street;


    public function __construct(SubjectConverterInterface $subjectItem) {
        $this->ogrn = $subjectItem->toExtractOgrn();
        $this->ogrnIp = $subjectItem->toExtractOgrnIp();
        $this->snils = $subjectItem->toExtractSnils();
        $this->inn = $subjectItem->toExtractInn();
        $this->email = $subjectItem->toExtractEmail() ?? 'none';
        $this->owner = $subjectItem->toExtractOwner() ?? 'none';
        $this->position = $subjectItem->toExtractPosition() ?? 'none';
        $this->userName = $subjectItem->toExtractUserName() ?? 'none';
        $this->region = $subjectItem->toExtractRegion() ?? 'none';
	    $this->city = $subjectItem->toExtractCity() ?? 'none';
        $this->street = $subjectItem->toExtractStreet() ?? 'none';
    }


    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return mixed
     */
    public function getInn()
    {
        return $this->inn;
    }

    /**
     * @return mixed
     */
    public function getOgrn()
    {
        return $this->ogrn;
    }

    /**
     * @return mixed
     */
    public function getOgrnIp()
    {
        return $this->ogrnIp;
    }

    /**
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return mixed
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @return mixed
     */
    public function getSnils()
    {
        return $this->snils;
    }

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return mixed
     */
    public function getUserNameExploded()
    {
        $arrUserName = explode(" ", $this->userName);
        return [$arrUserName[2], $arrUserName[1], $arrUserName[0]];
    }

    public function getFullAddress(): string {
        return $this->region.', '.$this->city.', '.$this->street;
    }


}
