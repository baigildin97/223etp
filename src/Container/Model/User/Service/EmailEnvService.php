<?php


namespace App\Container\Model\User\Service;


class EmailEnvService
{
    private $mailFrom;
    private $mailerTlsCert;
    private $mailerTlsKey;

    public function __construct(string $mailFrom, string $mailerTlsCert, string $mailerTlsKey)
    {
        $this->mailFrom = $mailFrom;
        $this->mailerTlsCert = $mailerTlsCert;
        $this->mailerTlsKey = $mailerTlsKey;
    }

    public function getMailFrom(): string
    {
        return $this->mailFrom;
    }

    public function getMailTlsCert(): string
    {
        return $this->mailerTlsCert;
    }

    public function getMailTlsKey(): string
    {
        return $this->mailerTlsKey;
    }
}