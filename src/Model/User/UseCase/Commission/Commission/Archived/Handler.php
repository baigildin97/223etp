<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Commission\Commission\Archived;



use App\Model\Flusher;
use App\Model\User\Entity\Commission\Commission\CommissionRepository;
use App\Model\User\Entity\Commission\Commission\Id;

class Handler
{

    private $flusher;

    private $commissionRepository;

    public function __construct(Flusher $flusher, CommissionRepository $commissionRepository) {
        $this->flusher = $flusher;
        $this->commissionRepository = $commissionRepository;
    }

    public function handle(Command $command): void {
        $commission = $this->commissionRepository->get(new Id($command->commissionId));

        $commission->archived();

        $this->flusher->flush();
    }

}