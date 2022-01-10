<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Procedure\Protocols;

use App\Container\Model\User\Service\XmlEncoderFactory;
use App\Helpers\Filter;
use App\Helpers\FormatMoney;
use App\Model\Admin\Entity\Settings\Key;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\Lot\Nds;
use App\Model\Work\Procedure\Entity\PriceForm;
use App\Model\Work\Procedure\Entity\Protocol\Reason;
use App\Model\Work\Procedure\Entity\Protocol\Type;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\ReadModel\Procedure\Bid\BidFetcher;
use App\ReadModel\Procedure\Lot\LotFetcher;
use App\ReadModel\Profile\Payment\Requisite\RequisiteFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Services\Hash\Streebog;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class ResultsXmlGenerator
 * @package App\Model\Work\Procedure\Services\Procedure\Protocols
 */
class ResultsXmlGenerator implements ProtocolGeneratorInterface
{
    /**
     * @var xmlEncoderFactory
     */
    private $xmlEncoderFactory;

    /**
     * @var Streebog
     */
    private $streebog;

    /**
     * @var LotFetcher
     */
    private $lotFetcher;

    /**
     * @var SettingsFetcher
     */
    private $settingsFetcher;

    /**
     * @var ProfileFetcher
     */
    private $profileFetcher;

    /**
     * @var FormatMoney
     */
    private $moneyFormatter;

    private $bidFetcher;

    private $requisiteFetcher;

    /**
     * ResultsXmlGenerator constructor
     * @param XmlEncoderFactory $xmlEncoderFactory
     * @param Streebog $streebog
     * @param LotFetcher $lotFetcher
     * @param ProfileFetcher $profileFetcher
     * @param BidFetcher $bidFetcher
     * @param RequisiteFetcher $requisiteFetcher
     * @param FormatMoney $moneyFormatter
     * @param SettingsFetcher $settingsFetcher
     */
    public function __construct(
        XmlEncoderFactory $xmlEncoderFactory,
        Streebog $streebog,
        LotFetcher $lotFetcher,
        ProfileFetcher $profileFetcher,
        BidFetcher $bidFetcher,
        RequisiteFetcher $requisiteFetcher,
        FormatMoney $moneyFormatter,
        SettingsFetcher $settingsFetcher)
    {
        $this->xmlEncoderFactory = $xmlEncoderFactory;
        $this->streebog = $streebog;
        $this->lotFetcher = $lotFetcher;
        $this->settingsFetcher = $settingsFetcher;
        $this->profileFetcher = $profileFetcher;
        $this->moneyFormatter = $moneyFormatter;
        $this->bidFetcher = $bidFetcher;
        $this->requisiteFetcher = $requisiteFetcher;
    }

