<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Payment\Requisite\Archived;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Requisite\Id;
use App\Model\User\Entity\Profile\Requisite\RequisiteRepository;

class Handler
{

    private $flusher;

    private $requisiteRepository;

    public function __construct(Flusher $flusher, RequisiteRepository $requisiteRepository) {
        $this->flusher = $flusher;
        $this->requisiteRepository = $requisiteRepository;
    }

    public function handle(Command $command): void {
        $requisite = $this->requisiteRepository->get(new Id($command->requisiteId));

        $requisite->archived();

        $this->flusher->flush();
    }
}