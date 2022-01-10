<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Certificate;


use App\Model\User\Service\Certificate\IssuerConverter\IssuerConverterInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class IssuerName
 * @ORM\Embeddable()
 */
class IssuerName
{
    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $issuer;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, name="unit_organization")
     */
    private $unitOrganization;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $inn;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $ogrn;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $region;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $city;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false)
     */
    private $street;

    public function __construct(IssuerConverterInterface $issuerConverter) {
        $this->issuer = $issuerConverter->toExtractCertificationCenter() ?? 'None';
        $this->unitOrganization = $issuerConverter->toExtractOrganizationUnit() ?? 'None';
        $this->inn = $issuerConverter->toExtractInn() ?? 'None';
        $this->ogrn = $issuerConverter->toExtractOgrn() ?? 'None';
        $this->email = $issuerConverter->toExtractEmail() ?? 'None';
        $this->region = $issuerConverter->toExtractRegion() ?? 'None';
        $this->city = $issuerConverter->toExtractCity() ?? 'None';
        $this->street = $issuerConverter->toExtractStreet() ?? 'None';
    }

    /**
     * @return string
     */
    public function getStreet(): string {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getRegion(): string {
        return $this->region;
    }

    /**
     * @return string
     */
    public function getOgrn(): string {
        return $this->ogrn;
    }

    /**
     * @return string
     */
    public function getInn(): string {
        return $this->inn;
    }

    /**
     * @return string
     */
    public function getEmail(): string {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getCity(): string {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getIssuer(): string {
        return $this->issuer;
    }

    /**
     * @return string
     */
    public function getUnitOrganization(): string {
        return $this->unitOrganization;
    }
}