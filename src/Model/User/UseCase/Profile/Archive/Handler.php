<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Archive;


use App\Model\Flusher;

class Handler
{
    private $profileRepository;
    private $flusher;

    public function __construct(ProfileRepository $profileRepository, Flusher $flusher) {
        $this->profileRepository = $profileRepository;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void {
        $profile = $this->profileRepository->get(new Id($command->profileId));
        $profile->archived();

        $this->flusher->flush();
    }
}