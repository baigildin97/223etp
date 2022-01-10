<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Files\Sign;


use App\Model\Flusher;
use App\Model\Work\Procedure\Entity\Document\Id;
use App\Model\Work\Procedure\Entity\Document\ProcedureDocumentRepository;
use App\Services\CryptoPro\CryptoPro;

class Handler
{
    private $flusher;
    private $filesRepository;

    public function __construct(Flusher $flusher, ProcedureDocumentRepository $filesRepository)
    {
        $this->flusher = $flusher;
        $this->filesRepository = $filesRepository;
    }

    public function handle(Command $command): void
    {
        $file = $this->filesRepository->get(new Id($command->id));

        try {
            CryptoPro::verify($file->getHash(), $command->sign);
        } catch (\Exception $e) {
            throw new \DomainException('Созданная подпись не соответсвует выбранному файлу');
        }

        $file->sign($command->sign, $command->clientIp,new \DateTimeImmutable());
        $this->flusher->flush();
    }
}