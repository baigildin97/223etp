<?php
declare(strict_types=1);

namespace App\Model\Admin\Entity\Settings;


use Webmozart\Assert\Assert;

class Key
{
    private const KEY_NAME_ORGANIZATION = 'KEY_NAME_ORGANIZATION';
    private const KEY_FULL_NAME_ORGANIZATION = 'KEY_FULL_NAME_ORGANIZATION';
    private const KEY_INN_ORGANIZATION = 'KEY_INN_ORGANIZATION';
    private const KEY_KPP_ORGANIZATION = 'KEY_KPP_ORGANIZATION';
    private const KEY_CORRESPONDENT_ACCOUNT_ORGANIZATION = 'KEY_CORRESPONDENT_ACCOUNT_ORGANIZATION';
    private const KEY_BANK_NAME_ORGANIZATION = 'KEY_BANK_NAME_ORGANIZATION';
    private const KEY_BANK_BIK_ORGANIZATION = 'KEY_BANK_BIK_ORGANIZATION';
    private const KEY_BANK_CHECKING_ACCOUNT_ORGANIZATION = 'KEY_BANK_CHECKING_ACCOUNT_ORGANIZATION';
    private const KEY_NAME_SERVICE = 'KEY_NAME_SERVICE';
    private const KEY_PHONE_SERVICE = 'KEY_PHONE_SERVICE';
    private const KEY_EMAIL_SERVICE = 'KEY_EMAIL_SERVICE';
    private const KEY_ACCREDITATION_PERIOD = 'KEY_ACCREDITATION_PERIOD';
    private const KEY_ACCREDITATION_PROCEDURE_PERIOD = 'KEY_ACCREDITATION_PROCEDURE_PERIOD';
    private const KEY_BUY_SELLING_PROCEDURE_PERIOD = 'KEY_BUY_SELLING_PROCEDURE_PERIOD';
    private const KEY_SITE_DOMAIN = 'KEY_SITE_DOMAIN';
    private const KEY_WITHDRAW_PERIOD = 'KEY_WITHDRAW_PERIOD';
    private const KEY_PARTICIPATION_WITH_NEGATIVE_BALANCE = 'KEY_PARTICIPATION_WITH_NEGATIVE_BALANCE';
    private const KEY_CONFIRMATION_PARTICIPATION_AUCTION = 'KEY_CONFIRMATION_PARTICIPATION_AUCTION';
    private const KEY_REPLACING_EP_PERIOD = 'KEY_REPLACING_EP_PERIOD';

    private $name;

    public static $keys = [
        self::KEY_NAME_ORGANIZATION => 'ООО "ЕТП РБ"',
        self::KEY_FULL_NAME_ORGANIZATION => 'Полное наименование организации',
        self::KEY_INN_ORGANIZATION => 'ИНН организации',
        self::KEY_KPP_ORGANIZATION => 'КПП организации',
        self::KEY_CORRESPONDENT_ACCOUNT_ORGANIZATION => 'Корреспондентский счет организации',
        self::KEY_BANK_NAME_ORGANIZATION => 'Название банка организации',
        self::KEY_BANK_BIK_ORGANIZATION => 'БИК банка организации',
        self::KEY_BANK_CHECKING_ACCOUNT_ORGANIZATION => 'Расчетный счет организации',
        self::KEY_NAME_SERVICE => 'Название ЭТП',
        self::KEY_PHONE_SERVICE => 'Телефон ЭТП',
        self::KEY_EMAIL_SERVICE => 'EMAIL ЭТП',
        self::KEY_ACCREDITATION_PERIOD => "Время аккредитации профиля",
        self::KEY_ACCREDITATION_PROCEDURE_PERIOD => "Время модерации процедуры",
        self::KEY_BUY_SELLING_PROCEDURE_PERIOD => "Время подписание договора купли/продажи",
        self::KEY_SITE_DOMAIN => "Домен сайта",
        self::KEY_WITHDRAW_PERIOD => "Время на отзыва заявки на вывод средств(мин.)",
        self::KEY_PARTICIPATION_WITH_NEGATIVE_BALANCE => "Подача заявки на участие при отрицательном балансе",
        self::KEY_CONFIRMATION_PARTICIPATION_AUCTION => "Подтверждение участие в аукционе обязательно",
        self::KEY_REPLACING_EP_PERIOD => "Время рассмотрения заявления замены ЭП",
    ];

