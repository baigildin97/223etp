<?php
declare(strict_types=1);
namespace App\Model\User\Service\Profile\Accreditation\Sign;


use App\Container\Model\User\Service\XmlEncoderFactory;

use App\Model\User\Entity\Profile\Document\FileType;
use App\Model\User\Entity\Profile\Document\Status;
use App\Model\User\Entity\Profile\Organization\IncorporationForm;
use App\Model\Admin\Entity\Settings\Key;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\ReadModel\Profile\DetailView;
use App\ReadModel\Profile\Document\DocumentFetcher;
use App\ReadModel\Profile\Document\Filter\Filter;

use App\ReadModel\Profile\XmlDocument\XmlDocumentFetcher;
use App\Services\Uploader\FileUploader;

class SignXmlGenerator
{
    /**
     * @var XmlEncoderFactory
     */
    private $xmlEncoderFactory;

    /**
     * @var DocumentFetcher
     */
    private $documentFetcher;

    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @var SettingsFetcher
     */
    private $settingsFetcher;

    /**
     * @var XmlDocumentFetcher
     */
    private $xmlDocumentFetcher;


    /**
     * SignXmlGenerator constructor.
     * @param XmlEncoderFactory $xmlEncoderFactory
     * @param DocumentFetcher $documentFetcher
     * @param FileUploader $fileUploader
     * @param SettingsFetcher $settingsFetcher
     * @param XmlDocumentFetcher $xmlDocumentFetcher
     */
    public function __construct(
        XmlEncoderFactory $xmlEncoderFactory,
        DocumentFetcher $documentFetcher,
        FileUploader $fileUploader,
        SettingsFetcher $settingsFetcher,
        XmlDocumentFetcher $xmlDocumentFetcher
    ) {
        $this->xmlEncoderFactory = $xmlEncoderFactory;
        $this->documentFetcher = $documentFetcher;
        $this->fileUploader = $fileUploader;
        $this->settingsFetcher = $settingsFetcher;
        $this->xmlDocumentFetcher = $xmlDocumentFetcher;
    }

