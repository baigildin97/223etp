<?php


namespace App\Container\Model\Certificate;


class CertificateService
{
    private $hash;
    private $captchaPubKey;

    public function __construct(string $hash, string $captchaPubKey)
    {
        $this->hash = $hash;
        $this->captchaPubKey = $captchaPubKey;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getCaptchaPubKey(): string
    {
        return $this->captchaPubKey;
    }
}