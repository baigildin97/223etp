<?php
declare(strict_types=1);
namespace App\Container\Model\SubscribeTariff;

/**
 * Class SubscribeTariffFactory
 * @package App\Container\Model\SubscribeTariff
 */
class SubscribeTariffFactory{

    private $defaultTariffId;

    /**
     * SubscribeTariffFactory constructor.
     * @param $defaultTariffId
     */
    public function __construct($defaultTariffId){
        $this->defaultTariffId = $defaultTariffId;
    }

    /**
     * @return string
     */
    public function create(): string{
        return $this->defaultTariffId;
    }
}