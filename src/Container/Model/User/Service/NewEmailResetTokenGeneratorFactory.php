<?php
declare(strict_types=1);
namespace App\Container\Model\User\Service;


use App\Model\User\Service\NewEmailTokenGenerator;
use DateInterval;
use Exception;

/**
 * Class NewEmailResetTokenGeneratorFactory
 * @package App\Container\Model\User\Service
 */
class NewEmailResetTokenGeneratorFactory
{
    /**
     * @param string $interval
     * @return NewEmailTokenGenerator
     * @throws Exception
     */
    public function create(string $interval): NewEmailTokenGenerator {
        return new NewEmailTokenGenerator(new DateInterval($interval));
    }
}