<?php


namespace App\Services\Notification;


use App\Model\User\Entity\User\Email;
use Twig\Environment;

class EmailMessageBase implements EmailMessageInterface
{
    protected $mailTo;
    protected $content;
    protected $subject;

    protected function __construct(Email $email)
    {
        $this->mailTo = $email;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getMailTo(): Email
    {
        return $this->mailTo;
    }

    public function setMailTo(Email $email)
    {
        $this->mailTo = $email;
    }
}