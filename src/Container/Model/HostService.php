<?php
declare(strict_types=1);
namespace App\Container\Model;


class HostService
{
    private $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function getBaseUrl(): string{
        return $this->baseUrl;
    }
}