<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\Services\Procedure\Protocols;


use App\Container\Model\User\Service\XmlEncoderFactory;
use App\Helpers\FormatMoney;
use App\Model\Admin\Entity\Settings\Key;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\Lot\Bid\Status;
use App\Model\Work\Procedure\Entity\Lot\Nds;
use App\Model\Work\Procedure\Entity\Lot\NdsType;
use App\Model\Work\Procedure\Entity\PriceForm;
use App\Model\Work\Procedure\Entity\Protocol\Reason;
use App\Model\Work\Procedure\Entity\Protocol\Type;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\ReadModel\Auction\Offers\OffersFetcher;
use App\ReadModel\Procedure\Bid\BidFetcher;
use App\ReadModel\Procedure\Bid\Filter\Filter;
use App\ReadModel\Procedure\Lot\LotFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Services\Hash\Streebog;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Money\Currency;
use Money\Money;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class WinningXmlGenerator implements ProtocolGeneratorInterface
{
    /**
     * @var string
     */
    public $nextStatus;

    /**
     * @var XmlEncoderFactory
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
     * @var OffersFetcher
     */
    private $offersFetcher;

    /**
     * @var FormatMoney
     */
    private $moneyFormatter;

    /**
     * @var BidFetcher
     */
    private $bidFetcher;

    /**
     * @var ProfileFetcher
     */
    private $profileFetcher;

    /**
     * @var SettingsFetcher
     */
    private $settingsFetcher;

    /**
     * WinningXmlGenerator constructor.
     * @param XmlEncoderFactory $xmlEncoderFactory
     * @param Streebog $streebog
     * @param LotFetcher $lotFetcher
     * @param BidFetcher $bidFetcher
     * @param OffersFetcher $offersFetcher
     * @param FormatMoney $moneyFormatter
     * @param ProfileFetcher $profileFetcher
     * @param SettingsFetcher $settingsFetcher
     */
    public function __construct(
        XmlEncoderFactory $xmlEncoderFactory,
        Streebog $streebog,
        LotFetcher $lotFetcher,
        BidFetcher $bidFetcher,
        OffersFetcher $offersFetcher,
        FormatMoney $moneyFormatter,
        ProfileFetcher $profileFetcher,
        SettingsFetcher $settingsFetcher
    )
    {
        $this->xmlEncoderFactory = $xmlEncoderFactory;
        $this->streebog = $streebog;
        $this->lotFetcher = $lotFetcher;
        $this->offersFetcher = $offersFetcher;
        $this->moneyFormatter = $moneyFormatter;
        $this->bidFetcher = $bidFetcher;
        $this->profileFetcher = $profileFetcher;
        $this->settingsFetcher = $settingsFetcher;
    }

    /**
     * TODO - выводится только 1 лот!!! Предполагается 1 лот - 1 процедура.
     * @param Id $procedureId
     * @return XmlDocument
     */
    public function generate(Id $procedureId, ?string $organizerComment,  ?string $requisiteId): XmlDocument
    {
        $lot = $this->lotFetcher->forXmlProtocolsView(\App\ReadModel\Procedure\Lot\Filter\Filter::fromProcedure($procedureId->getValue()), 1, 50);


        $reloadLot = $lot['reload_lot'] === true ? ' повторных' : ' ';


        $tradingDateTime = (new \DateTimeImmutable($lot['start_trading_date']));

        $tradingTime = $tradingDateTime->format('h:i');
        $tradingDate = $tradingDateTime->format('d.m.Y');

        $offers = new ArrayCollection([]);
        $countConfirmBidsByAuctionId =  $this->bidFetcher->countConfirmBidsByAuctionId($lot['id']);
        $countOffers = $this->offersFetcher->countOffersByAuctionId($lot['auction_id']);


        $findSiteDomain = $this->settingsFetcher->findDetailByKey(Key::siteDomain());

        $this->nextStatus = null;
        $results = null;
        $resolution = null;

        if ($countConfirmBidsByAuctionId === 0){
            $this->nextStatus = Reason::zeroConfirmBids()->getName();
        }else if ($countConfirmBidsByAuctionId === 1){
            $this->nextStatus = Reason::confirmedLessTwoBids()->getName();
        } else{
            if ($countOffers === 0) {
                $this->nextStatus = Reason::zeroOffers()->getName();
            }else{
                $findOffersByAuctionId = $this->offersFetcher->all(
                    \App\ReadModel\Auction\Offers\Filter\Filter::forAuctionId($lot['auction_id']),
                    1,
                    100
                );
                foreach ($findOffersByAuctionId as $row) {

                    $findProfileParticipant = $this->profileFetcher->find($row['participant_id']);

                    $offers->add([
                        'bidNumber' => $row['bid_number'],
                        'offerCreatedAt' => $row['created_at'],
                        'owner' => $findProfileParticipant->getFullNameAccount(),
                        'price' => $this->moneyFormatter->currencyAsSymbol($row['cost'])
                    ]);

                }

                $profileWinner = $this->profileFetcher->find($lot['winner_id']);

                $pledgeer = '';
                if ($lot['pledgeer'] !== null){
                    $pledgeer = "находящееся в залоге у {$lot['pledgeer']},";
                }

                $resolution = "Признать победителем {$reloadLot} торгов по продаже подвергнутого аресту 
                    {$lot['bailiffs_name']} по исполнительному производству: №{$lot['executive_production_number']}, 
                     {$pledgeer} и принадлежащее должнику {$lot['debtor_full_name_date_case']}, 
                    имущество: {$lot['lot_name']}, 
                    Участника: {$profileWinner->getOwnerFullNameBid()}, 
                    ИНН {$profileWinner->certificate_subject_name_inn}, {$profileWinner->getLegalAddress()}
                    ";

            }
        }



        $pledgeer = '';
        if ($lot['pledgeer'] !== null){
            $pledgeer = "находящееся в залоге у {$lot['pledgeer']},";
        }

        $ndsType = (new Nds($lot['nds']))->getLocalizedName();
        $lotProtocols = new ArrayCollection([
            'protocolName' => Type::$names[Type::WINNER_PROTOCOL],
            'procedureNumber' => $lot['procedure_number'],
            'lots' => [[[
                    'nextStatus' => $this->nextStatus ?? ' ',
                    'lotNumber' => $lot['lot_number'],
                    'documentName' => "Протокол заседания комиссии об определении победителя торгов в электронной форме (торговая процедура №{$lot['procedure_number']})",
                    'statementText' => "Подведение итогов {$reloadLot} торгов по продаже подвергнутого аресту {$lot['bailiffs_name']} по исполнительному производству: №{$lot['executive_production_number']}, {$pledgeer} и принадлежащее должнику {$lot['debtor_full_name_date_case']}, имущество: {$lot['lot_name']}",
                    'pricePresentationForm' => (new PriceForm($lot['bidding_form']))->getLocalizedName(),
                    'tradingPlace' => "Открытый аукцион в электронной форме проводился в {$tradingTime} {$tradingDate} года на сайте {$findSiteDomain} в сети «Интернет».",
                    'initialLotPrice' => "{$this->moneyFormatter->formatAmount($lot['starting_price'])} ({$this->moneyFormatter->formatPrescription($lot['starting_price'])}), {$ndsType}.",
                    'offers' => [$offers],
                    'resolution' => $resolution,
                    'salePrice' => "{$this->moneyFormatter->formatAmount($lot['final_cost'])} ({$this->moneyFormatter->formatPrescription($lot['final_cost'])}), {$ndsType}",
                    'commission' => 'Присутствовали все члены комиссии.',
                    'deadlineSign' => 'По итогам торгов в тот же день между Победителем торгов и Организатором торгов электронной подписью подписывается Протокол о результатах торгов. Если Победитель торгов в установленные сроки не подписал электронной подписью Протокол о результатах торгов, он лишается права на приобретение имущества, сумма внесенного им задатка не возвращается.'
            ]]],
            'results' => [
                'comments' => $this->nextStatus === null ? ' ' : (new Reason($this->nextStatus))->getLocalizedName()]
        ]);


        $content = $this->xmlEncoderFactory->create()->encode($lotProtocols, 'xml');

        return new XmlDocument($content, $this->streebog->getHash($content), Reason::none()->getName());
    }


    private function httpHost(): string
    {
        return $_SERVER['HTTP_HOST'];
    }
}