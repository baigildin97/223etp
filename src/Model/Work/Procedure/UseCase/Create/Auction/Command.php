<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Create\Auction;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $procedureType;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $organizerFullName;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Email()
     */
    public $organizerEmail;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $organizerPhone;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $pricePresentationForm;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $procedureName;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $infoPointEntry;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $infoTradingVenue;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $infoBiddingProcess;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $newProcedureId;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $userId;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $clientIp;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $tenderingProcess;


    /**
     * Command constructor.
     * @param string $userId
     * @param string $clientIp
     * @param string $newProcedureId
     */
    public function __construct(string $userId, string $clientIp, string $newProcedureId)
    {
        $this->userId = $userId;
        $this->clientIp = $clientIp;
        $this->newProcedureId = $newProcedureId;
        $this->infoBiddingProcess = $this->infoBiddingProcess();
        $this->infoPointEntry = $this->infoPointEntry();
        $this->infoTradingVenue = $this->infoTradingVenue();
        $this->tenderingProcess = $this->tenderingProcess();
    }


    /**
     * @return string
     */
    private function infoBiddingProcess(): string
    {
        return "Подача заявки и документов осуществляется посредством системы электронного документооборота на сайте https://229etp.ru в соответствии с извещением об аукционе, порядком подачи заявки на участие в торгах и регламентом электронной площадки и принимаются в электронном виде, подписанные ЭП должностного лица заявителя (для юр. лиц) или ЭП заявителя (для физ. лица). Ознакомиться с аукционной документацией, в том числе, с дополнительной информацией о предмете торгов, требованиями о порядке оформления в торгах и порядке их проведения заинтересованные лица могут на сайте https://229etp.ru, www.torgi.gov.ru (раздел «документы») и по телефону контактного лица организатора. 
К торгам допускаются любые лица, зарегистрированные на электронной торговой площадке , находящейся в сети Интернет по адресу: www.229etp.ru, предоставившие заявки на участие в торгах с помощью электронного документооборота на ЭТП, подписанные электронной подписью с необходимым комплектом документов. Участие в торгах производится в соответствии с тарифами, установленными нормативными документами электронной площадки и размещенными на сайте https://www.229etp.ru , в разделе «Тарифы». Победителем торгов признается участник, предложивший наиболее высокую цену. Для участия в торгах необходимо зарегистрироваться на электронной торговой площадке, находящейся в сети Интернет по адресу https://www.229etp.ru (срок регистрации в соответствии с регламентом электронной площадки в течении 5 (пяти) рабочих дней) и направить в виде электронного документа Организатору торгов следующие документы: 
1. Заявку на участие в торгах по установленной форме (подписанную ЭЦП).
2. Платежное поручение (платежный документ) с отметкой банка об исполнении, подтверждающее внесение претендентом задатка в счет обеспечения оплаты приобретаемого имущества. 
3. Надлежащим образом оформленная доверенность на лицо, имеющее право действовать от имени претендента, если заявка подается представителем претендента. 
4. Сведения указанные в опросном листе, размещенном на ЭТП в соотв. с ФЗ № 115 от 07.08.2001 
Для юридических лиц:
- Копии учредительных документов и свидетельства о государственной регистрации; - Надлежащим образом заверенные копии документов, подтверждающие полномочия органов управления претендента (выписки из протоколов, копии приказов); 
- Письменное решение соответствующего органа управления претендента, разрешающее приобретение имущества, если это необходимо в соответствии с учредительными документами претендента и действующим законодательством; 
- Оригинал или нотариально заверенную копию выписки из ЕГРЮЛ, выданную налоговым органом не ранее чем за 30 (тридцать) дней до даты проведения торгов;
- Выписка из торгового реестра страны происхождения или иное эквивалентное доказательство юридического статуса (для юридических лиц – нерезидентов РФ). 
Для физических лиц:
- Копия всех страниц паспорта или заменяющего его документа; 
- Копия свидетельства о постановке на налоговый учет. 
- Копия свидетельства СНИЛС Для индивидуальных предпринимателей: 
- документы по списку для физических лиц; 
- нотариально заверенная копия свидетельства о внесении физического лица в Единый государственный реестр индивидуальных предпринимателей;
- декларация о доходах на последнюю отчётную дату Физические лица - иностранные граждане и лица без гражданства (в том числе и представители) дополнительно предоставляют: 
1. Документы, подтверждающие в соответствии с действующим законодательством их законное пребывание (проживание) на территории Российской Федерации, в том числе миграционную карту. Документы, предоставляемые иностранным гражданином, и лицом без гражданства должны быть легализованы, документы, составленные на иностранном языке должны сопровождаться их нотариально заверенным переводом на русский язык.";
    }

    private function infoPointEntry(): string
    {
        return "Заявки на участие в аукционе, подписанные ЭП, вместе с прилагаемыми к ним документами направляются в электронной форме на сайт ЭТП по адресу в сети Интернет https://229etp.ru";
    }

    /**
     * @return string
     */
    private function infoTradingVenue(): string
    {
        return "Торги проводятся на электронной торговой площадке, находящейся в сети интернет по адресу https://229etp.ru, в соответствии со ст. 87, 89, 90 ФЗ «Об исполнительном производстве» от 2 октября 2007 г. № 229-ФЗ; ст. 447-449 ГК РФ, регламентом электронной торговой площадки";
    }

    private function tenderingProcess(): string {
        return "Организатор торгов объявляет торги несостоявшимися, если: 
1.Заявки на участие в торгах подали менее двух лиц; 
2. В торгах никто не принял участие или принял участие один участник торгов;
3. Из участников торгов никто не сделал надбавки к начальной цене имущества; 
4. Лицо, выигравшее торги, в течение пяти дней со дня проведения торгов не оплатило стоимость. По итогам торгов в тот же день победителем торгов и Организатором торгов подписывается электронной подписью Протокол о результатах торгов по продаже арестованного имущества (далее по тексту - Протокол). Победитель торгов уплачивает сумму покупки за вычетом задатка в течение 5 (пяти) рабочих дней с момента подписания электронной подписью обеими сторонами протокола. В течение 5 (пяти) рабочих дней после поступления на счет организатора торгов денежных средств, составляющих цену имущества, определенную по итогам торгов, победителем аукциона и организатором торгов подписывается электронной подписью договор купли-продажи. Если Победитель торгов в установленные сроки не подписал электронной подписью Протокол, он лишается права на приобретение имущества, сумма внесенного им задатка не возвращается. Право собственности на недвижимое имущество переходит к Победителю торгов в порядке, установленном законодательством Российской Федерации. Расходы по государственной регистрации перехода права собственности на недвижимое имущество возлагаются на победителя аукциона (покупателя).                                                                                                                                                                           Организатор торгов оставляет за собой право снять выставленное имущество с торгов по указанию судебного пристава-исполнителя. Все вопросы, касающиеся проведения аукциона, не нашедшие отражения в настоящем извещении, регулируются в соответствии с законодательством РФ.";
    }
}