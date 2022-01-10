<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Commission\Commission\Edit;

use App\Model\Flusher;
use App\Model\User\Entity\Commission\Commission\CommissionRepository;
use App\Model\User\Entity\Commission\Commission\Id;
use App\Model\User\Entity\Commission\Commission\Status;

/**
 * Class Handler
 * @package App\Model\Profile\UseCase\Commission\Edit
 */
class Handler
{

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var CommissionRepository
     */
    private $commissionRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param CommissionRepository $commissionRepository
     */
    public function __construct(Flusher $flusher, CommissionRepository $commissionRepository) {
        $this->flusher = $flusher;
        $this->commissionRepository = $commissionRepository;
    }


    /**
     * @param Command $command
     * @throws \Exception
     */
    public function handle(Command $command): void {
        $commission = $this->commissionRepository->get(new Id($command->id));

        $commission->edit($command->title, new Status($command->status), new \DateTimeImmutable());

        $this->flusher->flush();
    }

}