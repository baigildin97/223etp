<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Bid\Organizer;


use App\Container\Model\User\Service\XmlEncoderFactory;
use App\Model\Work\Procedure\Entity\Lot\Bid\TempStatus;
use App\ReadModel\Procedure\Bid\DetailView;
use App\ReadModel\Procedure\Bid\Document\DocumentFetcher;
use App\ReadModel\Procedure\Bid\Document\Filter\Filter;
use App\Services\Uploader\FileUploader;

class SignXmlGenerator
{
    /**
     * @var XmlEncoderFactory
     */
    private $xmlEncoderFactory;

    /**
     * @var DocumentFetcher
     */
    private $documentFetcher;

    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * SignXmlGenerator constructor.
     * @param XmlEncoderFactory $xmlEncoderFactory
     * @param DocumentFetcher $documentFetcher
     * @param FileUploader $fileUploader
     */
    public function __construct(
        XmlEncoderFactory $xmlEncoderFactory,
        DocumentFetcher $documentFetcher,
        FileUploader $fileUploader
    ) {
        $this->xmlEncoderFactory = $xmlEncoderFactory;
        $this->documentFetcher = $documentFetcher;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @param DetailView $detailView
     * @return bool|false|float|int|string
     */
    public function generate(DetailView $detailView): string {
        $document = $this->documentFetcher->all(
            Filter::fromBid($detailView->id),1, 100
        );
        $docArray = [];

        foreach ($document->getItems() as $item){
            $docArray[] = [
                'documentName' => $item['document_name'],
                'fileRealName' => $item['file_real_name'],
                'fileName' => $item['file_real_name'],
                'url' => $this->fileUploader->generateUrl($item['file_path'].'/'.$item['file_name']),
                'hash' => $item['file_hash'],
                'sign' => $item['file_sign'] ?? '',
                'uploaderIp' => $item['participant_ip'],
                'createdAt' => $item['created_at']
            ];
        }

        if($detailView->temp_status === TempStatus::approved()->getName()) {
            $content = "
             Организатор: $detailView->full_title_organization
             Одобрил заявку 
            ";
        }elseif ($detailView->temp_status === TempStatus::reject()->getName()){
            $content = "
             Организатор: $detailView->full_title_organization
             Отклонил заявку по причине: $detailView->organizer_comment
            ";
        }

        return $this->xmlEncoderFactory->create()->encode([
            'content' => $content,
            'cause' => $detailView->organizer_comment ?? ' ',
            'documents' => [$docArray],
            'causeReject' => $detailView->cause_reject,
            'createdAt' => $detailView->created_at
        ], 'xml');
    }

}
