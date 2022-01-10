<?php
declare(strict_types=1);
namespace App\Model\User\Service\Profile\Payment\Withdraw;


use App\Container\Model\User\Service\XmlEncoderFactory;
use App\Model\User\UseCase\Profile\Payment\Transaction\Withdraw\Sign\Normalize;

class WithdrawXmlGenerator
{
    private $xmlEncoderFactory;

    public function __construct(XmlEncoderFactory $xmlEncoderFactory) {
        $this->xmlEncoderFactory = $xmlEncoderFactory;
    }

    public function generate(Normalize $normalize){
        return $this->xmlEncoderFactory->create()->encode([
            'statementText' => $normalize->statementText,
            'withdrawalAmount' => $normalize->money.' RUB',
            'organizationInfo' => [
                'organizationName' => $normalize->clientOrganizationName,
                'organizationInn' => $normalize->clientOrganizationInn,
                'organizationKpp' => $normalize->clientOrganizationKpp,
            ],
            'bankInfo' => [
                'bankName' => $normalize->clientBankName,
                'bankPaymentAccountNumber' => $normalize->clientBankPaymentAccount,
                'bankCorrespondentAccountNumber' => $normalize->clientBankCorrespondentAccount,
                'bankBik' => $normalize->clientBankBik
            ]
        ], 'xml');
    }

}