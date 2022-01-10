<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Document\Delete;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Document\Id;
use App\Model\User\Entity\Profile\Document\ProfileDocumentsRepository;


/**
 * Class Handler
 * @package App\Model\User\UseCase\Profile\Document\Delete
 */
class Handler
{
    /**
     * @var ProfileDocumentsRepository
     */
    private $profileFileRepository;

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * Handler constructor.
     * @param ProfileDocumentsRepository $profileFilesRepository
     * @param Flusher $flusher
     */
    public function __construct(ProfileDocumentsRepository $profileFilesRepository, Flusher $flusher) {
        $this->profileFileRepository = $profileFilesRepository;
        $this->flusher = $flusher;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void {
        $file = $this->profileFileRepository->get(new Id($command->fileId));

        $file->archived();

        $this->flusher->flush();
    }
}