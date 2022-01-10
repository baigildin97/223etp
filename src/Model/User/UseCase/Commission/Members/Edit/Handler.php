<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Commission\Members\Edit;


use App\Model\Flusher;
use App\Model\User\Entity\Commission\Members\Id;
use App\Model\User\Entity\Commission\Members\MemberRepository;
use App\Model\User\Entity\Commission\Members\Status;

class Handler
{
    private $flusher;

    private $memberRepository;

    public function __construct(Flusher $flusher, MemberRepository $memberRepository) {
        $this->flusher = $flusher;
        $this->memberRepository = $memberRepository;
    }

    public function handle(Command $command): void {
        $member = $this->memberRepository->get(new Id($command->memberId));

        $member->edit(
            $command->lastName,
            $command->firstName,
            $command->middleName,
            $command->position,
            $command->role,
            new Status($command->status)
        );

        $this->flusher->flush();
    }

}