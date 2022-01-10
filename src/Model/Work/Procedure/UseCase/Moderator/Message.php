<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Moderator;


use App\Model\User\Entity\User\Email;
use App\Services\Notification\EmailMessageBase;

class Message extends EmailMessageBase
{

    /**
     * @param Email $email
     * @param int $idNumber
     * @param string $xmlDocumentNumber
     * @param string $showProcedureUrl
     * @return static
     */
    public static function procedureApprove(
        Email $email,
        int $idNumber,
        string $xmlDocumentNumber,
        string $showProcedureUrl
    ): self {
        $message = new self($email);
        $message->subject = "Извещение №{$xmlDocumentNumber} одобрено.";
        $message->content = "Извещение №{$xmlDocumentNumber} по торговой процедуре <a href=\"{$showProcedureUrl}\">№$idNumber</a> одобрено. Для публикации на площадке, вам необходимо подписать извещение о проведении торговой процедуры";
        return $message;
    }

    /**
     * Отправляется модератору, после одобрения модерации процедуры
     * @param Email $email
     * @param int $idNumber
     * @param string $xmlDocumentNumber
     * @param string $showProcedureUrl
     * @return static
     */
    public static function procedureApproveModerator(Email $email, int $idNumber, string $xmlDocumentNumber, string $showProcedureUrl): self
    {
        $message = new self($email);
        $message->subject = "Извещение №{$xmlDocumentNumber} одобрено.";
        $message->content = "Извещение №{$xmlDocumentNumber} по торговой процедуре <a href=\"{$showProcedureUrl}\">№$idNumber</a> одобрено. Для публикации на площадке, ожидаем подписания извещения организатором.";
        return $message;
    }


    public static function procedureReject(Email $email, string $xmlDocumentNumber, int $idNumber, string $showProcedureUrl, string $cause): self
    {
        $message = new self($email);
        $message->subject = "Извещение №{$xmlDocumentNumber} отклонено.";
        $message->content = "Извещение №{$xmlDocumentNumber} по торговой процедуре <a href=\"{$showProcedureUrl}\">№$idNumber</a> отклонено. Причина: {$cause}";
        return $message;
    }

//    /**
//     * Отправляется модератору, после отклонения с модерации процедуры
//     * @param Email $email
//     * @param int $idNumber
//     * @param string $showProcedureUrl
//     * @return static
//     */
//    public static function procedureRejectModerator(Email $email, int $idNumber, string $xmlDocumentNumber, string $showProcedureUrl): self
//    {
//        $message = new self($email);
//        $message->subject = "Извещение №{$xmlDocumentNumber} отклонено.";
//        $message->content = "Извещение о проведении торговой процедуры <a href=\"{$showProcedureUrl}\">№$idNumber</a> отклонено. ";
//
//        return $message;
//    }

}