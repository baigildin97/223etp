<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Tariff\Create;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Tariff\Id;
use App\Model\User\Entity\Profile\Tariff\Tariff;
use App\Model\User\Entity\Profile\Tariff\TariffRepository;
use App\Model\User\Entity\Profile\Tariff\Status;
use Money\Currency;
use Money\Money;

class Handler
{
    /**
     * @var TariffRepository
     */
    private $rateRepository;

    /**
     * @var Flusher
     */
    private $flusher;

    public function __construct(TariffRepository $rateRepository, Flusher $flusher) {
        $this->rateRepository = $rateRepository;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void {
        $rate = new Tariff(
            Id::next(),
            $command->title,
            new Money(
                $command->cost,
                new Currency('RUB')
            ),
            $command->period,
            new Status($command->status),
            new \DateTimeImmutable(),
            $command->defaultPercent
        );

        $this->rateRepository->add($rate);

        $this->flusher->flush();
    }
}