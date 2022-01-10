<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Tariff\Levels\Create;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Tariff\Levels\Id;
use App\Model\User\Entity\Profile\Tariff\Levels\Level;
use App\Model\User\Entity\Profile\Tariff\Levels\LevelRepository;
use App\Model\User\Entity\Profile\Tariff\TariffRepository;
use Money\Currency;
use Money\Money;

class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var LevelRepository
     */
    private $levelRepository;

    /**
     * @var TariffRepository
     */
    private $tariffRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param LevelRepository $levelRepository
     * @param TariffRepository $tariffRepository
     */
    public function __construct(Flusher $flusher,
                                LevelRepository $levelRepository,
                                TariffRepository $tariffRepository
    ){
        $this->flusher = $flusher;
        $this->levelRepository = $levelRepository;
        $this->tariffRepository = $tariffRepository;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void{
        $tariff = $this->tariffRepository->get(new \App\Model\User\Entity\Profile\Tariff\Id($command->tariff_id));

        $level = new Level(
            Id::next(),
            $command->priority,
            $tariff,
            new Money(
                $command->amount,
                new Currency("RUB")),
            $command->percent,
            new \DateTimeImmutable()
        );

        $this->levelRepository->add($level);

        $this->flusher->flush();

    }
}