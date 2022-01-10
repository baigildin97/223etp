<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\Services\Procedure\Protocols;


use App\Container\Model\User\Service\XmlEncoderFactory;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\PriceForm;
use App\Model\Work\Procedure\Entity\Protocol\Reason;
use App\Model\Work\Procedure\Entity\Protocol\Type;
use App\ReadModel\Procedure\Lot\LotFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Services\Hash\Streebog;
use Doctrine\Common\Collections\ArrayCollection;

class CancellationProtocolResultXmlGenerator implements ProtocolGeneratorInterface
{

    /**
     * @var XmlEncoderFactory
     */
    private $xmlEncoderFactory;

    /**
     * @var Streebog
     */
    private $streebog;

    private $lotFetcher;

    private $profileFetcher;

    public function __construct(XmlEncoderFactory $xmlEncoderFactory,
                                Streebog $streebog,
                                LotFetcher $lotFetcher,
                                ProfileFetcher $profileFetcher
    )
    {
        $this->xmlEncoderFactory = $xmlEncoderFactory;
        $this->streebog = $streebog;
        $this->lotFetcher = $lotFetcher;
        $this->profileFetcher = $profileFetcher;
    }

    /**
     * @param Id $procedureId
     * @param string|null $organizerComment
     * @param string|null $requisiteId
     * @return XmlDocument
     */
    public function generate(Id $procedureId, ?string $organizerComment, ?string $requisiteId): XmlDocument{
        $lot = $this->lotFetcher->forXmlProtocolsView(\App\ReadModel\Procedure\Lot\Filter\Filter::fromProcedure($procedureId->getValue()), 1, 50);
        $profileOrganizer = $this->profileFetcher->find($lot['profile_id']);

        $lots = new ArrayCollection([[
            'lotNumber' => $lot['lot_number'],
            'serviceDepartmentInfo' => [
                'fullName' => $lot['bailiffs_name'],
                'productionNumber' => $lot['executive_production_number'],
            ],
            'groundsBidding' => $lot['tender_basic'],
       //     'subjectBidding' => $lot['title_of_item'],
            'debtorInfo' => [
                'fullName' => $lot['debtor_full_name']
            ]
        ]]);

        $protocol = new ArrayCollection([
            'protocolName' => Type::$names[Type::cancellationProtocolResult()->getName()],
            'procedureNumber' => $lot['procedure_number'],
            'biddingForm' => (new \App\Model\Work\Procedure\Entity\Type($lot['type_procedure']))->getLocalizedName(),
            'pricePresentationForm' => (new PriceForm($lot['bidding_form']))->getLocalizedName(),

            'organizerInfo' => [
                'fullName' => $lot['full_title_organization'],
                'ogrn' => $profileOrganizer->ogrn,
                'inn' => $profileOrganizer->inn,
                'kpp' => $profileOrganizer->kpp,
                'legal_address' => $profileOrganizer->getLegalAddress(),
                'fact_address' => $profileOrganizer->getFactAddress(),
            ],
            'organizerComment' => $organizerComment,
            'lots' => [$lots]
        ]);

        $content = $this->xmlEncoderFactory->create()->encode($protocol, 'xml');

        return new XmlDocument($content, $this->streebog->getHash($content), Reason::none()->getName());
    }

}