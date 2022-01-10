<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Document\Delete;


use App\Model\Flusher;
use App\Model\Work\Procedure\Entity\Lot\Document\DocumentRepository;
use App\Model\Work\Procedure\Entity\Lot\Document\Id;

class Handler
{

    private $flusher;

    private $documentRepository;

    public function __construct(Flusher $flusher, DocumentRepository $documentRepository) {
        $this->flusher = $flusher;
        $this->documentRepository = $documentRepository;
    }

    public function handle(Command $command): void {
        $document = $this->documentRepository->get(new Id($command->documentId));

        if ($document->getLot()->getId()->getValue() !== $command->lotId){
            throw new \DomainException('Ошибка. Попробуйте еще раз.');
        }

        $document->archived();

        $this->flusher->flush();
    }

}