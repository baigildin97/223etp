<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Accredation\Recall;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\Profile\XmlDocument\Id;
use App\Model\User\Entity\Profile\XmlDocument\Status;
use App\Model\User\Entity\Profile\XmlDocument\StatusTactic;

// TODO - хэндлер не используется
class Handler
{
    /**
     * @var Flusher
     */
    public $flusher;

    /**
     * @var ProfileRepository
     */
    public $profileRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param ProfileRepository $profileRepository
     */
    public function __construct(Flusher $flusher, ProfileRepository $profileRepository) {
        $this->flusher = $flusher;
        $this->profileRepository = $profileRepository;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void {
        $profile = $this->profileRepository->get(new \App\Model\User\Entity\Profile\Id($command->profileId));


        $profile->recall(
            Id::next(),
            Status::signed(),
            $command->xml,
            $command->hash,
            $command->sign,
            new \DateTimeImmutable(),
            StatusTactic::recalled()
        );

        $this->flusher->flush();
    }
}