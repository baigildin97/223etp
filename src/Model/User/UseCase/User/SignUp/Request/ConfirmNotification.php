<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\User\SignUp\Request;


use App\Model\User\Entity\User\Email;

class ConfirmNotification
{
    private $subject;

    private $content;

    private $mailTo;

    public function __construct(string $subject, string $content, Email $email)
    {
        $this->subject = $subject;
        $this->content = $content;
        $this->mailTo = $email;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return Email
     */
    public function getMailTo(): Email
    {
        return $this->mailTo;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }
}