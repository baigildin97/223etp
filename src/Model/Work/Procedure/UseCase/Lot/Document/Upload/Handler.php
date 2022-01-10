<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Document\Upload;


use App\Model\Flusher;
use App\Model\Work\Procedure\Entity\Lot\Document\Status;
use App\Model\Work\Procedure\Entity\Lot\Id;
use App\Model\Work\Procedure\Entity\Lot\Lot;
use App\Model\Work\Procedure\Entity\Lot\LotRepository;

class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var LotRepository
     */
    private $lotRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param LotRepository $lotRepository
     */
    public function __construct(Flusher $flusher, LotRepository $lotRepository){
        $this->flusher = $flusher;
        $this->lotRepository = $lotRepository;
    }

    public function handle(Command $command): void{
        $lot = $this->lotRepository->get(new Id($command->lotId));

     /*   if ($command->userId !== $lot->getOrganizer()->getUser()->getId()->getValue()) {
            throw new \DomainException("You are not the organizer of this procedure");
        }*/

        $lot->addDocument(
                \App\Model\Work\Procedure\Entity\Lot\Document\Id::next(),
                Status::new(),
                new \App\Model\Work\Procedure\Entity\Lot\Document\File(
                    $command->file->path,
                    $command->file->name,
                    $command->file->size,
                    $command->file->realName,
                    $command->file->hash
                ),
                new \DateTimeImmutable(),
                $command->clientIp,
                $command->fileTitle
        );

        $this->flusher->flush();
    }
}