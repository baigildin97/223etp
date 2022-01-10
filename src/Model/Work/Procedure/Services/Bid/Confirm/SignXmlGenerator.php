<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Bid\Confirm;


use App\Container\Model\User\Service\XmlEncoderFactory;
use App\ReadModel\Profile\DetailView;

class SignXmlGenerator
{
    /**
     * @var XmlEncoderFactory
     */
    private $xmlEncoderFactory;

    /**
     * SignXmlGenerator constructor.
     * @param XmlEncoderFactory $xmlEncoderFactory
     */
    public function __construct(XmlEncoderFactory $xmlEncoderFactory) {
        $this->xmlEncoderFactory = $xmlEncoderFactory;
    }

    public function generate(\App\ReadModel\Procedure\Bid\DetailView $detailView): string {
        return $this->xmlEncoderFactory->create()->encode([
            'owner' => $detailView->getOwnerFullName(),
            'statement' => "Подтверждаю свое участие в торгах по процедуре: №{$detailView->procedure_number}",
            'createdAt' => (new \DateTimeImmutable())->format("Y-m-d H:i:s")
        ], 'xml');
    }
}
