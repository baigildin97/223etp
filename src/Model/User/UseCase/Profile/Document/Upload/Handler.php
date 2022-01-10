<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Document\Upload;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Document\FileType;
use App\Model\User\Entity\Profile\Document\ProfileDocument;
use App\Model\User\Entity\Profile\Document\Status;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\Profile\Document\Id as FileId;
use App\Model\User\Entity\Profile\Document\File;

class Handler
{

    private $flusher;

    private $profileRepository;

    public function __construct(Flusher $flusher, ProfileRepository $profileRepository) {
        $this->flusher = $flusher;
        $this->profileRepository = $profileRepository;
    }

    public function handle(Command $command): void {
        $profile = $this->profileRepository->getByUser(new Id($command->userId));

        $profile->addFile(
            new ProfileDocument(
                FileId::next(),
                $profile,
                new FileType($command->fileType),
                new File(
                    $command->file->path,
                    $command->file->name,
                    $command->file->size,
                    $command->fileTitle,
                    $command->file->hash
                ),
                new \DateTimeImmutable(),
                Status::new()
            )
        );

        $this->flusher->flush();
    }

}