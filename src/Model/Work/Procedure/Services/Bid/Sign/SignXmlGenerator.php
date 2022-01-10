<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Bid\Sign;


use App\Container\Model\User\Service\XmlEncoderFactory;
use App\Model\Admin\Entity\Settings\Key;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\ReadModel\Procedure\Bid\DetailView;
use App\ReadModel\Procedure\Bid\Document\DocumentFetcher;
use App\ReadModel\Procedure\Bid\Document\Filter\Filter;
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
     * SignXmlGenerator constructor.
     * @param XmlEncoderFactory $xmlEncoderFactory
     * @param DocumentFetcher $documentFetcher
     * @param FileUploader $fileUploader
     * @param SettingsFetcher $settingsFetcher
     */
    public function __construct(
        XmlEncoderFactory $xmlEncoderFactory,
        DocumentFetcher $documentFetcher,
        FileUploader $fileUploader,
        SettingsFetcher $settingsFetcher
    ) {
        $this->xmlEncoderFactory = $xmlEncoderFactory;
        $this->documentFetcher = $documentFetcher;
        $this->fileUploader = $fileUploader;
        $this->settingsFetcher = $settingsFetcher;
    }

    /**
     * @param DetailView $detailView
     * @param \App\ReadModel\Profile\DetailView $detailViewProfile
     * @param \App\ReadModel\Profile\Payment\Requisite\DetailView $detailViewRequsite
     * @return string
     */
    public function generate(DetailView $detailView, \App\ReadModel\Profile\DetailView $detailViewProfile, \App\ReadModel\Profile\Payment\Requisite\DetailView $detailViewRequsite): string {
        $document = $this->documentFetcher->all(
            Filter::fromBid($detailView->id),1, 100
        );
        $docArray = [];

        foreach ($document->getItems() as $item){
            $docArray[] = [
                'documentName' => $item['document_name'],
                'fileRealName' => $item['file_real_name'],
                'fileName' => $item['file_real_name'],
                'url' => $this->fileUploader->generateUrl($item['file_path'].'/'.$item['file_name']),
                'hash' => $item['file_hash'],
                'sign' => $item['file_sign'] ?? '',
                'uploaderIp' => $item['participant_ip'],
                'createdAt' => $item['created_at']
            ];
        }

        $findServerName = $this->settingsFetcher->findDetailByKey(Key::siteDomain());
        $findNameOrganization = $this->settingsFetcher->findDetailByKey(Key::nameOrganization());

        if($detailViewProfile->isLegalEntity()){
            $dataProfile = $detailViewProfile->getDataOrganizer();
        } elseif ($detailViewProfile->isIndividualEntrepreneur() or $detailViewProfile->isIndividual()){
            $dataProfile = $detailViewProfile->getOwnerFullNameBid().", ".$detailViewProfile->getDataPassword();
        }



        $firstPoint = "Ознакомившись с извещением о проведении электронных торгов (далее «торги») по продаже подвергнутого аресту {$detailView->bailiffs_name_dative_case} (далее – «отдел УФССП») по исполнительному производству 
         №{$detailView->executive_production_number} и принадлежащего должнику 
         {$detailView->debtor_full_name_date_case} имущества: 
         {$detailView->procedure_title}, опубликованным на сайте {$findServerName}, 
         оператор {$findNameOrganization}, от 
         ".$this->date($detailView->lot_created_at).", и с Порядком проведения торгов по реализации арестованного имущества и регламентом ЭТП, а также изучив предмет торгов,
         $dataProfile
         (далее Заявитель), согласен на использование организатором торгов персональных данных согласно ст.9 Федерального закона «О персональных данных» от 27.07.2006 № 152-ФЗ и просит принять настоящую заявку на участие в открытых торгах в электронной форме,
         Организатор торгов – {$detailView->organizer_full_title_organization}, ".$this->date($detailView->start_trading_date).", по адресу {$findServerName}";

        $secondPoint = "Подавая настоящую заявку на участие в торгах, Заявитель обязуется соблюдать правила проведения торгов, содержащиеся в указанном выше извещении о проведении торгов на сайте {$findServerName} torgi.gov.ru, fssprus.ru и сайте Организатора торгов";

        $thirdPoint = "Настоящим Заявитель подтверждает, что он ознакомлен с имуществом, его обременениями  и ограничениями, требованиями указанными в извещении об аукционе, с проектом договора купли-продажи имущества, условия которого определены в качестве условий договора присоединения, и принимает его полностью.";

        $fourthPoint = "В случае признания победителем торгов Заявитель обязуется:
                        <ul>
                        <li>подписать электронной подписью протокол о результатах торгов в день завершения торгов, согласно регламенту работы электронной площадки, по адресу, указанному в информационном сообщении;</li>
                        <li>оплатить имущество по цене, в порядке и сроки, установленные подписанным протоколом о результатах торгов;</li>
                        <li>подписать договор купли-продажи имущества в срок, установленный извещением о проведении торгов.</li>
                        </ul>";
        $fifthPoint = "Заявитель осведомлен о том, что выставленное на торги имущество продается на основании документа {$detailView->tender_basic}, и согласен с тем, что:
                        <ul>
                        <li>проданное на торгах имущество возврату не подлежит, и что ни Организатор торгов, ни орган, обративший взыскание на имущество, не несут ответственности за качество проданного имущества;</li>
                        <li>Организатор торгов не несет ответственности за ущерб, который может быть причинен Заявителю отменой торгов или снятием с торгов части имущества (независимо от времени до начала проведения торгов), а также приостановлением организации и проведения торгов в случае, если данные действия осуществлены во исполнение поступившего от органа обратившего взыскание на имущество, постановления об отложении, приостановлении или прекращении исполнительного производства, а также в иных предусмотренных федеральным законодательством и иными нормативными правовыми актами случаях отзыва государственным органом заявки на реализацию имущества или уменьшения объема (количества) выставленного на торги имущества. Имущество может быть отозвано с реализации на основании Постановления судебного пристава исполнителя на любой стадии реализации</li>
                        </ul>";

        $sixthPoint = "
         Заявитель осведомлен о том, что он вправе отозвать настоящую заявку до момента приобретения им статуса участника торгов и что при этом сумма внесенного задатка возвращается Заявителю в порядке,
                        установленном договором о задатке, заключенным с Организатором торгов. Заявитель осведомлен о том, что при отказе от подписания протокола о результатах торгов и/или не внесении денежных средств в счет оплаты приобретенного имущества задаток победителю торгов не возвращается.
                        <br/>
                        Реквизиты заявителя:
                        <br>
                        {$dataProfile}<br>
                        Наименование банка: {$detailViewRequsite->bank_name}<br>
                        Расчетный счет: {$detailViewRequsite->payment_account}<br>
                        Бик: {$detailViewRequsite->bank_bik}<br>
                        Корреспондентский счет: {$detailViewRequsite->correspondent_account}
        ";

        return $this->xmlEncoderFactory->create()->encode([
            'name' => 'Заявка на участие в электронных торгах',
            'firstPoint' => $firstPoint,
            'secondPoint' => $secondPoint,
            'thirdPoint' => $thirdPoint,
            'fourthPoint' => $fourthPoint,
            'fifthPoint' => $fifthPoint,
            'sixthPoint' => $sixthPoint,
            'documents' => [$docArray],
            'createdAt' => $detailView->created_at
        ], 'xml');
    }

    private function date(string $date): string{
        return (new \DateTimeImmutable($date))->format('d.m.Y');
    }

}
