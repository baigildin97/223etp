<?php
declare(strict_types=1);

namespace App\Services\Notification;

use App\Model\User\Entity\User\Email;

interface EmailMessageInterface
{
    public function getSubject(): string;
    public function getContent(): string;
    public function getMailTo(): Email;
    public function setMailTo(Email $email);
}