<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Certificate\Archive;


use App\Model\Flusher;
use App\Model\User\Entity\Certificate\CertificateRepository;
use App\Model\User\Entity\Certificate\Id;

class Handler
{
    private $certificateRepository;
    private $flusher;

    public function __construct(CertificateRepository $certificateRepository, Flusher $flusher) {
        $this->certificateRepository = $certificateRepository;
        $this->flusher = $flusher;
    }

    public function handle(Command $command): void {
        $certificate = $this->certificateRepository->get(new Id($command->certificateId));

        $certificate->archived();

        $this->flusher->flush();
    }
}