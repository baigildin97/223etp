<?php
declare(strict_types=1);
namespace App\Services\Uploader;


class File
{

    private $path;
    private $name;
    private $size;
    private $sign;
    private $hash;
    private $realName;

    public function __construct(
        string $path,
        string $name,
        int $size,
        string $realName,
        string $hash = null
    )
    {
        $this->path = $path;
        $this->name = $name;
        $this->size = $size;
        $this->hash = $hash;
        $this->realName = $realName;
    }

    /**
     * @return string
     */
    public function getPath(): string {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getSize(): int {
        return $this->size;
    }

    /**
     * @return string|null
     */
    public function getHash(): ? string {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getRealName(): string {
        return $this->realName;
    }
}