    /**
     * @param DetailView $detailView
     * @return bool|false|float|int|string
     */
    public function generate(DetailView $detailView){
//        if ($detailView->id == 'd7a9a8d9-0231-4918-abaf-63f953909fcc'){
//            dd($detailView);
//        }
        $document = $this->documentFetcher->all(
            Filter::fromProfile($detailView->id),1, 100
        );


        $docArray = [];
        foreach ($document->getItems() as $item){
            $docArray[] = [
                'id' => $item['id'],
                'fileRealName' => $item['file_real_name'],
                'fileName' => $item['file_real_name'],
                'url' => $this->fileUploader->generateUrl($item['file_path'].'/'.$item['file_name']),
                'fileType' => (new FileType($item['file_type']))->getLocalizedName(),
                'fileTypeOrigin' => $item['file_type'],
                'fileHash' => $item['file_hash'],
                'fileStatus' => (new Status($item['status']))->getLocalizedName().' '.\App\Helpers\Filter::date($item['file_sign_at']),
                'sign' => $item['file_sign'] ?? ' ',
                'fileSignAt' => $item['file_sign_at'] ?? ' ',
                'createdAt' => $item['created_at']
            ];
        }

        if ($detailView->isLegalEntity()){
            $inn = \App\Helpers\Filter::filterInnLegalEntity($detailView->inn);
        }else{
            if ($detailView->passport_inn){
                $inn = $detailView->passport_inn;
            }elseif ($detailView->inn){
                $inn = $detailView->inn;
            }else{
                $inn = $detailView->certificate_subject_name_inn;
            }
        }

        return $this->xmlEncoderFactory->create()->encode([
            'generalInformation'=> [
                'statementText' => $this->getStatementText($detailView),
                'status' => $detailView->status  ?? ' ',
                'typeProfile' => $detailView->role_name ?? ' ',
                'incorporatedForm' => (new IncorporationForm($detailView->incorporated_form))->getLocalizedName() ?? ' ',
                'incorporatedFormOrigin' => $detailView->incorporated_form,
                'profileId' => $detailView->id,
                'createdAt' => $detailView->created_at ?? ' ',
            ],

            'organizationInfo'=> [
                'fullTitleOrganization' => $detailView->full_title_organization ?? ' ',
                'shortTitleOrganization' => $detailView->short_title_organization ?? ' ',
                'kpp' => $detailView->kpp ?? ' ',
                'ogrn' => $detailView->ogrn ?? ' ',
                'inn' => $inn ?? ' ',
                'factCountry' => $detailView->fact_country ?? ' ',
                'factRegion' => $detailView->fact_region ?? ' ',
                'factCity' => $detailView->fact_city ?? ' ',
                'factIndex' => $detailView->fact_index ?? ' ',
                'factStreet' => $detailView->fact_street ?? ' ',
                'factHouse' => $detailView->fact_house ?? ' ',
                'legalCountry' => $detailView->legal_country ?? ' ',
                'legalRegion' => $detailView->legal_region ?? ' ',
                'legalCity' => $detailView->legal_city ?? ' ',
                'legalIndex' => $detailView->legal_index ?? ' ',
                'legalStreet' => $detailView->legal_street ?? ' ',
                'legalHouse' => $detailView->legal_house ?? ' ',
                'orgEmail' => $detailView->org_email ?? ' ',
                'webSite' => $detailView->web_site ?? ' ',
            ],

            'representativeInfo' => [
                'ownerPosition' => $detailView->position ?? ' ',
                'reprPassportFirstName' => $detailView->repr_passport_first_name ?? ' ',
                'reprPassportMiddleName' => $detailView->repr_passport_middle_name ?? ' ',
                'reprPassportLastName' => $detailView->repr_passport_last_name ?? ' ',
                'reprPassportInn' => $detailView->passport_inn ?? ' ',
                'position' => $detailView->position ?? ' ',
                'confirmingDocument' => $detailView->confirming_document ?? ' ',
                'phone' => $detailView->phone ?? ' ',
                'email' => $detailView->user_email ?? ' ',
                'snils' => $detailView->passport_snils ?? '',
                'passportSeries' => $detailView->passport_series ?? ' ',
                'passportNumber' => $detailView->passport_number ?? ' ',
                'passportIssuer' => $detailView->passport_issuer ?? ' ',
                'passportIssuanceDate' => $detailView->passport_issuance_date ?? ' ',
                'passportCitizenship' => $detailView->passport_citizenship ?? ' ',
                'passportUnitCode' => $detailView->passport_unit_code ?? ' ',
                'passportBirthDay' => $detailView->passport_birth_day ?? ' ',

                'factCountry' => $detailView->passport_fact_country ?? ' ',
                'factRegion' => $detailView->passport_fact_region ?? ' ',
                'factCity' => $detailView->passport_fact_city ?? ' ',
                'factIndex' => $detailView->passport_fact_index ?? ' ',
                'factStreet' => $detailView->passport_fact_street ?? ' ',
                'factHouse' => $detailView->passport_fact_house ?? ' ',
                'legalCountry' => $detailView->passport_legal_country ?? ' ',
                'legalRegion' => $detailView->passport_legal_region ?? ' ',
                'legalCity' => $detailView->passport_legal_city ?? ' ',
                'legalIndex' => $detailView->passport_legal_index ?? ' ',
                'legalStreet' => $detailView->passport_legal_street ?? ' ',
                'legalHouse' => $detailView->passport_legal_house ?? ' ',
            ],

            'certificateInfo' => [
              'id' => $detailView->certificate_id ?? ' ',
              'owner' => $detailView->certificate_owner ?? ' ',
              'certificateValidFrom' => $detailView->certificate_valid_from ?? ' ',
              'certificateValidTo' => $detailView->certificate_valid_to ?? ''
            ],
            'email' => $detailView->user_email ?? '',
            'createdAt' => $detailView->created_at ?? ' ',

            'documents' => [$docArray]
        ], 'xml');
    }


    /**
     * @param string $userFullName
     * @param string $passportSeries
     * @param string $passportNumber
     * @param string $passportIssuer
     * @param string $passportIssuanceDate
     * @return array
     */
    private function statementEditTextDefault(
        string $userFullName,
        string $passportSeries,
        string $passportNumber,
        string $passportIssuer,
        string $passportIssuanceDate
    ): array {
        return [
            'title' => 'Заявление на редактирование данных пользователя.',
            'text' => "<p>Я, {$userFullName}, паспорт серия: {$passportSeries}, номер: {$passportNumber}, выдан: {$passportIssuer}, дата выдачи: ".\App\Helpers\Filter::date($passportIssuanceDate, true).",</p>
<p>прошу отредактировать данные моего профиля пользователя и/или заменить регистрационные документы, в связи с внесенными мной изменениями в личном кабинете.</p>"
        ];
    }

    /**
     * @param string $ownerOrganizationFullName
     * @param string $incorporatedForm
     * @param string $ownerPosition
     * @param string $ownerFullName
     * @param string $confirmingDocument
     * @return string
     */
    private function statementEditTextLegalEntity(
        string $ownerOrganizationFullName,
        string $incorporatedForm,
        string $ownerPosition,
        string $ownerFullName,
        string $confirmingDocument
    ): array {

        return [
            'title' => 'Заявление на редактирование данных пользователя.',
            'text' => "<p>{$ownerOrganizationFullName}, в лице {$ownerPosition} {$ownerFullName}, действующего на основании Устава,</p><p> прошу отредактировать данные профиля пользователя нашей организации и/или заменить регистрационные документы, в связи с внесенными мной изменениями в личном кабинете.</p>"
        ];
    }


