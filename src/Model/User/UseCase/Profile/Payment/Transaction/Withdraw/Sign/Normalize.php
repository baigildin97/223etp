<?php
declare(strict_types=1);
namespace App\Model\User\UseCase\Profile\Payment\Transaction\Withdraw\Sign;


class Normalize
{

    public $statementText;

    public $money;

    public $clientOrganizationName;

    public $clientOrganizationInn;

    public $clientOrganizationKpp;

    public $clientBankName;

    public $clientBankPaymentAccount;

    public $clientBankCorrespondentAccount;

    public $clientBankBik;


    public function __construct(
        string $statementText,
        string $money,
        string $clientOrganizationName,
        string $clientOrganizationInn,
        ? string $clientOrganizationKpp,
        string $clientBankName,
        string $clientBankPaymentAccount,
        string $clientBankCorrespondentAccount,
        string $clientBankBik
    ) {
        $this->statementText = $statementText;
        $this->money = $money;
        $this->clientBankBik = $clientBankBik;
        $this->clientBankCorrespondentAccount = $clientBankCorrespondentAccount;
        $this->clientBankPaymentAccount = $clientBankPaymentAccount;
        $this->clientBankName = $clientBankName;
        $this->clientOrganizationKpp = $clientOrganizationKpp;
        $this->clientOrganizationInn = $clientOrganizationInn;
        $this->clientOrganizationName = $clientOrganizationName;
    }

}