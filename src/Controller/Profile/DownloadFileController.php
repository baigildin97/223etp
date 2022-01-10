<?php
declare(strict_types=1);
namespace App\Controller\Profile;

use App\ReadModel\Profile\Document\DocumentFetcher;
use App\Services\Uploader\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class DownloadFileController extends AbstractController
{
    private $tempfile;

    public function __construct()
    {
        $this->tempfile = tmpfile();
    }

    /**
     * @param string $file_id
     * @param DocumentFetcher $documentFetcher
     * @return BinaryFileResponse
     * @Route("/profile/download/{file_id}", name="profile.download")
     */
    public function downloadFile(string $file_id, DocumentFetcher $documentFetcher, FileUploader $fileUploader): BinaryFileResponse
    {
        $file = $documentFetcher->getDetail($file_id);

        if (!is_null($file)) {
            $formattedFileRealName = str_replace('.', '_', $file->file_real_name);

            $zipfile = stream_get_meta_data($this->tempfile)['uri'];
            $generateUrlFile = $fileUploader->generateUrl($file->file_path . '/' . $file->file_name);
            $zip = new \ZipArchive();

            $extension = explode('.', $file->file_name);
            $zip->open($zipfile, \ZipArchive::CREATE);
            $zip->addFromString($file->file_real_name.'.'.$extension[1], file_get_contents($generateUrlFile));

            $zip->addFromString('hash.txt', $file->file_hash ?? '');
            $zip->addFromString('sign.sig', $file->file_sign ?? '');
            $zip->close();

            $response = new BinaryFileResponse($zipfile);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $formattedFileRealName . '.zip');
        } else
            $response = new BinaryFileResponse($file_id, Response::HTTP_NOT_FOUND);

        return $response;
    }

    /**
     * @param DocumentFetcher $documentFetcher
     * @param string $document_id
     * @return BinaryFileResponse
     * @Route("profile/documents/download/{document_id}", name="profile.documents.download")
     */
    public function downloadProfileXml(DocumentFetcher $documentFetcher, string $document_id): Response
    {
        $document = $documentFetcher->getForDownload($document_id);

        if ($document) {
            $formattedFileRealName = 'profile_'.$document->created_at;

            $zipfile = stream_get_meta_data($this->tempfile)['uri'];

            $zip = new \ZipArchive();

            $zip->open($zipfile, \ZipArchive::CREATE);
            $zip->addFromString('profile.xml', $document->xml);
            $zip->addFromString('hash.txt', $document->hash);
            $zip->addFromString('sign.sig', $document->sign);
            $zip->close();

            $response = new BinaryFileResponse($zipfile);
            $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $formattedFileRealName . '.zip');
        }
        else {
            $this->addFlash('error', 'Не удалось скачать документ');
            return $this->redirectToRoute('profile.documents');
        }

        return $response;
    }

    public function __destruct()
    {
        fclose($this->tempfile);
    }
}