<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Notification\Read;


use App\Model\Flusher;
use App\Model\User\Entity\User\Notification\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;

class Handler
{
    private $flusher;
    private $em;
    private $notificationRepository;

    public function __construct(EntityManagerInterface $em, Flusher $flusher, NotificationRepository $notificationRepository){
        $this->flusher = $flusher;
        $this->em = $em;
        $this->notificationRepository = $notificationRepository;
    }

    public function handle(Command $command){
        $notification = $this->notificationRepository->get($command->notification_id);
        $notification->read();
        $this->flusher->flush();
    }
}