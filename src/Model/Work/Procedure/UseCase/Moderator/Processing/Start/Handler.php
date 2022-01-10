<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Moderator\Processing\Start;


use App\Model\Flusher;
use App\Model\User\Entity\User\Id;
use App\Model\User\Entity\User\UserRepository;
use App\Model\Work\Procedure\Entity\XmlDocument\XmlDocumentRepository;

class Handler
{

    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var XmlDocumentRepository
     */
    private $xmlDocumentRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param XmlDocumentRepository $xmlDocumentRepository
     * @param UserRepository $userRepository
     */
    public function __construct(Flusher $flusher, XmlDocumentRepository $xmlDocumentRepository, UserRepository $userRepository)
    {
        $this->flusher = $flusher;
        $this->xmlDocumentRepository = $xmlDocumentRepository;
        $this->userRepository = $userRepository;
    }

    public function handle(Command $command): void {
        $moderator = $this->userRepository->get(new Id($command->moderatorId));
        $xmlDocument = $this->xmlDocumentRepository->get(new \App\Model\Work\Procedure\Entity\XmlDocument\Id($command->xmlDocumentId));
        $xmlDocument->takeOnTask($moderator, $command->clientIp);
        $this->flusher->flush();
    }

}