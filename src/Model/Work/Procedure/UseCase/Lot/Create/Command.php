<?php


namespace App\Model\Work\Procedure\UseCase\Lot\Create;

use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     * @Assert\NotBlank
     */
    public $arrestedPropertyType;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $reloadLot;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $tenderBasic;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $nds;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $dateEnforcementProceedings;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     */
    public $startDateOfApplications;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     */
    public $closingDateOfApplications;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     */
    public $summingUpApplications;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $debtorFullName;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $debtorFullNameDateCase;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     */
    public $advancePaymentTime;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $requisite;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $region;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $location_object;

    /**
     * @var string
     */
    public $additional_object_characteristics;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $starting_price;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $deposit_amount;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $depositPolicy;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $bailiffsName;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $bailiffsNameDativeCase;

    /**
     * @var string
     */
    public $pledgeer;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $executiveProductionNumber;

    /**
     * @var string
     */
    public $clientIp;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $offerAuctionTime;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $lotName;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $auctionStep;

    /**
     * @var \DateTime
     * @Assert\NotBlank
     */
    public $startTradingDate;

    /**
     * @var string
     */
    public $procedure_id;

    /**
     * Command constructor.
     * @param string $procedure_id
     * @param string $clientIp
     */
    public function __construct(string $procedure_id, string $clientIp)
    {
        $this->clientIp = $clientIp;
        $this->requisite = $this->depositPaymentDetails();
        $this->additional_object_characteristics = null;
        $this->depositPolicy = $this->depositPolicy();
        $this->tenderBasic = $this->tenderBasic();
        $this->reloadLot = 'NO';
        $this->procedure_id = $procedure_id;
    }

    private function depositPaymentDetails(): string {
        return "Получатель: Межрегиональное территориальное управление Федерального агентства по управлению государственным имуществом в городе Санкт-Петербурге и Ленинградской области: УФК по г.Санкт-Петербургу (МТУ Росимущества в городе Санкт-Петербурге и Ленинградской области, л/с 05721А16220), р/с 03212643000000017200 в Северо-Западном ГУ Банка России//УФК по г. Санкт-Петербургу, г.Санкт-Петербург, БИК 014030106, к/с 40102810945370000005, ИНН 7838426520, КПП 784001001, ОКТМО 40909000, статус налогоплательщика 01, УИН/0, КБК 16711414011010500440";
    }

    private function tenderBasic(): string {
        return "Имущество передано на реализацию на основании постановления судебного пристава-исполнителя";
    }

    private function depositPolicy(): string {
        return "Документом, подтверждающим поступление задатка является выписка с расчетного счета МТУ Росимущества в г.Санкт-Петербург, полученная Организатором торгов. Извещение о торгах является публичной офертой для заключения договора о задатке в соответствии со ст.437 ГК РФ, а перечисление задатка и подача претендентом заявки на участие в торгах является акцептом такой оферты, после чего договор о задатке считается заключенным в письменной форме.";
    }
}