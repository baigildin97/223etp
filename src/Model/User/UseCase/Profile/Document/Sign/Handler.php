<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\Profile\Document\Sign;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Document\Id;
use App\Model\User\Entity\Profile\Document\ProfileDocumentsRepository;
use App\Model\User\Entity\Profile\Document\Status;
use App\Services\CryptoPro\CryptoPro;

class Handler
{
    private $flusher;
    private $filesRepository;

    public function __construct(Flusher $flusher, ProfileDocumentsRepository $filesRepository)
    {
        $this->flusher = $flusher;
        $this->filesRepository = $filesRepository;
    }

    public function handle(Command $command): void
    {
        $file = $this->filesRepository->get(new Id($command->id));

        try {
            CryptoPro::verify($file->getHash(), $command->sign);
        }
        catch (\Exception $e) {
            throw new \DomainException('Созданная подпись не соответсвует выбранному файлу');
        }

        $file->sign($command->sign, $command->clientIp);
        $this->flusher->flush();
    }
}