    private function statementRegistrationTextDefault(
        string $serviceName,
        string $siteDomain,
        string $fullName,
        string $passportSeries,
        string $passportNumber,
        string $passportIssuer,
        string $passportIssuanceDate,
        string $serviceOrganizationFullName
    ): array {
        return [
            'title' => "Заявление на регистрацию на ЭТП «РесТорг».",
            'text' => "<p>Прошу зарегистрировать меня на электронной торговой площадке {$serviceName}.</p>
<p>Я, {$fullName}, паспорт серия: {$passportSeries}, номер: {$passportNumber}, выдан: {$passportIssuer}, дата выдачи: ".\App\Helpers\Filter::date($passportIssuanceDate, true).",</p>
<p>в соответствии со статьёй 428 ГК Российской Федерации полностью и безусловно присоединяюсь к Регламенту электронной торговой площадки {$serviceName} условия которого определены 
Оператором электронной площадки {$serviceOrganizationFullName} и опубликованы на электронной площадке по адресу {$siteDomain}.</p>
<p>С Регламентом электронной площадки {$serviceName} и приложениями к нему ознакомлен(а) и обязуюсь соблюдать все положения указанного документа.</p>
<p>Настоящим даю согласие на обработку моих персональных данных Оператору электронной площадки {$serviceOrganizationFullName}  (далее Оператор), в целях обеспечения моего участия в торгах на электронной площадке {$serviceName}. Персональные данные, на обработку которых распространяется данное разрешение, включают в себя данные, предоставленные мною в форме анкет, договоров и других документов, заполненных мною на электронной площадке, а также переданных мной Оператору лично, через представителя, почтовой связью или иным способом. Обработка персональных данных включает в себя совершение действий, предусмотренных пунктом 3 части первой статьи 3 Федерального закона от 27 июля 2006 года N 152-ФЗ \"О персональных данных\". Обработка персональных данных может быть как автоматизированная, так и без использования средств автоматизации. Настоящее согласие выдано без ограничения срока его действия и может быть отозвано в порядке, установленном законодательством о защите персональных данных.</p>"
        ];
    }

    private function statementRegistrationTextLegalEntity(
        string $serviceName,
        string $ownerOrganizationName,
        string $ownerPosition,
        string $ownerFullName,
        string $siteDomain,
        string $serviceOrganizationFullName
    ): array {
        return [
            'title' => "Заявление на регистрацию на ЭТП {$serviceName}.",
            'text' => "<p>Прошу зарегистрировать {$ownerOrganizationName} на электронной торговой площадке {$serviceName}.</p>

<p>{$ownerOrganizationName}, в лице {$ownerPosition} {$ownerFullName}, действующего на основании Устава, настоящим заявляем, что </p>
<p>в соответствии со статьёй 428 ГК Российской Федерации полностью и безусловно присоединяемся к Регламенту электронной торговой площадки {$serviceName} 
 условия которого определены Оператором электронной площадки $serviceOrganizationFullName и опубликованы на электронной площадке по адресу {$siteDomain}.</p>

<p>С Регламентом электронной площадки {$serviceName} и приложениями к нему ознакомлены  и обязуемся соблюдать все положения указанного документа.</p>

<p>Настоящим даю согласие на обработку моих персональных данных Оператору электронной площадки $serviceOrganizationFullName (далее Оператор), в целях обеспечения моего участия в торгах на электронной площадке {$serviceName}. Персональные данные, на обработку которых распространяется данное разрешение, включают в себя данные, предоставленные мною в форме анкет, договоров и других документов, заполненных мною на электронной площадке, а также переданных мной Оператору лично, через представителя, почтовой связью или иным способом. Обработка персональных данных включает в себя совершение действий, предусмотренных пунктом 3 части первой статьи 3 Федерального закона от 27 июля 2006 года N 152-ФЗ \"О персональных данных\". Обработка персональных данных может быть как автоматизированная, так и без использования средств автоматизации. Настоящее согласие выдано без ограничения срока его действия и может быть отозвано в порядке, установленном законодательством о защите персональных данных.</p>"
        ];
    }


