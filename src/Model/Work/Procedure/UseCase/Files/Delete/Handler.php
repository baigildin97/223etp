<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Files\Delete;

use App\Model\Flusher;
use App\Model\Work\Procedure\Entity\Document\Id;
use App\Model\Work\Procedure\Entity\Document\ProcedureDocumentRepository;


/**
 * Class Handler
 * @package App\Model\Work\Procedure\UseCase\Files\Delete
 */
class Handler
{
    /**
     * @var ProcedureDocumentRepository
     */
    private $procedureFilesRepository;

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * Handler constructor.
     * @param ProcedureDocumentRepository $procedureFilesRepository
     * @param Flusher $flusher
     */
    public function __construct(ProcedureDocumentRepository $procedureFilesRepository, Flusher $flusher) {
        $this->procedureFilesRepository = $procedureFilesRepository;
        $this->flusher = $flusher;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void {
        $file = $this->procedureFilesRepository->get(new Id($command->fileId));

        $file->archived();

        $this->flusher->flush();
    }
}