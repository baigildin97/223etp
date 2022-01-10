<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Tariff\Edit;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Tariff\Id;
use App\Model\User\Entity\Profile\Tariff\TariffRepository;
use App\Model\User\Entity\Profile\Tariff\Status;

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

    /**
     * Handler constructor.
     * @param TariffRepository $rateRepository
     * @param Flusher $flusher
     */
    public function __construct(TariffRepository $rateRepository, Flusher $flusher) {
        $this->rateRepository = $rateRepository;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void {
        $rate = $this->rateRepository->get(new Id($command->tariffId));

        $rate->edit(
            $command->title,
            $command->cost,
            $command->period,
            new Status($command->status)
        );

        $this->flusher->flush();
    }

}