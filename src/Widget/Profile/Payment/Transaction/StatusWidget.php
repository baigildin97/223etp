<?php
declare(strict_types=1);
namespace App\Widget\Profile\Payment\Transaction;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StatusWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('transaction_status', [$this, 'status'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function status(Environment $twig, string $status): string {
        return $twig->render('widget/profile/payment/transaction/status.html.twig',[
            'status' => $status
        ]);
    }
}