    private function statementRegistrationTextIndividualEntrepreneur(
        string $serviceName,
        string $siteDomain,
        string $fullName,
        string $inn,
        string $passportSeries,
        string $passportNumber,
        string $passportIssuer,
        string $passportIssuanceDate,
        string $serviceOrganizationFullName
    ): array {
        return [
            'title' => "Заявление на регистрацию на ЭТП «РесТорг».",
            'text' => "<p>Прошу зарегистрировать меня на электронной торговой площадке {$serviceName}.</p>
<p>Я, {$fullName}, являясь индивидуальным предпринимателем, ИНН: {$inn}, паспорт серия: {$passportSeries}, номер: {$passportNumber}, выдан: {$passportIssuer}, дата выдачи: ".\App\Helpers\Filter::date($passportIssuanceDate, true).",</p>
<p>в соответствии со статьёй 428 ГК Российской Федерации полностью и безусловно присоединяюсь к Регламенту электронной торговой площадки {$serviceName} условия которого определены 
Оператором электронной площадки $serviceOrganizationFullName и опубликованы на электронной площадке по адресу {$siteDomain}.</p>
<p>С Регламентом электронной площадки «{$serviceName}» и приложениями к нему ознакомлен(а) и обязуюсь соблюдать все положения указанного документа.</p>
<p>Настоящим даю согласие на обработку моих персональных данных Оператору электронной площадки $serviceOrganizationFullName  (далее Оператор), в целях обеспечения моего участия в торгах на электронной площадке {$serviceName}. Персональные данные, на обработку которых распространяется данное разрешение, включают в себя данные, предоставленные мною в форме анкет, договоров и других документов, заполненных мною на электронной площадке, а также переданных мной Оператору лично, через представителя, почтовой связью или иным способом. Обработка персональных данных включает в себя совершение действий, предусмотренных пунктом 3 части первой статьи 3 Федерального закона от 27 июля 2006 года N 152-ФЗ \"О персональных данных\". Обработка персональных данных может быть как автоматизированная, так и без использования средств автоматизации. Настоящее согласие выдано без ограничения срока его действия и может быть отозвано в порядке, установленном законодательством о защите персональных данных.</p>"
        ];
    }


    private function statementReplacingEpTextDefault(): array {
        return [
            'title' => 'Заявление на замену ЭП.',
            'text' => "<p>Прошу произвести замену моего сертификата электронной подписи</p>"
        ];
    }

    /**
     * @param DetailView $detailView
     * @return string[]
     * @throws \Doctrine\DBAL\Exception
     */
    private function getStatementText(DetailView $detailView): array {
        $xmlDocument = $this->xmlDocumentFetcher->issetStatementForRegistration($detailView->id);
        $settings = $this->settingsFetcher->allArray();

        $incorporatedForm = new IncorporationForm($detailView->incorporated_form);

        if ($detailView->isReplacingEp()){
            //Замена Электронной подписи
                return $this->statementReplacingEpTextDefault();
        }

        if ($xmlDocument){
            // Редактирование
            if ($incorporatedForm->isLegalEntity()){
                return $this->statementEditTextLegalEntity(
                    $detailView->full_title_organization,
                    $incorporatedForm->getLocalizedName(),
                    $detailView->position,
                    $detailView->user_name,
                    $detailView->confirming_document
                );
            }else{
                return $this->statementEditTextDefault(
                    $detailView->user_name,
                    $detailView->passport_series,
                    $detailView->passport_number,
                    $detailView->passport_issuer,
                    $detailView->passport_issuance_date
                );
            }
        }else{
            // Создание
            if ($incorporatedForm->isLegalEntity()){
                return $this->statementRegistrationTextLegalEntity(
                    $settings[Key::nameService()->getName()],
                    $detailView->full_title_organization,
                    $detailView->position,
                    $detailView->user_name,
                    $settings[Key::siteDomain()->getName()],
                    $settings[Key::nameOrganization()->getName()]
                );
            }else if ($incorporatedForm->isIndividualEntrepreneur()){
                return $this->statementRegistrationTextIndividualEntrepreneur(
                    $settings[Key::nameService()->getName()],
                    $settings[Key::siteDomain()->getName()],
                    $detailView->user_name,
                    $detailView->inn,
                    $detailView->passport_series,
                    $detailView->passport_number,
                    $detailView->passport_issuer,
                    $detailView->passport_issuance_date,
                    $settings[Key::nameOrganization()->getName()]
                );

            } else{
                return $this->statementRegistrationTextDefault(
                    $settings[Key::nameService()->getName()],
                    $settings[Key::siteDomain()->getName()],
                    $detailView->user_name,
                    $detailView->passport_series,
                    $detailView->passport_number,
                    $detailView->passport_issuer,
                    $detailView->passport_issuance_date,
                    $settings[Key::nameOrganization()->getName()]
                );
            }
        }
    }
}
