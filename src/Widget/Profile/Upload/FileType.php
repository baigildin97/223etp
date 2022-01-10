<?php
declare(strict_types=1);
namespace App\Widget\Profile\Upload;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FileType extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('fileType', [$this, 'fileType'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function fileType(Environment $twig, string $fileType): string {
        $getNameFileType = (new \App\Model\User\Entity\Profile\Document\FileType($fileType))->getLocalizedName();

        return $twig->render('widget/profile/upload/file_type.html.twig',[
            'file_type' => $getNameFileType
        ]);
    }
}