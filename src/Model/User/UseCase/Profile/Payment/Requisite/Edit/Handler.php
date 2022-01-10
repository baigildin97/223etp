<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Payment\Requisite\Edit;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Requisite\Id;
use App\Model\User\Entity\Profile\Requisite\RequisiteRepository;

/**
 * Class Handler
 * @package App\Model\User\UseCase\Profile\Payment\Requisite\Edit
 */
class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var RequisiteRepository
     */
    private $requisiteRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param RequisiteRepository $requisiteRepository
     */
    public function __construct(Flusher $flusher, RequisiteRepository $requisiteRepository) {
        $this->flusher = $flusher;
        $this->requisiteRepository = $requisiteRepository;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void {
        $requisite = $this->requisiteRepository->get(new Id($command->requisiteId));

        $requisite->edit(
            $command->bankName,
            $command->bankBik,
            $command->correspondentAccount,
            $command->paymentAccount,
            $command->personalAccount,
            $command->bankAddress
        );

        $this->flusher->flush();
    }

}