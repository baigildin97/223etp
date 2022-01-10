<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\Services\Procedure\Protocols;

use App\Container\Model\User\Service\XmlEncoderFactory;
use App\Model\User\Entity\Profile\Organization\IncorporationForm;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\Lot\Bid\Status;
use App\Model\Work\Procedure\Entity\PriceForm;
use App\Model\Work\Procedure\Entity\Protocol\Reason;
use App\Model\Work\Procedure\Entity\Protocol\Type;
use App\ReadModel\Procedure\Bid\BidFetcher;
use App\ReadModel\Procedure\Bid\Filter\Filter;
use App\ReadModel\Procedure\Lot\LotFetcher;
use App\ReadModel\Procedure\ProcedureFetcher;
use App\ReadModel\Profile\ProfileFetcher;
use App\Services\Hash\Streebog;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Request;

class ReviewBidsXmlGenerator implements ProtocolGeneratorInterface
{

    /**
     * @var string
     */
    private $nextStatus;

    /**
     * @var XmlEncoderFactory
     */
    private $xmlEncoderFactory;

    /**
     * @var LotFetcher
     */
    private $lotFetcher;

    /**
     * @var BidFetcher
     */
    private $bidFetcher;

    private $streebog;

    /**
     * @var ProfileFetcher
     */
    private $profileFetcher;

    /**
     * ReviewBidsXmlGenerator constructor.
     * @param XmlEncoderFactory $xmlEncoderFactory
     * @param LotFetcher $lotFetcher
     * @param BidFetcher $bidFetcher
     * @param Streebog $streebog
     * @param ProfileFetcher $profileFetcher
     */
    public function __construct(
        XmlEncoderFactory $xmlEncoderFactory,
        LotFetcher $lotFetcher,
        BidFetcher $bidFetcher,
        Streebog $streebog,
        ProfileFetcher $profileFetcher
    )
    {
        $this->xmlEncoderFactory = $xmlEncoderFactory;
        $this->lotFetcher = $lotFetcher;
        $this->bidFetcher = $bidFetcher;
        $this->streebog = $streebog;
        $this->nextStatus = null;
        $this->profileFetcher = $profileFetcher;
    }


    /**
     * @param Id $procedureId
     * @param string|null $organizerComment
     * @param string|null $requisiteId
     * @return XmlDocument
     * @throws Exception
     */
    public function generate(Id $procedureId, ?string $organizerComment, ?string $requisiteId): XmlDocument
    {

        $lot = $this->lotFetcher->forXmlProtocolsView(\App\ReadModel\Procedure\Lot\Filter\Filter::fromProcedure($procedureId->getValue()), 1, 50);

        $bids = $this->bidFetcher->all(Filter::bidInStatusesForLot($lot['id'], [Status::APPROVED, Status::REJECT]), 1, 100);

        $arrayBids = new ArrayCollection();
        foreach ($bids->getItems() as $bid) {
            $findProfileParticipant = $this->profileFetcher->find($bid['participant_id']);

            $arrayBids->add([
                'fullNameParticipant' => $findProfileParticipant->getFullNameAccount(),
                'status' => $bid['status'],
                'localizedStatus' => (new Status($bid['status']))->getLocalizedName()
            ]);
        }

        $received = null;
        if ($arrayBids->count() < 2) {
            $this->nextStatus = Reason::lessTwoBids()->getName();
        }

        $searchApproved = new Comparison('status', '=', Status::APPROVED);
        $criteria = new Criteria();
        $criteria->where($searchApproved);
        $matched = $arrayBids->matching($criteria);

        if ($matched->count() < 2) {
            $this->nextStatus = Reason::approveLessTwoBids()->getName();
        } else {
            $this->nextStatus = Reason::approveMoreTwoBids()->getName();
            $received = "поступило заявок: " . $arrayBids->count();
        }


        $currentDate = new \DateTimeImmutable();
        $lots = new ArrayCollection([[
            'lotNumber' => $lot['lot_number'],
            'subjectBidding' => $lot['title'],
            'debtorInfo' => [
                'fullName' => $lot['debtor_full_name']
            ],
            'serviceDepartmentInfo' => [
                'fullName' => $lot['bailiffs_name'],
                'productionNumber' => $lot['executive_production_number'],
            ],
            'results' => [
                'bids' => [
                    $arrayBids,
                ],
                'comment' => $this->nextStatus === Reason::approveMoreTwoBids()->getName() ? ' ' : (new Reason($this->nextStatus))->getLocalizedName()
            ]
        ]]);

        $content = $this->xmlEncoderFactory->create()->encode([
            'protocolName' => Type::$names[Type::TYPE_SUMMARIZING_RESULTS_RECEIVING_BIDS],
            'procedureNumber' => $lot['procedure_number'],
            'firstBlock' => "на участие в торгах по продаже подвергнутого аресту " . $lot['bailiffs_name_dative_case'] . " по исполнительному производству: №" . $lot['executive_production_number'] . " от " . $lot['date_enforcement_proceedings'] . ", принадлежащее должнику " . $lot['debtor_full_name_date_case'] . "
             имущество: " . $lot['title'] . "",
            'city' => $lot['legal_city'] . " " . $currentDate->format("d.m.Y"),
            'twoBlock' => "Комиссия по проведению торгов в полном составе
            констатирует, что по состоянию на  " . $this->date($lot['closing_date_of_applications']) . " на участие в торгах по продаже подвергнутого аресту " . $lot['bailiffs_name_dative_case'] . " по исполнительному производству: №" . $lot['executive_production_number'] . " от " . $lot['date_enforcement_proceedings'] . ", принадлежащее должнику " . $lot['debtor_full_name_date_case'] . " имущество: " . $lot['title'] . ",
            " . $received . "",
            'biddingForm' => (new \App\Model\Work\Procedure\Entity\Type($lot['type_procedure']))->getLocalizedName(),
            'pricePresentationForm' => (new PriceForm($lot['bidding_form']))->getLocalizedName(),
            'biddingProcess' => $lot['bidding_process'],
            'organizerInfo' => [
                'fullName' => $lot['full_title_organization']
            ],
            'lots' => [$lots],
            'countBids' => $arrayBids->count()
        ], 'xml');


        return new XmlDocument($content, $this->streebog->getHash($content), $this->nextStatus);
    }

    /**
     * @param $data
     * @return string
     */
    private function date($data)
    {
        return \App\Helpers\Filter::date($data);
    }

}