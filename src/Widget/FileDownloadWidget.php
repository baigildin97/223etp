<?php
declare(strict_types=1);

namespace App\Widget;


use App\Helpers\FormatMoney;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FileDownloadWidget extends AbstractExtension
{
    public function __construct(FormatMoney $formatMoney)
    {
        $this->formatMoney = $formatMoney;
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('fileDownload', [$this, 'fileDownload'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function fileDownload(Environment $twig, string $fileId, $fileType): string {

        return $twig->render('widget/file_download.html.twig',[
            'fileId' => $fileId,
            'fileType' => $fileType
        ]);
    }
}