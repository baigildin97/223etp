<?php
declare(strict_types=1);
namespace App\Services\Hash;


class Streebog
{
    public function getHash(string $content): string
    {
        return hash('stribog256', $content);
       // return hash('sha256', $content);
    }

    public function getFileHash(string $fileName): string
    {
        return hash('stribog256', $fileName);
      //  return hash('sha256', $fileName);
    }

    public function verify(string $xml, string $hash): bool {
       return hash('stribog256', $xml) === $hash;
      // return hash('sha256', $xml) === $hash;
    }
}