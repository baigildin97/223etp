<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Procedure\Notifications;


use App\Container\Model\User\Service\XmlEncoderFactory;
use App\Helpers\FormatMoney;
use App\Model\Admin\Entity\Settings\Key;
use App\Model\Work\Procedure\Entity\Lot\Nds;
use App\Model\Work\Procedure\Entity\PriceForm;
use App\Model\Work\Procedure\Entity\Type;
use App\ReadModel\Admin\Settings\SettingsFetcher;
use App\ReadModel\Procedure\DetailView;
use App\ReadModel\Procedure\Lot\LotFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use Doctrine\Common\Collections\ArrayCollection;

class XmlNotifyGenerator
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
     * @param \App\Model\Work\Procedure\Entity\XmlDocument\Type $type
     * @param string $organizerComment
     * @return string
     */
    public function generate(DetailView $detailView, \App\Model\Work\Procedure\Entity\XmlDocument\Type $type, string $organizerComment): string
    {

        $nameOrganization = $this->settingsFetcher->findDetailByKey(Key::fullNameOrganization());

        $lots = $this->lotFetcher->all(
            \App\ReadModel\Procedure\Lot\Filter\Filter::fromProcedure($detailView->id),
            1, 100
        );


        $lotsCollection = new ArrayCollection([]);
        foreach ($lots->getItems() as $lot){
            $lotsCollection->add(
                [
                    'number' => $lot['procedure_number'].'-'.$lot['id_number'],
                    'name' => "Подвергнутое аресту {$lot['bailiffs_name_dative_case']} по исполнительному производству {$lot['executive_production_number']}, принадлежащее должнику {$lot['debtor_full_name_date_case']} и находящееся в залоге у {$lot['pledgeer']}, {$lot['lot_name']}",
                    'startingPrice' => $this->moneyFormat($lot['starting_price']),
                    'depositAmount' => $this->moneyFormat($lot['deposit_amount']),
                    'nds' => (new Nds($lot['nds']))->getLocalizedName(),
                    'auctionStep' => $this->moneyFormat($lot['auction_step']),
                ]
            );
        }

        $content = [
            'documentName' => $type->getLocalizedName(),
            'procedureType' => (new Type($detailView->type))->getLocalizedName(),
            'pricePresentationForm' => (new PriceForm($detailView->price_presentation_form))->getLocalizedName(),
            'nameOrganization' => $nameOrganization,
            'organizerFullName' => $detailView->organization_full_title,
            'lots' => $lotsCollection,
            'organizerComment' => $organizerComment
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