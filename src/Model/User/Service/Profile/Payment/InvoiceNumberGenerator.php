<?php
declare(strict_types=1);
namespace App\Model\User\Service\Profile\Payment;



class InvoiceNumberGenerator
{
    public function generate(): string {
        try {
            return (string) random_int(100000, 999999);
        } catch (\Exception $e) {
            throw new \DomainException('Failed to generate a number');
        }
    }
}