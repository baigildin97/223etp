<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Lot\Open\Bid\Upload;


use App\Model\Flusher;
use App\Model\Work\Procedure\Entity\Lot\Bid\BidRepository;
use App\Model\Work\Procedure\Entity\Lot\Bid\Document;
use App\Model\Work\Procedure\Entity\Lot\Bid\Id;

class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var BidRepository
     */
    private $bidRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param BidRepository $bidRepository
     */
    public function __construct(Flusher $flusher, BidRepository $bidRepository){
        $this->flusher = $flusher;
        $this->bidRepository = $bidRepository;
    }

    public function handle(Command $command): void{
        $bid = $this->bidRepository->get(new Id($command->bidId));

        $bid->addDocument(
                Document\Id::next(),
                Document\Status::new(),
                new Document\File (
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