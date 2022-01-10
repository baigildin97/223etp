<?php
declare(strict_types=1);
namespace App\Container\Model\User\Service;


use App\Model\User\Service\ResetTokenGenerator;
use DateInterval;
use Exception;

/**
 * Class ResetTokenGeneratorFactory
 * @package App\Container\Model\User\Service
 */
class ResetTokenGeneratorFactory
{
    /**
     * @param string $interval
     * @return ResetTokenGenerator
     * @throws Exception
     */
    public function create(string $interval): ResetTokenGenerator {
        return new ResetTokenGenerator(new DateInterval($interval));
    }
}