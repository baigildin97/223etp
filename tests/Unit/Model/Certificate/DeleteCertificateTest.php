<?php


namespace App\Tests\Unit\Model\Certificate;


use App\Model\User\Entity\Certificate\Status;
use App\Tests\Builder\Certificate\CertificateBuilder;

class DeleteCertificateTest extends \PHPUnit\Framework\TestCase
{
    public function testSuccess() {
        self::assertEquals(Status::archived(), (new CertificateBuilder())->archived()->build()->getStatus());
    }
}