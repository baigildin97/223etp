<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Procedure\Sign;


use App\Container\Model\User\Service\XmlEncoderFactory;
use App\Helpers\Filter;
use App\Helpers\FormatMoney;
use App\Model\Admin\Entity\Settings\Key;
use App\Model\Work\Procedure\Entity\Lot\ArrestedPropertyType;
use App\Model\Work\Procedure\Entity\Lot\Nds;
use App\Model\Work\Procedure\Entity\PriceForm;
use App\Model\Work\Procedure\Entity\Type;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\ReadModel\Procedure\DetailView;
use App\ReadModel\Procedure\Lot\LotFetcher;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use Doctrine\Common\Collections\ArrayCollection;


class SignXmlGenerator
{
    /**
     * @var XmlEncoderFactory
     */
    private $xmlEncoderFactory;

    /**
     * @var LotFetcher
     */
    private $lotFetcher;

    /**
     * @var FormatMoney
     */
    private $formatMoney;

    /**
     * @var ProfileFetcher
     */
    private $profileFetcher;

    /**
     * @var SettingsFetcher
     */
    private $settingsFetcher;

    /**
     * XmlGenerator constructor.
     * @param XmlEncoderFactory $xmlEncoderFactory
     * @param LotFetcher $lotFetcher
     * @param FormatMoney $formatMoney
     * @param ProfileFetcher $profileFetcher
     * @param SettingsFetcher $settingsFetcher
     */
    public function __construct(
        XmlEncoderFactory $xmlEncoderFactory,
        LotFetcher $lotFetcher,
        FormatMoney $formatMoney,
        ProfileFetcher $profileFetcher,
        SettingsFetcher $settingsFetcher
    ) {
        $this->xmlEncoderFactory = $xmlEncoderFactory;
        $this->lotFetcher = $lotFetcher;
        $this->formatMoney = $formatMoney;
        $this->profileFetcher = $profileFetcher;
        $this->settingsFetcher = $settingsFetcher;
    }


