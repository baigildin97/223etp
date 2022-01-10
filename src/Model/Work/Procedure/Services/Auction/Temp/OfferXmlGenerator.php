<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Auction\Temp;

use App\Container\Model\User\Service\XmlEncoderFactory;
use App\Helpers\FormatMoney;
use App\Model\User\Entity\Profile\Profile;
use App\ReadModel\Auction\AuctionFetcher;
use App\ReadModel\Procedure\Bid\BidFetcher;
use App\ReadModel\Profile\DetailView;
use Money\Money;

class OfferXmlGenerator
{
    /**
     * @var XmlEncoderFactory
     */
    private $xmlEncoderFactory;

    /**
     * @var BidFetcher
     */
    private $bidFetcher;

    /**
     * @var AuctionFetcher
     */
    private $auctionFetcher;

    /**
     * @var FormatMoney
     */
    private $formatMoney;

    /**
     * TempXmlGenerator constructor.
     * @param XmlEncoderFactory $xmlEncoderFactory
     * @param BidFetcher $bidFetcher
     * @param AuctionFetcher $auctionFetcher
     * @param FormatMoney $formatMoney
     */
    public function __construct(
        XmlEncoderFactory $xmlEncoderFactory,
        BidFetcher $bidFetcher,
        AuctionFetcher $auctionFetcher,
        FormatMoney $formatMoney
    ) {
        $this->xmlEncoderFactory = $xmlEncoderFactory;
        $this->bidFetcher = $bidFetcher;
        $this->auctionFetcher = $auctionFetcher;
        $this->formatMoney = $formatMoney;
    }

    /**
     * @param string $bidId
     * @param string $auctionId
     * @param string $cost
     * @param string $clientIp
     * @return string
     */
    public function generate(string $bidId, string $auctionId, string $cost, string $clientIp): string {
        $bid = $this->bidFetcher->findDetail($bidId);
        $auction = $this->auctionFetcher->findDetail($auctionId);
        $cost = $this->formatMoney->money($auction->current_cost)->add(Money::RUB($cost*100));
        return $this->xmlEncoderFactory->create()->encode([
            'offer' => [
                'owner' => $bid->getOwnerFullName(),
                'cost' => $this->formatMoney->convertMoneyToString($cost),
                'clientIp' => $clientIp
            ],
        ], 'xml');
    }
}