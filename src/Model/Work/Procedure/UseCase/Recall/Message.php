<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Recall;

use App\Model\User\Entity\User\Email;

/**
 * Class Message
 * @package App\Model\Work\Procedure\UseCase\Recall
 */
class Message extends \App\Services\Notification\EmailMessageBase
{
    /**
     * @param Email $email
     * @param string $notificationNumber
     * @param int $procedureNumber
     * @param string $showProcedureUrl
     * @return static
     */
    public static function procedureRejectOrganizer(Email $email, string $notificationNumber, int $procedureNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Извещение отозвано";
        $message->content = "Ваше извещение №{$notificationNumber} о проведении торговой 
                             процедуры <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a>, отозвано";
        return $message;
    }
}