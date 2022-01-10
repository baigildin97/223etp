<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Commission\Commission\Create;


use App\Model\Flusher;
use App\Model\User\Entity\Commission\Commission\Commission;
use App\Model\User\Entity\Commission\Commission\CommissionRepository;
use App\Model\User\Entity\Commission\Commission\Id;
use App\Model\User\Entity\Commission\Commission\Status;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\Profile\Id as ProfileId;

class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var CommissionRepository
     */
    private $commissionRepository;

    /**
     * @var ProfileRepository
     */
    private $profileRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param CommissionRepository $commissionRepository
     * @param ProfileRepository $profileRepository
     */
    public function __construct(Flusher $flusher, CommissionRepository $commissionRepository, ProfileRepository $profileRepository) {
        $this->flusher = $flusher;
        $this->commissionRepository = $commissionRepository;
        $this->profileRepository = $profileRepository;
    }

    /**
     * @param Command $command
     * @throws \Exception
     */
    public function handle(Command $command): void {
        $profile = $this->profileRepository->get(new ProfileId($command->profileId));

        $commission = new Commission(
            Id::next(),
            new Status($command->status),
            $command->title,
            new \DateTimeImmutable(),
            $profile
        );

        $this->commissionRepository->add($commission);

        $this->flusher->flush();
    }

}