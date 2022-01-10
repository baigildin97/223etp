<?php
declare(strict_types=1);
namespace App\Model\User\Service\Profile\Accreditation\Recall;


use App\Container\Model\User\Service\XmlEncoderFactory;
use App\Helpers\Filter;
use App\ReadModel\Profile\XmlDocument\DetailView;
use App\ReadModel\Profile\XmlDocument\XmlDocumentFetcher;

class RecallXmlGenerator
{
    /**
     * @var XmlEncoderFactory
     */
    private $xmlEncoderFactory;

    private $xmlDocumentFetcher;


    /**
     * @param XmlEncoderFactory $xmlEncoderFactory
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     */
    public function __construct(XmlEncoderFactory $xmlEncoderFactory, XmlDocumentFetcher $xmlDocumentFetcher) {
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
        $this->xmlEncoderFactory = $xmlEncoderFactory;
    }

    public function generate(DetailView $detailView): string {
        if ($detailView->isTypeStatementEdit()){
            return $this->xmlEncoderFactory->create()->encode([
                'generalInformation' =>
                    [
                        'statementText' => [
                            'title' => 'Отзыв заявления на редактирование регистрационных данных',
                            'text' => $this->getStatementEditText($detailView)
                        ],
                    ]
            ],'xml');
        }


        if ($detailView->isTypeStatementRegistration()){
            return $this->xmlEncoderFactory->create()->encode([
                'generalInformation' =>
                    [
                        'statementText' => [
                            'title' => 'Отзыв заявления на регистрацию',
                            'text' => $this->getStatementRegistrationText($detailView)
                        ],
                    ]
            ],'xml');
        }

        if ($detailView->isTypeReplacingEp()){
            return $this->xmlEncoderFactory->create()->encode([
                'generalInformation' =>
                    [
                        'statementText' => [
                            'title' => 'Отзыв заявления на замену электронной подписи',
                            'text' => $this->getStatementReplacingEp($detailView)
                        ],
                    ]
            ],'xml');
        }




    }

    private function getStatementEditText(DetailView $detailView): string {
        if ($detailView->userIsLegalEntity()){
            //Просим отозвать по данное ранее от нашей организации заявление на регистрации
            return "<p>{$detailView->organization_full_title}, в лице {$detailView->user_position} {$detailView->user_name}, действующего на основании Устава, просим отозвать поданное ранее от нашей организации заявление на регистрацию  (входящий №{$detailView->id_number}) от ".Filter::date($detailView->created_at, true)."</p>";
        }else{
            return "Я, {$detailView->user_name}, {$detailView->user_passport_series} {$detailView->user_passport_number} {$detailView->user_passport_issuer} {$detailView->user_passport_issuance_date}, 
            прошу отозвать ранее мною поданное заявление на редактирование регистрационных данных (входящий №{$detailView->id_number}) от ".Filter::date($detailView->created_at, true);
        }
    }

    /**
     * @param DetailView $detailView
     * @return string
     */
    private function getStatementReplacingEp(DetailView $detailView): string {
        if ($detailView->userIsLegalEntity()){
            //Просим отозвать по данное ранее от нашей организации заявление на замену ЭП
            return "<p>{$detailView->organization_full_title}, в лице {$detailView->user_position} {$detailView->user_name}, действующего на основании Устава, просим отозвать поданное ранее от нашей организации заявление на замену электронной подписи (входящий №{$detailView->id_number}) от ".Filter::date($detailView->created_at, true)."</p>";
        }else{
            return "Я, {$detailView->user_name}, {$detailView->user_passport_series} {$detailView->user_passport_number} {$detailView->user_passport_issuer} {$detailView->user_passport_issuance_date}, 
            прошу отозвать ранее мною поданное заявление на замену электронной подписи (входящий №{$detailView->id_number}) от ".Filter::date($detailView->created_at, true);
        }
    }

    private function getStatementRegistrationText(DetailView $detailView): string {
        if ($detailView->userIsLegalEntity()){
            //Просим отозвать по данное ранее от нашей организации заявление на регистрации
            return "<p>{$detailView->organization_full_title}, в лице {$detailView->user_position} {$detailView->user_name}, действующего на основании Устава, просим отозвать поданное ранее от нашей организации заявление на регистрацию  (входящий №{$detailView->id_number}) от ".Filter::date($detailView->created_at, true)."</p>";
        }else{
            return "Я, {$detailView->user_name}, {$detailView->user_passport_series} {$detailView->user_passport_number} {$detailView->user_passport_issuer} {$detailView->user_passport_issuance_date}, 
            прошу отозвать ранее мною поданное заявление на регистрацию (входящий №{$detailView->id_number}) от ".Filter::date($detailView->created_at, true);
        }
    }
}