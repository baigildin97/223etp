<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Bid;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Xml
 * @package App\Model\Work\Procedure\Entity\Lot\Bid
 * @ORM\Embeddable()
 */
class Xml
{
    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $file;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $hash;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $sign;

    /**
     * @var \DateTimeImmutable
     */
    private $signedAt;

    /**
     * Xml constructor.
     * @param string $file
     * @param string $hash
     * @param string $sign
     * @param \DateTimeImmutable $signedAt
     */
    public function __construct(string $file, string $hash, string $sign, \DateTimeImmutable $signedAt) {
        $this->file = $file;
        $this->hash = $hash;
        $this->sign = $sign;
        $this->signedAt = $signedAt;
    }
}