    /**
     * @param Id $procedureId
     * @param string|null $organizerComment
     * @param string|null $requisiteId
     * @return XmlDocument
     * @throws \Exception
     */
    public function generate(Id $procedureId, ?string $organizerComment, ?string $requisiteId): XmlDocument {
        $lot = $this->lotFetcher->forXmlProtocolsView(\App\ReadModel\Procedure\Lot\Filter\Filter::fromProcedure($procedureId->getValue()),1,50);

        $findSiteDomain = $this->settingsFetcher->findDetailByKey(Key::siteDomain());
        $findBuySellingProcedurePeriod = $this->settingsFetcher->findDetailByKey(Key::buySellingProcedurePeriod());

        $tradingDateTime = (new \DateTimeImmutable($lot['start_trading_date']));
        $tradingTime = $tradingDateTime->format('h:i');
        $tradingDate = $tradingDateTime->format('d.m.Y');


        $profileWinner = $this->profileFetcher->find($lot['winner_id']);
        $profileOrganizer = $this->profileFetcher->find($lot['profile_id']);


        $findMyBid = $this->bidFetcher->findApprovedBidByParticipant($lot['id'], $lot['winner_id']);

        $requisiteOrganizer = $this->requisiteFetcher->findDetail($requisiteId);

        $requisiteParticipant = $this->requisiteFetcher->findDetail($findMyBid->requisite_id);

        $ndsType = (new Nds($lot['nds']))->getLocalizedName();
        $lots = new ArrayCollection([[
                'lotNumber' => $lot['lot_number'],
                'documentName' => "Протокол заседания комиссии об определении победителя торгов в электронной форме (торговая процедура №{$lot['procedure_number']})",
                'tradingPlace' => "Открытый аукцион в электронной форме проводился в {$tradingTime} {$tradingDate} года на сайте {$findSiteDomain} в сети «Интернет».",
                'serviceDepartmentInfo' =>[
                    'fullName' => $lot['bailiffs_name'],
                    'productionNumber' => $lot['executive_production_number'],
                    'dateEnforcementProceedings' => $lot['date_enforcement_proceedings']
                ],
                'groundsBidding' => $lot['tender_basic'],
                'subjectBidding' => $lot['title'],
                'debtorInfo' => [
                    'fullName' => $lot['debtor_full_name']
                ]
        ]]);


        if ($profileWinner->isIndividualOrIndividualEntrepreneur()){
            $participantInfo = [
                'incorporatedForm' => $profileWinner->incorporated_form,
                'fullName' => $lot['full_title_organization'],
                'inn' => $profileWinner->certificate_subject_name_inn,
                'birthDay' => Filter::date($profileWinner->passport_birth_day),
                'legal_address' => $profileWinner->getLegalAddress(),
                'fact_address' => $profileWinner->getFactAddress(),
                'requisites' => [
                    'bankName' => $requisiteParticipant->bank_name,
                    'paymentAccount' => $requisiteParticipant->payment_account,
                    'bankBik' => $requisiteParticipant->bank_bik,
                    'correspondentAccount' => $requisiteParticipant->correspondent_account
                ]
            ];
        }

        if ($profileWinner->isLegalEntity()){
            $participantInfo = [
                'incorporatedForm' => $profileWinner->incorporated_form,
                'fullName' => $lot['full_title_organization'],
                'inn' => $profileWinner->certificate_subject_name_inn,
                'legal_address' => $profileWinner->getLegalAddress(),
                'fact_address' => $profileWinner->getFactAddress(),
                'ogrn' => $profileWinner->ogrn,
                'kpp' => $profileWinner->kpp,
                'requisites' => [
                    'bankName' => $requisiteParticipant->bank_name,
                    'paymentAccount' => $requisiteParticipant->payment_account,
                    'bankBik' => $requisiteParticipant->bank_bik,
                    'correspondentAccount' => $requisiteParticipant->correspondent_account
                ]
            ];
        }

        $currentTime = new \DateTime();


        $totalAmount = $this->moneyFormatter->money($lot['final_cost']);
        $totalAmount = $totalAmount->subtract($this->moneyFormatter->money($lot['deposit_amount']));
        $totalAmount = $totalAmount->getCurrency()." ".$totalAmount->getAmount();

        $lotProtocols = new ArrayCollection([
            'protocolName' => Type::$names[Type::resultProtocol()->getName()],
            'place' => $profileOrganizer->legal_city." ".$currentTime->format("d.m.Y H:i:s"),
            'procedureNumber' => $lot['procedure_number'],
            'representativeOrganizer' => $profileOrganizer->position." ".$profileOrganizer->getOwnerFullNameBid(),
            'biddingForm' => (new \App\Model\Work\Procedure\Entity\Type($lot['type_procedure']))->getLocalizedName(),
            'pricePresentationForm' => (new PriceForm($lot['bidding_form']))->getLocalizedName(),
            'winnerFullName' => $profileWinner->getOwnerFullNameBid(),
            'finalCost' => "{$this->moneyFormatter->formatAmount($lot['final_cost'])} ({$this->moneyFormatter->formatPrescription($lot['final_cost'])}), {$ndsType}",
            'rulesPayProperty' => "
            В течение {$findBuySellingProcedurePeriod} рабочих дней со дня подписания настоящего Протокола.
            Победитель торгов обязуется уплатить {$this->moneyFormatter->formatAmount($totalAmount)} ({$this->moneyFormatter->formatPrescription($totalAmount)}). 
            Оплата производиться на лицевой счет Организатора торгов, указанный в реквизитах сторон. 
            Назначение платежа: 
            Оплата стоимости имущества по Протоколу о результатах торгов (торговая процедура №{$lot['procedure_number']}), 
            должник {$lot['debtor_full_name']}.. 
            Факт оплаты стоимости имущества удостоверяется выпиской с лицевого счета Организатора торгов. 
            Непоступление денежных средств, в счет оплаты Имущества в сумме и в сроки, указанные в настоящем Протоколе, считается отказом Победителя торгов от исполнения обязательств по оплате Имущества. 
            В этом случае Организатор торгов вправе отказаться от исполнения своих обязательств по настоящему Протоколу, письменно уведомив Победителя торгов о прекращении действия настоящего Протокола. 
            Настоящий Протокол прекращает свое действие с момента направления Организатором торгов указанного уведомления, при этом Победитель торгов теряет право на получение Имущества и утрачивает внесенный задаток.",
            'rulesBuyingSelling' => "
            После поступления на счет Организатора торгов денежных средств в сумме {$this->moneyFormatter->formatAmount($totalAmount)} ({$this->moneyFormatter->formatPrescription($totalAmount)}), 
            но не ранее чем через {$findBuySellingProcedurePeriod} дней с даты подписания настоящего Протокола, Организатор торгов заключает с Победителем торгов договор купли-продажи имущества.
            ",

            'organizerInfo' => [
                'fullName' => $lot['full_title_organization'],
                'ogrn' => $profileOrganizer->ogrn,
                'inn' => $profileOrganizer->inn,
                'kpp' => $profileOrganizer->kpp,
                'legal_address' => $profileOrganizer->getLegalAddress(),
                'fact_address' => $profileOrganizer->getFactAddress(),
                'requisites' => [
                    'bankName' => $requisiteOrganizer->bank_name,
                    'paymentAccount' => $requisiteOrganizer->payment_account,
                    'bankBik' => $requisiteOrganizer->bank_bik,
                    'correspondentAccount' => $requisiteOrganizer->correspondent_account
                ]
            ],

            'participantInfo' => $participantInfo,
            'lots' => [$lots],
        ]);


        $content = $this->xmlEncoderFactory->create()->encode($lotProtocols, 'xml');

        return new XmlDocument($content, $this->streebog->getHash($content), Reason::none()->getName());

    }

}