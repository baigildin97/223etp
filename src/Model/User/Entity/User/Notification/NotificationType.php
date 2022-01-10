<?php
declare(strict_types=1);

namespace App\Model\User\Entity\User\Notification;


use Money\Money;

class NotificationType
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param $findSiteName
     * @param $urlConfirm
     * @return static
     */
    public static function confirmTokenSender($findSiteName, $urlConfirm): self
    {
        $data = [
            "subject" => "Подтверждение электронной почты",
            "text" => "Ваш адрес электронной почты был использован для регистрации нового пользователя на ЭТП {$findSiteName}.<br/>
            Если Вы заинтересованы в регистрации, пожалуйста, перейдите по <a href='{$urlConfirm}'>ссылке</a> для подтверждения адреса электронной почты.<br/>
            Ссылка действительна в течение 2 часов.
            "
        ];
        return new self($data);
    }

    /**
     * Отпрвляется пользователю о то что профиль ожидает модерации
     * @param $fullName
     * @param $findSiteName
     * @param $idBid
     * @param $accreditationPeriod
     * @param $getIncorporatedForm
     * @return static
     * TODO - строго типизировать!!!!
     */
    public static function userSingedProfile($fullName, $findSiteName, $idBid, $accreditationPeriod, $getIncorporatedForm): self
    {
        $data = [
            "subject" => "Ваше заявление на регистрацию отправлено",
            "text" => "
             Уважаемый(ая) $fullName. 
             Ваше заявление на регистрацию на ЭТП «{$findSiteName}» поставлено в очередь на рассмотрение Администратором системы. <br/>
             Ему присвоен номер №{$idBid}. По результатам проверки данных пользователя, в случае отклонения вашего заявления, предоставляется возможность редактирования данных и повторной подачи заявления. 
             <br/>
             Уведомление о результатах проверки ожидайте в течение {$accreditationPeriod} рабочих дней.
            "
        ];
        return new self($data);
    }

    /**
     * Отапавляется пользователю после повторной отправки заявление на редактирование профиля
     * @param int $idBid
     * @param string $findAccreditationPeriod
     * @param \DateTimeImmutable $createdAt
     * @return static
     */
    public static function userRepeatSingedProfile(int $idBid, string $findAccreditationPeriod, \DateTimeImmutable $createdAt): self
    {
        $data = [
            "subject" => "Вами отправлено заявление на редактирование регистрационных данных",
            "text" => "Вами отправлено заявление на редактирование регистрационных данных (входящий №{$idBid}) от " . $createdAt->format('d.m.Y') . ".
            Вы получите уведомление о результатах рассмотрения заявки в течение {$findAccreditationPeriod} рабочих дней.
            "
        ];
        return new self($data);
    }


    /**
     * Отапавляется пользователю после отправки заявления на замену Электронной подписи
     * @param int $idBid
     * @param string $findAccreditationPeriod
     * @param \DateTimeImmutable $createdAt
     * @return static
     */
    public static function userReplacingEp(int $idBid, string $findAccreditationPeriod, \DateTimeImmutable $createdAt): self
    {
        $data = [
            "subject" => "Вами отправлено заявление на замену электронной подписи",
            "text" => "Вами отправлено заявление на замену электронной подписи (входящий №{$idBid}) от " . $createdAt->format('d.m.Y') . ".
            Вы получите уведомление о результатах рассмотрения заявки в течение {$findAccreditationPeriod} рабочих дней."
        ];
        return new self($data);
    }


    /**
     * Отпрвляется модератору о то что пользователь ожидает модерации
     * @param $fullName
     * @return static
     */
    public static function userSingedModerateProfile($fullName): self
    {
        $data = [
            "subject" => "Пользователь {$fullName} ожидает модерации профиля",
            "text" => "Пользователь {$fullName} ожидает модерации профиля"
        ];
        return new self($data);
    }

    /**
     * Отпрвляется модератору о то что пользователь ожидает повторной модерации
     * @param $fullName
     * @return static
     */
    public static function userSingedModerateProfileRepeat($fullName): self
    {
        $data = [
            "subject" => "Пользователь {$fullName} ожидает повторной модерации профиля",
            "text" => "Пользователь {$fullName} ожидает повторной модерации профиля"
        ];
        return new self($data);
    }

    /**
     * Отпрвляется пользователю после отклонения модератором первичной регистрации
     * @param string $findSiteName
     * @param string $cause
     * @param int $bidId
     * @param \DateTimeImmutable $createdAt
     * @return static
     */
    public static function profileRejectUser(string $findSiteName, string $cause, int $bidId, \DateTimeImmutable $createdAt): self
    {
        $data = [
            "subject" => "Ваше заявление на регистрацию на ЭТП «{$findSiteName}» отклонено",
            "text" => "Ваше заявление (входящий №{$bidId}) от " . $createdAt->format('d.m.Y') . " на регистрацию на ЭТП «{$findSiteName}» отклонено. Причина: {$cause}.
             Вы можете повторно подать заявление на регистрацию, после устранения указанных оснований для отказа."
        ];
        return new self($data);
    }

    /**
     * Отпрвляется пользователю после отклонения модератором заявки на редактирование
     * @param string $findSiteName
     * @param string $cause
     * @return static
     */
    public static function profileEditRejectUser(string $findSiteName, string $cause): self
    {
        $data = [
            "subject" => "Заявление на изменение данных профиля пользователя отклонено.",
            "text" => "Заявление на изменение данных профиля пользователя отклонено. Причина: {$cause}. Вы можете повторно подать заявление на регистрацию, после устранения указанных оснований для отказа."
        ];
        return new self($data);
    }

    /**
     * Отпрвляется модераторм после отклонения профиля
     * @param string $fullName
     * @param string $cause
     * @return static
     */
    public static function profileRejectModerator(string $fullName, string $cause): self
    {
        $data = [
            "subject" => "Мы отклонили профиль пользователя {$fullName}",
            "text" => "Мы отклонили профиль пользователя: {$fullName} Причина: {$cause}"
        ];
        return new self($data);
    }

    /**
     * Отпрвляется модераторм после отклонения заявки на редактироване
     * @param string $fullName
     * @param string $cause
     * @return static
     */
    public static function profileEditRejectModerator(string $fullName, string $cause): self
    {
        $data = [
            "subject" => "Мы отклонили заявку на редактироване пользователя {$fullName}",
            "text" => "Мы отклонили заявку на редактироване пользователя: {$fullName} Причина: {$cause}"
        ];
        return new self($data);
    }


    /**
     * Отправляется пользователю после успешной регистрации профиля
     * @param string $findSiteName
     * @return static
     */
    public static function profileSuccessRegister(string $findSiteName): self
    {
        $data = [
            "subject" => "Вы зарегистрированы на электронной торговой площадке {$findSiteName}",
            "text" => "Вы зарегистрированы на электронной торговой площадке {$findSiteName}.
             Для полного доступа к функционалу системы необходимо заполнить банковские реквизиты во вкладке «Банковские реквизиты» и подписать договор."
        ];
        return new self($data);
    }

    /**
     * Отправляется организатору после успешной регистрации профиля
     * @param string $findSiteName
     * @param string $findFullNameOrganization
     * @return static
     */
    public static function profileSuccessRegisterOrganizer(string $findSiteName, string $findFullNameOrganization): self
    {
        $data = [
            "subject" => "Вы зарегистрированы на электронной торговой площадке {$findSiteName} как Организатор торгов",
            "text" => "Вы зарегистрированы на электронной торговой площадке {$findSiteName} как Организатор торгов.
             Для полного доступа к функционалу системы необходимо добавиь банковские реквизиты во разделе «Банковские реквизиты», а также подписать договор с Оператором {$findFullNameOrganization}."
        ];
        return new self($data);
    }


    /**
     * Отправляется пользователю после успешной активации после редактирования профиля
     * @param string $findSiteName
     * @return static
     */
    public static function profileSuccessEdit(string $findSiteName): self
    {
        $data = [
            "subject" => "Активация личного кабинета произведена.",
            "text" => "Данные профиля пользователя успешно изменены. Активация личного кабинета произведена."
        ];
        return new self($data);
    }

    /**
     * Отпрвляется модераторм после одобрения профиля
     * @param string $fullName
     * @param string $cause
     * @return static
     */
    public static function profileSuccessRegisterModerator(string $fullName): self
    {
        $data = [
            "subject" => "Профиль пользователя {$fullName} одобрен",
            "text" => "Профиль пользователя {$fullName} одобрен",
        ];
        return new self($data);
    }

    /**
     * Отпрвляется модераторм после одобрения заявки на редактироване
     * @param string $fullName
     * @param string $cause
     * @return static
     */
    public static function profileSuccessEditModerator(string $fullName): self
    {
        $data = [
            "subject" => "Заявление на редактирование данных пользователя {$fullName} одобрено.",
            "text" => "Заявление на редактирование данных пользователя {$fullName} одобрено.",
        ];
        return new self($data);
    }

    /**
     * Отпрвляется модераторм после одобрения заявки на редактироване
     * @param string $fullName
     * @return static
     */
    public static function profileReplacingEpModerator(string $fullName): self
    {
        $data = [
            "subject" => "Заявление на замену электронной подписи пользователя {$fullName} одобрено.",
            "text" => "Заявление на замену электронной подписи пользователя {$fullName} одобрено.",
        ];
        return new self($data);
    }

    /**
     * Отправляется пользователю после покупки тарифа
     * @param string $fullName
     * @param string $nameTarff
     * @return static
     */
    public static function buyTariff(string $fullName, string $nameTarff): self
    {
        $data = [
            "subject" => "Покупка тарифа",
            "text" => "Уважаемый, {$fullName}, Тариф {$nameTarff} успешно активирован"
        ];
        return new self($data);
    }

    /**
     * Отправляется пользователю после смены ЭЦП
     * @param string $findSiteName
     * @param string $fullName
     * @param string $findAccreditationPeriod
     * @return static
     */
    public static function resetCertificateProfile(string $findSiteName, string $fullName, string $findAccreditationPeriod): self
    {
        $data = [
            "subject" => "Ваш профиль на электронной торговой площадке {$findSiteName} был изменен",
            "text" => "Уважаемый(ая) {$fullName}, Ваш профиль на электронной торговой площадке {$findSiteName} был изменен и поставлен в очередь на рассмотрение. Вы получите уведомление о результатах рассмотрения заявки в течение {$findAccreditationPeriod} рабочих дней"
        ];
        return new self($data);
    }

    /**
     * Отправляется адмнистратором и модераторам после смены ЭЦП пользователем
     * @param string $fullName
     * @return static
     */
    public static function resetCertificateProfileModerate(string $fullName): self
    {
        $data = [
            "subject" => "Пользователь: {$fullName} сменил(-а) сертификат профиля",
            "text" => "Пользователь: {$fullName} сменил(-а) сертификат профиля"
        ];
        return new self($data);
    }


    /**
     * Отправляется организатору после отправки процедуры на модерацию
     * @param int $idNumber
     * @return static
     */
    public static function procedureModerate(int $idNumber): self
    {
        $data = [
            "subject" => "Ваше заявление на процедуру №{$idNumber} ожидает модерации",
            "text" => "Ваше заявление на процедуру №{$idNumber} ожидает модерации"
        ];
        return new self($data);
    }

    /**
     * Отправляется модератором, о том что создана новая процедура
     * @param int $idNumber
     * @return static
     */
    public static function procedureCreate(int $idNumber): self
    {
        $data = [
            "subject" => "Создана новая процедура №{$idNumber}",
            "text" => "Создана новая процедура №{$idNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется модератором, о том что процедура ожидает повторной модерации
     * @param int $idNumber
     * @return static
     */
    public static function procedureModerateRepeat(int $idNumber): self
    {
        $data = [
            "subject" => "Процедура №{$idNumber} ожидает повторной модерации",
            "text" => "Процедура №{$idNumber} ожидает повторной модерации"
        ];
        return new self($data);
    }

    /**
     * Отправляется организатору, после отклонения модерации процедуры
     * @param int $idNumber
     * @param string $findSiteName
     * @param string $cause
     * @return static
     */
    public static function procedureReject(int $idNumber, string $findSiteName, string $cause): self
    {
        $data = [
            "subject" => "Ваше заявление №{$idNumber} на организацию торгов отклонена",
            "text" => "Ваше заявление №{$idNumber} на организацию торгов на электронной торговой площадке {$findSiteName} отклонена. Причина: {$cause}"
        ];
        return new self($data);
    }

    /**
     * Отправляется модератору, после отклонения с модерации процедуры
     * @param int $idNumber
     * @return static
     */
    public static function procedureRejectModerator(int $idNumber): self
    {
        $data = [
            "subject" => "Извещение о проведении торговой процедуры №{$idNumber}, отклонено",
            "text" => "Извещение о проведении торговой процедуры №{$idNumber}, отклонено",
        ];
        return new self($data);
    }

    /**
     * Отправляется организатору, после одобрения с модерации процедуры
     * @param int $idNumber
     * @param string $findSiteName
     * @return static
     */
    public static function procedureApprove(int $idNumber, string $findSiteName): self
    {
        $data = [
            "subject" => "Ваше заявление №{$idNumber} на организацию торгов одобрена",
            "text" => "Ваше заявление №{$idNumber} на организацию торгов на электронной торговой площадке «{$findSiteName}» одобрена. Чтобы опубликовать процедуру вам необходимо подписать извещение о проведении торговой процедуры",
        ];
        return new self($data);
    }

    /**
     * Отправляется модератором, после одобрения с модерации процедуры
     * @param int $idNumber
     * @return static
     */
    public static function procedureApproveModerator(int $idNumber): self
    {
        $data = [
            "subject" => "Извещение о проведении торговой процедуры №{$idNumber}, одобрено",
            "text" => "Извещение о проведении торговой процедуры №{$idNumber}, одобрено"
        ];
        return new self($data);
    }

    /**
     * Отправляется модератором, после отзыва с модерации процедуры организатором
     * @param int $idNumber
     * @return static
     */
    public static function procedureRejectedOrganizer(int $idNumber): self
    {
        $data = [
            "subject" => "Извещение о проведении торговой процедуры №{$idNumber}, отозвано организатором",
            "text" => "Извещение о проведении торговой процедуры №{$idNumber}, отозвано организатором"
        ];
        return new self($data);
    }

    /**
     * Отправляется организатору, после подписание извещения о проведения торгов
     * @param int $idNumber
     * @param string $findSiteName
     * @return static
     */
    public static function procedurePublished(int $idNumber, string $findSiteName): self
    {
        $data = [
            "subject" => "Ваше процедура опубликована",
            "text" => "Ваше процедура №{$idNumber} на электронной торговой площадке {$findSiteName}, опубликована",
        ];
        return new self($data);
    }

    /**
     * Отправляется участнику, после пополнения виртуального счета
     * @param string $sum
     * @return static
     */
    public static function virtualBalanceReplenished(string $sum): self
    {
        $data = [
            "subject" => "Баланс вашего виртуального счета пополнен",
            "text" => "Баланс вашего виртуального счета пополнен на сумму {$sum}"
        ];
        return new self($data);
    }

    /**
     * Отправляется модератором, после пополнения виртуального счета
     * @param string $fullName
     * @return static
     */
    public static function virtualBalanceReplenishedModerator(string $fullName): self
    {
        $data = [
            "subject" => "Заявление на зачисление средств участника {$fullName}, исполнено",
            "text" => "Заявление на зачисление средств участника {$fullName}, исполнено"
        ];
        return new self($data);
    }

    /**
     * Отправляется участнику, после отклонения транзакции вывода
     * @param string $idNumber
     * @return static
     */
    public static function virtualBalanceReject(string $idNumber): self
    {
        $data = [
            "subject" => "Заявление №{$idNumber} на вывод средств, отклонено",
            "text" => "Заявление №{$idNumber} на вывод средств, отклонено"
        ];
        return new self($data);
    }

    /**
     * Отправляется модератором, после отклонения транзакции вывода
     * @param string $fullName
     * @return static
     */
    public static function virtualBalanceRejectModerator(string $fullName): self
    {
        $data = [
            "subject" => "Заявление на вывод средств участника {$fullName}, отклонено",
            "text" => "Заявление на вывод средств участника {$fullName}, отклонено"
        ];
        return new self($data);
    }

    /**
     * Отправляется участнику, после одобрения транзакции вывода
     * @param string $idNumber
     * @return static
     */
    public static function virtualBalanceApprove(string $idNumber): self
    {
        $data = [
            "subject" => "Заявление №{$idNumber} на вывод средств, исполнено",
            "text" => "Заявление №{$idNumber} на вывод средств, исполнено"
        ];
        return new self($data);
    }

    /**
     * Отправляется модератором, после одобрения транзакции вывода
     * @param string $fullName
     * @return static
     */
    public static function virtualBalanceApproveModerator(string $fullName): self
    {
        $data = [
            "subject" => "Заявление на вывод средств участника {$fullName}, исполнено",
            "text" => "Заявление на вывод средств участника {$fullName}, исполнено"
        ];
        return new self($data);
    }

    /**
     * Отправляется модератором, после заявки на вывод сресдтв
     * @param string $fullName
     * @return static
     */
    public static function withdrawModerator(string $fullName): self
    {
        $data = [
            "subject" => "Участник {$fullName}, подал заявление на вывод средств",
            "text" => "Участник {$fullName}, подал заявление на вывод средств"
        ];
        return new self($data);
    }

    /**
     * Отправляется участнику, после подачи заявки на аукцион
     * @param string $procedureNumber
     * @param int $bidNumber
     * @return static
     */
    public static function createBid(string $procedureNumber, int $bidNumber): self
    {
        $data = [
            "subject" => "Вы подали заявку на участие",
            "text" => "Вы подали заявку на участие в процедуре №{$procedureNumber}. Вашей заявке присвоен номер: №$bidNumber"
        ];
        return new self($data);
    }

    /**
     * Отправляется участнику, после подачи заявки на аукцион
     * @param string $procedureFullNumber
     * @param int $bidNumber
     * @param float $levelPercent
     * @param string $initialLotPrice
     * @param string $typeNds
     * @return static
     */
    public static function blockedFunds(
        string $procedureFullNumber,
        int $bidNumber,
        float $levelPercent,
        string $initialLotPrice,
        string $typeNds
    ): self
    {
        $data = [
            "subject" => "Средства гарантийного обеспечения для участия в торгах №{$procedureFullNumber}, заблокированы на вашем виртуальном счете",
            "text" => "Средства гарантийного обеспечения для участия в торгах №{$procedureFullNumber} по заявке №{$bidNumber}, в размере {$levelPercent}% от начальной (минимальной) цены имущества, в сумме {$initialLotPrice} ({$typeNds}), заблокированы на вашем виртуальном счете"
        ];
        return new self($data);
    }


    /**
     * Отправляется участнику, после разблокировки заблокированных средств
     * @param string $procedureFullNumber
     * @param int $bidNumber
     * @param float $levelPercent
     * @param string $initialLotPrice
     * @param string $typeNds
     * @return static
     */
    public static function blockedFundsUnblocked(
        string $procedureFullNumber,
        int $bidNumber,
        float $levelPercent,
        string $initialLotPrice,
        string $typeNds
    ): self
    {
        $data = [
            "subject" => "Разблокирование средств гарантийного обеспечения",
            "text" => "Средства гарантийного обеспечения для участия в торгах №{$procedureFullNumber} по заявке №{$bidNumber}, в размере {$levelPercent}% от начальной (минимальной) цены имущества, в сумме {$initialLotPrice} ({$typeNds}), разблокированы на вашем виртуальном счете"
        ];
        return new self($data);
    }


    /**
     * Отправляется организатору, после подачи заявки участникам
     * @param int $bidNumber
     * @param string $auctionNumber
     * @return static
     */
    public static function newBidOrganizer(int $bidNumber, string $auctionNumber): self
    {
        $data = [
            "subject" => "Претендентом подана заявка №{$bidNumber} на участие в торгах №{$auctionNumber}",
            "text" => "Претендентом подана заявка №{$bidNumber} на участие в торгах №{$auctionNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется участнику, после отзывы заявки
     * @param int $bidNumber
     * @return static
     */
    public static function bidRecall(int $bidNumber): self
    {
        $data = [
            "subject" => "Вами отозвана ваша заявка №{$bidNumber} с модерации",
            "text" => "Вами отозвана ваша заявка №{$bidNumber} с модерации"
        ];
        return new self($data);
    }

    /**
     * Отправляется организатору, после отзывы заявки участником
     * @param int $bidNumber
     * @param string $procedureFullNumber
     * @return static
     */
    public static function bidRecallOrganizer(int $bidNumber, string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Отозвана заявка №{$bidNumber} по вашей процедуре №{$procedureFullNumber}",
            "text" => "Отозвана заявка №{$bidNumber} по вашей процедуре №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется пользователю отозвал свою заявку на регистрацию
     * @param int $number
     * @param \DateTimeImmutable $createdAt
     * @return static
     */
    public static function profileXmlDocumentRegistrationRecallUser(int $number, \DateTimeImmutable $createdAt): self
    {
        $data = [
            "subject" => "Ваше заявление на регистрацию (входящий №{$number}) от {$createdAt->format('d.m.Y')} отозвано.",
            "text" => "Ваше заявление на регистрацию (входящий №{$number}) от {$createdAt->format('d.m.Y')} отозвано."
        ];
        return new self($data);
    }

    /**
     * Отправляется пользователю отозвал свою заявку на изменение данных профиля
     * @param int $number
     * @param \DateTimeImmutable $createdAt
     * @return static
     */
    public static function profileXmlDocumentEditRecallUser(int $number, \DateTimeImmutable $createdAt): self
    {
        $data = [
            "subject" => "Ваше заявление на изменение данных пользователя (входящий №{$number}) от {$createdAt->format('d.m.Y')} отозвано.",
            "text" => "Ваше заявление на изменение данных пользователя (входящий №{$number}) от {$createdAt->format('d.m.Y')} отозвано."
        ];
        return new self($data);
    }

    /**
     * Пользователь отозвал свою заявку на замену электронной подписи
     * @param int $number
     * @param \DateTimeImmutable $createdAt
     * @return static
     */
    public static function profileXmlDocumentReplacingEp(int $number, \DateTimeImmutable $createdAt): self
    {
        $data = [
            "subject" => "Ваше заявление на замену элекутронной подписи данных пользователя (входящий №{$number}) от {$createdAt->format('d.m.Y')} отозвано.",
            "text" => "Ваше заявление на замену элекутронной подписи данных пользователя (входящий №{$number}) от {$createdAt->format('d.m.Y')} отозвано."
        ];
        return new self($data);
    }


    /**
     * Отправляется организатору, после подписание договора о задатке
     * @param int $bidNumber
     * @param string $procedureFullNumber
     * @return static
     */
    public static function signedDepositAgreementOrganizer(int $bidNumber, string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Вы подписали договор о задатке по заявке №{$bidNumber} Процедура №{$procedureFullNumber}",
            "text" => "Вы подписали договор о задатке по заявке №{$bidNumber} Процедура №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется участнику, после подписание договора о задатке организатором
     * @param string $procedureFullNumber
     * @return static
     */
    public static function signedDepositAgreement(string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Организатор подписал договор о задатке",
            "text" => "Организатор подписал договор о задатке. Процедура №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется организатору, после одобрения заявки на участие
     * @param int $bidNumber
     * @param string $procedureFullNumber
     * @return static
     */
    public static function organizerApproveBid(int $bidNumber, string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Вы допустили заявку №{$bidNumber}",
            "text" => "Вы допустили заявку №{$bidNumber}. Процедура №{$procedureFullNumber}"
        ];
        return new self($data);
    }


    /**
     * Отправляется организатору, после отклонения заявки на участие
     * @param int $bidNumber
     * @param string $procedureFullNumber
     * @return static
     */
    public static function organizerRejectBid(int $bidNumber, string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Вы отклонинили заявку №{$bidNumber}",
            "text" => "Вы отклонинили заявку №{$bidNumber}. Процедура №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется участнику, после одобрения его заявки организатором на участие
     * @param int $bidNumber
     * @param string $procedureFullNumber
     * @return static
     */
    public static function participantApproveBid(int $bidNumber, string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Ваша заявка №{$bidNumber} допущена",
            "text" => "Ваша заявка №{$bidNumber} на участие в процедуре №{$procedureFullNumber} допущена"
        ];
        return new self($data);
    }

    /**
     * Отправляется участнику, после отклонения заявки организатором на участие
     * @param int $bidNumber
     * @param string $procedureFullNumber
     * @return static
     */
    public static function participantRejectBid(int $bidNumber, string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Ваша заявка №{$bidNumber} отклонена",
            "text" => "Ваша заявка №{$bidNumber} на участие в процедуре №{$procedureFullNumber} отклонена"
        ];
        return new self($data);
    }

    /**
     * Отправляется организатору, после началы аукциона
     * @param string $procedureFullNumber
     * @return static
     */
    public static function startAuction(string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Торги по процедуре №{$procedureFullNumber} начались",
            "text" => "Торги по процедуре №{$procedureFullNumber} начались",
        ];
        return new self($data);
    }


    /**
     * Отправляется участникам, после создание протокола о подведении итогов приема и регистрации заявок
     * @param string $procedureFullNumber
     * @return static
     */
    public static function participantPublishedProtocolSummingRegProcedure(string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Организатор опубликовал протокол о подведении итогов приема и регистрации заявок по торговой процедуре №{$procedureFullNumber}",
            "text" => "Организатор опубликовал протокол о подведении итогов приема и регистрации заявок по торговой процедуре №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется организатору, после создание протокола о подведении итогов приема и регистрации заявок
     * @param string $procedureFullNumber
     * @return static
     */
    public static function organizerPublishedProtocolSummingRegProcedure(string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Организатор опубликовал протокол о подведении итогов приема и регистрации заявок по торговой процедуре №{$procedureFullNumber}",
            "text" => "Организатор опубликовал протокол о подведении итогов приема и регистрации заявок по торговой процедуре №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется участникам, после создание протокола о результатах торгов
     * @param string $procedureFullNumber
     * @return static
     */
    public static function organizerPublishedProtocolResults(string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Организатор опубликовал протокол об результатах торгов по торговой процедуре №{$procedureFullNumber}",
            "text" => "Организатор опубликовал протокол об результатах торгов по торговой процедуре №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется участникам, после создание протокола об определения победителя
     * @param string $procedureFullNumber
     * @return static
     */
    public static function participantPublishedProtocolWinner(string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Организатор опубликовал протокол об определении победителя торгов по торговой процедуре №{$procedureFullNumber}",
            "text" => "Организатор опубликовал протокол об определении победителя торгов по торговой процедуре №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется организатору, после подписание протокола о результатах торгов
     * @param string $procedureFullNumber
     * @return static
     */
    public static function organizerSignedProtocolResults(string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Вы подписали протокол о результатах торгов по торговой процедуре №{$procedureFullNumber}",
            "text" => "Вы подписали протокол о результатах торгов по торговой процедуре №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется участнику, после создание протокола о результатах торгов
     * @param int $place
     * @param string $procedureFullNumber
     * @return static
     */
    public static function setPlaceParticipant(int $place, string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Вашей заявке присвоено место №{$place}",
            "text" => "Вашей заявке присвоено место №{$place} по процедуре №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется участнику(победителю), после создание протокола о результатах торгов
     * @param string $procedureFullNumber
     * @return static
     */
    public static function publishedProtocolWinner(string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Организатор опубликовал протокол о результатах торгов, вам необходимо подписать протокол по торговой процедуре №{$procedureFullNumber}",
            "text" => "Организатор опубликовал протокол о результатах торгов, вам необходимо подписать протокол по торговой процедуре №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется победилю, после снятие средст со счета средст
     * @param string $procedureFullNumber
     * @param string $initialLotPrice
     * @return static
     */
    public static function payOperatorServices(string $procedureFullNumber,
                                               string $initialLotPrice): self
    {
        $data = [
            "subject" => "Списание средств в счет оплаты услуг Оператора электронной площадки победителем осуществлено",
            "text" => "Списание средств в счет оплаты услуг Оператора электронной площадки победителем по торговой процедуре №{$procedureFullNumber} в сумме {$initialLotPrice} осуществлено"
        ];
        return new self($data);
    }

    /**
     * Отправляется организатору, после публикации протоколо об результатах торгов
     * @param string $procedureFullNumber
     * @return static
     */
    public static function organizerPublishedProtocol(string $procedureFullNumber)
    {
        $data = [
            "subject" => "Вы опубликовали протокол о результатах торгов по торговой процедуре №{$procedureFullNumber}",
            "text" => "Вы опубликовали протокол о результатах торгов по торговой процедуре №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется организатору, после окончание рассмотрения заявок
     * @param string $procedureFullNumber
     * @return static
     */
    public static function organizerProtocolSummingRegProcedure(string $procedureFullNumber)
    {
        $data = [
            "subject" => "Необходимо подписать протокол о подведении итогов приема и регистрации заявок",
            "text" => "Необходимо подписать протокол о подведении итогов приема и регистрации заявок по торговой процедуре №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется организатору, после завершение аукциона
     * @param string $procedureFullNumber
     * @return static
     */
    public static function organizerCompletedAuction(string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Торги по процедуре №{$procedureFullNumber} завершены, Вам необходимо подписать протокол о результатах торгов",
            "text" => "Торги по процедуре №{$procedureFullNumber} завершены, Вам необходимо подписать протокол о результатах торгов",
        ];
        return new self($data);
    }


    /**
     * Отправляется организатору, после публикации протоколоа об определении победителя торгов
     * @param string $procedureFullNumber
     * @return static
     */
    public static function organizerPublishedProtocolWinner(string $procedureFullNumber)
    {
        $data = [
            "subject" => "Вы опубликовали протокол об определении победителя торгов по торговой процедуре №{$procedureFullNumber}",
            "text" => "Вы опубликовали протокол об определении победителя торгов по торговой процедуре №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется организатору, после подписание протокола победителем
     * @param string $procedureFullNumber
     * @return static
     */
    public static function organizerSignedProtocolWinner(string $procedureFullNumber)
    {
        $data = [
            "subject" => "Победитель подписал протокол о результатах торгов по торговой процедуре №{$procedureFullNumber}",
            "text" => "Победитель подписал протокол о результатах торгов по торговой процедуре №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется участнику, после подписание протокола победителем
     * @param string $procedureFullNumber
     * @return static
     */
    public static function participantSignedProtocolWinner(string $procedureFullNumber)
    {
        $data = [
            "subject" => "Вы подписал протокол о результатах торгов",
            "text" => "Вы подписал протокол о результатах торгов по торговой процедуре №{$procedureFullNumber}"
        ];
        return new self($data);
    }

    /**
     * Отправляется пользовталею после подтверждения email адреса.
     * @param string $findSiteName
     * @return NotificationType
     */
    public static function firstStageSignup(string $findSiteName)
    {
        $data = [
            "subject" => "Первый этап регистрации на электронной торговой площадке '{$findSiteName}'",
            "text" => "Вы прошли первый этап регистрации
             на ЭТП '{$findSiteName}' - создание учетной записи пользователя. Для подключения полного функционала системы,
             необходимо пройти следующий этап - заполнение и создание профиля пользователя. 
              "
        ];
        return new self($data);
    }

    /**
     * Отправляется участнику, после завершение аукциона
     * @param string $procedureFullNumber
     * @return static
     */
    public static function completedAuction(string $procedureFullNumber): self
    {
        $data = [
            "subject" => "Торги по процедуре №{$procedureFullNumber} завершены",
            "text" => "Торги по процедуре №{$procedureFullNumber} завершены",
        ];
        return new self($data);
    }

    /**
     * @param string $findNameOrganization
     * @param string $findSiteName
     * @return NotificationType
     */
    public static function signContract(string $findNameOrganization, string $findSiteName)
    {
        $data = [
            "subject" => "Договор с {$findNameOrganization} подписан.",
            "text" => "Договор с {$findNameOrganization} подписан. 
            Возможность размещать процедуры на ЭТП «{$findSiteName}», как Организатор торгов, доступна.",
        ];
        return new self($data);
    }

    /**
     * @param string $findNameOrganization
     * @param string $findSiteName
     * @return NotificationType
     */
    public static function cancellationContract(string $findNameOrganization, string $findSiteName)
    {
        $data = [
            "subject" => "Договор с {$findNameOrganization} истек.",
            "text" => "Договор с {$findNameOrganization} истек.",
        ];
        return new self($data);
    }
    /**
     * Отпрвка сообщение администратором
     * @param string $text
     * @return NotificationType
     */
    public static function sendMessage(string $text)
    {
        $data = [
            "subject" => "Сообщение от администратора",
            "text" => $text
        ];
        return new self($data);
    }


    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->data['subject'];
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->data['text'];
    }


}