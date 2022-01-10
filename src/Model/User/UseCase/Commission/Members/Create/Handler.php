<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Commission\Members\Create;

use App\Model\Flusher;
use App\Model\User\Entity\Commission\Commission\CommissionRepository;
use App\Model\User\Entity\Commission\Members\Member;
use App\Model\User\Entity\Commission\Members\MemberRepository;
use App\Model\User\Entity\Commission\Members\Id as MemberId;
use App\Model\User\Entity\Commission\Commission\Id as CommissionId;
use App\Model\User\Entity\Commission\Members\Status;

/**
 * Class Handler
 * @package App\Model\Profile\UseCase\Commission\Members\Create
 */
class Handler
{

    private $flusher;

    private $memberRepository;

    private $commissionRepository;

    public function __construct(
        Flusher $flusher,
        MemberRepository $memberRepository,
        CommissionRepository $commissionRepository
    ) {
        $this->flusher = $flusher;
        $this->memberRepository = $memberRepository;
        $this->commissionRepository = $commissionRepository;
    }

    public function handle(Command $command): void {
        $commission = $this->commissionRepository->get(new CommissionId($command->commissionId));

        $member = new Member(
            MemberId::next(),
            new \DateTimeImmutable(),
            $command->lastName,
            $command->firstName,
            $command->middleName,
            $command->position,
            $command->role,
            new Status($command->status),
            $commission
        );
        $this->memberRepository->add($member);

        $this->flusher->flush();
    }

}