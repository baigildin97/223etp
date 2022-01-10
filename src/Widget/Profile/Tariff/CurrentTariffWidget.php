<?php
declare(strict_types=1);
namespace App\Widget\Profile\Tariff;


use App\ReadModel\Profile\Tariff\TariffFetcher;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CurrentTariffWidget extends AbstractExtension
{
    private $tariffFetcher;

    public function __construct(TariffFetcher $tariffFetcher){
        $this->tariffFetcher = $tariffFetcher;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('currentTariff', [$this, 'currentTariff'], ['needs_environment' => true, 'is_safe' => ['html']])
        ];
    }

    public function currentTariff(Environment $twig, ?string $tariff_id): string
    {
        if($tariff_id !== null) {
            $findInfoTariff = $this->tariffFetcher->findDetail($tariff_id);
            $findInfoSubscribeTariff = $this->tariffFetcher->findSubscribeDetail($tariff_id);
        }
        return $twig->render('widget/profile/tariff/current_tariff.html.twig', [
            'tariff' => $findInfoTariff ?? null,
            'subscribe_tariff' => $findInfoSubscribeTariff ?? null
        ]);
    }
}


