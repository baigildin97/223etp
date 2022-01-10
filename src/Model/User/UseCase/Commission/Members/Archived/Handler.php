<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Commission\Members\Archived;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Members\MemberRepository;

/**
 * Class Handler
 * @package App\Model\Profile\UseCase\Commission\Members\Archived
 */
class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var MemberRepository
     */
    private $memberRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param MemberRepository $memberRepository
     */
    public function __construct(Flusher $flusher, MemberRepository $memberRepository) {
        $this->flusher = $flusher;
        $this->memberRepository = $memberRepository;
    }

    /**
     * @param Command $command
     */
    public function handle(Command $command): void {
        $member = $this->memberRepository->get(new Id($command->memberId));

        $member->archived();

        $this->flusher->flush();
    }

}