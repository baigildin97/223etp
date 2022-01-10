<?php
declare(strict_types=1);
namespace App\Widget\Auction;


use App\Helpers\FormatMoney;
use App\Model\Work\Procedure\Entity\Type;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StateAuctionInterest extends AbstractExtension
{
    private $formatMoney;

    public function __construct(FormatMoney $formatMoney)
    {
        $this->formatMoney = $formatMoney;
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('stateAuctionInterest', [$this, 'stateAuctionInterest'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function stateAuctionInterest(Environment $twig, string $type): string {
        $type = new Type($type);
        return $twig->render('widget/auction/state_auction_interest.html.twig',[
            'type' => $type
        ]);
    }
}