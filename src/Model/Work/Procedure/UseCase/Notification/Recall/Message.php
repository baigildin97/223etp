<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Notification\Recall;

use App\Model\User\Entity\User\Email;

/**
 * Class Message
 * @package App\Model\Work\Procedure\UseCase\Notification\Recall
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
    public static function cancellingPublication(Email $email,
                                              string $notificationNumber,
                                              int $procedureNumber,
                                              string $showProcedureUrl
    ): self
    {
        $message = new self($email);
        $message->subject = "Вами прозведена отмена публикации";
        $message->content = "Вами произведена отмена публикации извещения №{$notificationNumber} по торговой процедуре <a href=\"{$showProcedureUrl}\">№{$procedureNumber}</a>";

        return $message;
    }
}