    /**
     * @param DetailView $detailView
     * @return bool|false|float|int|string
     */
    public function generate(DetailView $detailView): string
    {

        $fullNameOrganization = $this->settingsFetcher->findDetailByKey(Key::fullNameOrganization());

//        $organizerProfile = $this->profileFetcher->find($detailView);

        $lots = $this->lotFetcher->all(
            \App\ReadModel\Procedure\Lot\Filter\Filter::fromProcedure($detailView->id),
            1, 100
        );


        $bidInfo = 'Подача заявки и документов осуществляется посредством системы электронного документооборота на сайте https://229etp.ru в соответствии с извещением об аукционе, порядком подачи заявки на участие в торгах и регламентом электронной площадки и принимаются в электронном виде, подписанные ЭЦП должностного лица заявителя (для юр. лиц) или ЭЦП заявителя (для физ. лица). Для участия в торгах необходимо направить в виде электронного документа Организатору торгов следующие документы: 1. Заявку на участие в торгах по установленной форме (подписанную ЭЦП) . 2. Подписанный и заполненный договор о задатке в отсканированном виде прикладывается к заявке на участие в торгах (шаблон договора приложен на ЭТП). 3. Платежное поручение с отметкой банка об исполнении, подтверждающее внесение претендентом задатка в соответствии с договором о задатке. 4. Надлежащим образом оформленная доверенность на лицо, имеющее право действовать от имени претендента, если заявка подается представителем претендента. 5. Сведения указанные в опросном листе, размещенном на ЭТП в соотв. с ФЗ № 115 от 07.08.2001 Для юридических лиц: - Копии учредительных документов и свидетельства о государственной регистрации; - Надлежащим образом заверенные копии документов, подтверждающие полномочия органов управления претендента (выписки из протоколов, копии приказов); - Письменное решение соответствующего органа управления претендента, разрешающее приобретение имущества, если это необходимо в соответствии с учредительными документами претендента и действующим законодательством; - Оригинал или нотариально заверенную копию выписки из ЕГРЮЛ, выданную налоговым органом не ранее чем за 30 (тридцать) дней до даты проведения торгов; - Выписка из торгового реестра страны происхождения или иное эквивалентное доказательство юридического статуса (для юридических лиц – нерезидентов РФ). Для физических лиц: - Копия всех страниц паспорта или заменяющего его документа; - Копию свидетельства о присвоении ИНН. Для индивидуальных предпринимателей: - документы по списку для физических лиц; - копия свидетельства о внесении физического лица в Единый государственный реестр индивидуальных предпринимателей; - декларация о доходах на последнюю отчётную дату Физические лица - иностранные граждане и лица без гражданства (в том числе и представители) дополнительно предоставляют: 1. Документы, подтверждающие в соответствии с действующим законодательством их законное пребывание (проживание) на территории Российской Федерации, в том числе миграционную карту. Документы, предоставляемые иностранным гражданином, и лицом без гражданства должны быть легализованы, документы, составленные на иностранном языке должны сопровождаться их нотариально заверенным переводом на русский язык. Ознакомиться с аукционной документацией, в том числе, с дополнительной информацией о предмете торгов, требованиями о порядке оформления в торгах и порядке их проведения заинтересованные лица могут на сайте http://229etp.ru, http://www.megapolis.org.ru, www.torgi.gov.ru (раздел «документы») и по телефону контактного лица организатора.';
        $infoBiddingProcess = 'Торги проводятся на электронной торговой площадке, находящейся в сети интернет по адресу https://229etp.ru, в соответствии со ст. 887, 89, 90 Федерального закона от 02.10.2007 №229-ФЗ «Об исполнительном производстве»; статьей 57 Федерального закона от 16.07.1998 №102-ФЗ «Об ипотеке (залоге недвижимости)»; статьями 447-449 ГК РФ, регламентом электронной торговой площадки.
        К торгам допускаются любые лица, зарегистрированные на электронной торговой площадке , находящейся в сети Интернет по адресу: www.229etp.ru, предоставившие заявки на участие в торгах с помощью электронного документооборота на ЭТП, подписанные электронной подписью с необходимым комплектом документов. Победителем торгов признается участник, предложивший наиболее высокую цену. Организатор торгов объявляет торги несостоявшимися, если: 1.Заявки на участие в торгах подали менее двух лиц; 2. В торгах никто не принял участие или принял участие один участник торгов; 3. Из участников торгов никто не сделал надбавки к начальной цене имущества; 4. Лицо, выигравшее торги, в течение пяти дней со дня проведения торгов не оплатило стоимость. По итогам торгов в тот же день победителем торгов и Организатором торгов подписывается электронной подписью Протокол о результатах торгов по продаже арестованного имущества (далее по тексту - Протокол). Победитель торгов уплачивает сумму покупки за вычетом задатка Организатору торгов в течение 5 (пяти) рабочих дней с момента подписания электронной подписью обеими сторонами протокола. В течение 5 (пяти) рабочих дней после поступления на счет организатора торгов денежных средств, составляющих цену имущества, определенную по итогам торгов, победителем аукциона и организатором торгов подписывается электронной подписью договор купли-продажи. Если Победитель торгов в установленные сроки не подписал электронной подписью Протокол, он лишается права на приобретение имущества, сумма внесенного им задатка не возвращается. Право собственности на недвижимое имущество переходит к Победителю торгов в порядке, установленном законодательством Российской Федерации. Расходы по государственной регистрации перехода права собственности на недвижимое имущество возлагаются на победителя аукциона (покупателя).';

        $lotsCollection = new ArrayCollection([]);
        foreach ($lots->getItems() as $lot){

            $pledgeer = null;
            $arrestedPropertyType = new ArrestedPropertyType($lot['arrested_property_type']);
            if ($arrestedPropertyType->isPledgedRealEstate() or $arrestedPropertyType->isCollateralizedMovableProperty()){
                $pledgeer = " ,и находящееся в залоге у {$lot['pledgeer']}, ";
            }

            //                     'name' => "Подвергнутое аресту {$lot['bailiffs_name']} по исполнительному производству {$lot['executive_production_number']}  от {$lot['date_enforcement_proceedings']} г., принадлежащее должнику {$lot['debtor_full_name_date_case']}, {$lot['lot_name']}",

            $lotsCollection->add(
                [
                    'number' => $lot['procedure_number'].'-'.$lot['id_number'],
                    'name' => "Подвергнутое аресту {$lot['bailiffs_name']} по исполнительному производству {$lot['executive_production_number']} от {$lot['date_enforcement_proceedings']} г., принадлежащее должнику {$lot['debtor_full_name_date_case']} {$pledgeer} {$lot['lot_name']}",
                    'tenderBasic' => $lot['tender_basic'],
                    'procedureType' => Type::auction()->getLocalizedName(),
                    'startTradingDate' => $lot['start_trading_date'],
                    'startingPrice' => $this->moneyFormat($lot['starting_price']),
                    'nds' => (new Nds($lot['nds']))->getLocalizedName(),
                    'depositAmount' => $this->moneyFormat($lot['deposit_amount']),
                    'advancePaymentTime' => $lot['advance_payment_time'],
                    'auctionStep' => $this->moneyFormat($lot['auction_step']),
                    'requisite' => $lot['requisite'],
                    'depositPolicy' => "В установленных законодательством РФ случаях на стоимость имущества начисляется НДС.
                    Задаток должен поступить на реквизиты: {$lot['requisite']}, не позднее {$this->date($lot['advance_payment_time'])}.
                    Документом, подтверждающим поступление задатка на счет, является выписка со счета, указанного в извещении.
                    Сумма внесенного задатка засчитывается в счет исполнения обязательств Победителя торгов по оплате приобретенного имущества.",
                    'moreInfo' => "Ознакомиться с дополнительной информацией о предмете торгов, о порядке оформления в торгах и порядке их проведения заинтересованные лица могут на сайте {$this->getServerHost()}, www.torgi.gov.ru, {$detailView->web_site} и по телефону контактного лица организатора.",
                    'infoPointEntry' => "{$detailView->info_point_entry} c {$this->dateTime($lot['start_date_of_applications'])} по {$this->dateTime($lot['closing_date_of_applications'])}
Подведение итогов приема заявок осуществляется {$this->dateTime($lot['summing_up_applications'])} и оформляется Организатором торгов соответствующим протоколом."
                ]
            );
        }

        $infoPointEntry = $detailView->info_point_entry;

        $content = [
            'documentName' => $detailView->organization_full_title.' торгов сообщает о проведении торгов в электронной форме',
            'procedureType' => (new Type($detailView->type))->getLocalizedName(),
            'pricePresentationForm' => (new PriceForm($detailView->price_presentation_form))->getLocalizedName(),
            'nameOrganization' => $fullNameOrganization,
            'organizerFullName' => $detailView->organization_full_title,
            'organizerWebSite' => $detailView->web_site,
            'organizerPhone' => $detailView->organizer_phone,
            'organizerEmail' => $detailView->organizer_email,
            'organizerIndex' => $detailView->organization_fact_index,
            'organizerCountry' => $detailView->organization_fact_country,
            'organizerRegion' => $detailView->organization_fact_region,
            'organizerCity' => $detailView->organization_fact_city,
            'organizerStreet' => $detailView->organization_fact_street,
            'organizerHouse' => $detailView->organization_fact_house,
            'lots' => $lotsCollection,
            //'infoApplicationProcedure' => $detailView->info_application_procedure,
            'contactPerson'=>"{$detailView->organizer_full_name}, телефон: {$detailView->organizer_phone}, email: {$detailView->organizer_email}, адрес места нахождения: {$detailView->getFactAddress()}",
            'infoTradingVenue' => $detailView->info_trading_venue,
            'infoBiddingProcess' => $detailView->info_bidding_process,
            'tendering_process' => $detailView->tendering_process,
        ];

        return $this->xmlEncoderFactory->create()->encode($content, 'xml');
    }

    /**
     * @param string $money
     * @return string
     */
    private function moneyFormat(string $money): string{
        return $this->formatMoney->currencyAsSymbol($money);
    }

    private function date(string $date): string{
        return (new \DateTimeImmutable($date))->format('d.m.Y');
    }

    private function dateTime(string $date): string {
        return (new \DateTimeImmutable($date))->format('d.m.Y H:i');
    }

    private function getServerHost(): string{
        return $_SERVER['SERVER_NAME'];
    }
}
