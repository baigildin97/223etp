<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Bid\Document;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class File
 * @package App\Model\Work\Procedure\Entity\Lot\Bid\Document
 * @ORM\Embeddable()
 */
class File
{
    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $path;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private $size;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $realName;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $sign;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $hash;

    /**
     * File constructor.
     * @param string $path
     * @param string $name
     * @param int $size
     * @param string $realName
     * @param string $hash
     */
    public function __construct(
        string $path,
        string $name,
        int $size,
        string $realName,
        string $hash
    ) {
        $this->path = $path;
        $this->name = $name;
        $this->size = $size;
        $this->realName = $realName;
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getSign(): string
    {
        return $this->sign;
    }

    public function addSign(string $sign): void {
        $this->sign = $sign;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }
}