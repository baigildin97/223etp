<?php
declare(strict_types=1);
namespace App\Widget\Procedure;


use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class UploadedFilesWidget extends AbstractExtension
{
    public function getFunctions(): array {
        return [
            new TwigFunction('uploadedFiles', [$this, 'uploadedFiles'],
                ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function uploadedFiles(Environment $twig, array $files, string $certificateThumbprint, string $procedureId): string {
        return $twig->render('widget/procedure/uploaded_files.html.twig',[
            'files' => $files,
            'certificate_thumbprint' => $certificateThumbprint,
            'procedure_id' => $procedureId
        ]);
    }
}