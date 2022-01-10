<?php
declare(strict_types=1);
namespace App\ReadModel\Certificate;


class ShortView
{
    public $id;
    public $thumbprint;
    public $owner;
    public $issuer;
    public $validFrom;
    public $validTo;
}