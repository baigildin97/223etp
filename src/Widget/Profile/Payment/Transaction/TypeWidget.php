<?php
declare(strict_types=1);
namespace App\Widget\Profile\Payment\Transaction;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TypeWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('transaction_type', [$this, 'type'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function type(Environment $twig, string $type): string {
        return $twig->render('widget/profile/payment/transaction/type.html.twig',[
            'type' => $type
        ]);
    }
}