    public static $defaultKeys = [
        self::KEY_NAME_ORGANIZATION => 'ООО "ЕТП РБ"',
        self::KEY_FULL_NAME_ORGANIZATION => 'ООО "Единая Торговая Площадка Республики Башкортостан"',
        self::KEY_INN_ORGANIZATION => 'ИНН организации',
        self::KEY_KPP_ORGANIZATION => 'КПП организации',
        self::KEY_CORRESPONDENT_ACCOUNT_ORGANIZATION => 'Корреспондентский счет организации',
        self::KEY_BANK_NAME_ORGANIZATION => 'Название банка организации',
        self::KEY_BANK_BIK_ORGANIZATION => 'БИК банка организации',
        self::KEY_BANK_CHECKING_ACCOUNT_ORGANIZATION => 'Расчетный счет организации',
        self::KEY_NAME_SERVICE => '«РесТорг»',
        self::KEY_PHONE_SERVICE => 'Телефон ЭТП',
        self::KEY_EMAIL_SERVICE => 'admin@yandex.ru',
        self::KEY_ACCREDITATION_PERIOD => "5",
        self::KEY_ACCREDITATION_PROCEDURE_PERIOD => "3",
        self::KEY_BUY_SELLING_PROCEDURE_PERIOD => "5",
        self::KEY_SITE_DOMAIN => "https://229etp.ru",
        self::KEY_WITHDRAW_PERIOD => "15",
        self::KEY_PARTICIPATION_WITH_NEGATIVE_BALANCE => 'on',
        self::KEY_CONFIRMATION_PARTICIPATION_AUCTION => 'on',
        self::KEY_REPLACING_EP_PERIOD => '5'
    ];

    /**
     * Status constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        Assert::oneOf($name, [
            self::KEY_NAME_ORGANIZATION,
            self::KEY_FULL_NAME_ORGANIZATION,
            self::KEY_INN_ORGANIZATION,
            self::KEY_KPP_ORGANIZATION,
            self::KEY_CORRESPONDENT_ACCOUNT_ORGANIZATION,
            self::KEY_BANK_NAME_ORGANIZATION,
            self::KEY_BANK_BIK_ORGANIZATION,
            self::KEY_BANK_CHECKING_ACCOUNT_ORGANIZATION,
            self::KEY_NAME_SERVICE,
            self::KEY_PHONE_SERVICE,
            self::KEY_EMAIL_SERVICE,
            self::KEY_ACCREDITATION_PERIOD,
            self::KEY_ACCREDITATION_PROCEDURE_PERIOD,
            self::KEY_BUY_SELLING_PROCEDURE_PERIOD,
            self::KEY_SITE_DOMAIN,
            self::KEY_WITHDRAW_PERIOD,
            self::KEY_PARTICIPATION_WITH_NEGATIVE_BALANCE,
            self::KEY_CONFIRMATION_PARTICIPATION_AUCTION,
            self::KEY_REPLACING_EP_PERIOD

        ]);
        $this->name = $name;
    }

    public static function nameOrganization(): self
    {
        return new self(self::KEY_NAME_ORGANIZATION);
    }

    public static function fullNameOrganization(): self
    {
        return new self(self::KEY_FULL_NAME_ORGANIZATION);
    }

    public static function innOrganization(): self
    {
        return new self(self::KEY_INN_ORGANIZATION);
    }

    public static function kppOrganization(): self
    {
        return new self(self::KEY_KPP_ORGANIZATION);
    }

    public static function correspondentAccountOrganization(): self
    {
        return new self(self::KEY_CORRESPONDENT_ACCOUNT_ORGANIZATION);
    }

    public static function bankNameOrganization(): self
    {
        return new self(self::KEY_CORRESPONDENT_ACCOUNT_ORGANIZATION);
    }

    public static function bankBikOrganization(): self
    {
        return new self(self::KEY_CORRESPONDENT_ACCOUNT_ORGANIZATION);
    }

    public static function bankCheckingAccountOrganization(): self
    {
        return new self(self::KEY_CORRESPONDENT_ACCOUNT_ORGANIZATION);
    }

    public static function nameService(): self
    {
        return new self(self::KEY_NAME_SERVICE);
    }

    public static function emailService(): self
    {
        return new self(self::KEY_NAME_SERVICE);
    }

    public static function phoneService(): self
    {
        return new self(self::KEY_PHONE_SERVICE);
    }

    public static function accreditationPeriod(): self
    {
        return new self(self::KEY_ACCREDITATION_PERIOD);
    }

    public static function accreditationProcedurePeriod(): self
    {
        return new self(self::KEY_ACCREDITATION_PROCEDURE_PERIOD);
    }

    public static function buySellingProcedurePeriod(): self
    {
        return new self(self::KEY_BUY_SELLING_PROCEDURE_PERIOD);
    }

    public static function siteDomain(): self
    {
        return new self(self::KEY_SITE_DOMAIN);
    }

    public static function withdrawPeriod(): self
    {
        return new self(self::KEY_WITHDRAW_PERIOD);
    }

    public static function participationWithNegativeBalance(): self
    {
        return new self(self::KEY_PARTICIPATION_WITH_NEGATIVE_BALANCE);
    }

    public static function confirmationParticipationAuction(): self
    {
        return new self(self::KEY_CONFIRMATION_PARTICIPATION_AUCTION);
    }

    public static function replacingEpPeriod(): self
    {
        return new self(self::KEY_REPLACING_EP_PERIOD);
    }

    /**
     * @return string
     */
    public function getValue(): string{
        return self::$defaultKeys[$this->name];
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
