<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Document\Upload;


class File
{
    public $path;
    public $name;
    public $size;
    public $hash;
    public $realName;

    public function __construct(string $path, string $name, int $size, string $realName, string $hash)
    {
        $this->path = $path;
        $this->name = $name;
        $this->size = $size;
        $this->hash = $hash;
        $this->realName = $realName;
    }
}