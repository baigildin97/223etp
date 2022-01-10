<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Bid;


use Webmozart\Assert\Assert;

class IdNumber
{
    private $value;

    public function __construct(string $value)
    {
        Assert::notEmpty($value);
        $this->value = $value;
    }

    public static function next(): self
    {
        $time = explode(' ', microtime());
        return new self($time[1].substr($time[0], 3, -2));
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}