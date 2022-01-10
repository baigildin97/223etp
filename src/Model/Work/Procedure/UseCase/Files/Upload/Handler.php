<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Files\Upload;


use App\Model\Flusher;
use App\Model\Work\Procedure\Entity\Document\FileType;
use App\Model\Work\Procedure\Entity\Document\ProcedureDocument;
use App\Model\Work\Procedure\Entity\Document\Status;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use App\Model\Work\Procedure\Entity\Document\Id as FileId;
use App\Model\Work\Procedure\Entity\Document\File;
class Handler
{
    private $flusher;

    private $procedureRepository;

    public function __construct(Flusher $flusher, ProcedureRepository  $procedureRepository){
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
    }

    public function handle(Command $command): void{
        $procedure = $this->procedureRepository->get(new Id($command->procedureId));

        $procedure->addFile(new ProcedureDocument(
            FileId::next(),
            $procedure,
            new File(
                $command->file->path,
                $command->file->name,
                $command->file->size,
                $command->fileTitle,
                $command->file->hash
            ),
            new \DateTimeImmutable(),
            Status::new(),
            new FileType($command->fileType)

        ));

        $this->flusher->flush();

    }
}
