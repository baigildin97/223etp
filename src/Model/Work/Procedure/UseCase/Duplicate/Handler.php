<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Duplicate;


use App\Model\Flusher;
use App\Model\User\Entity\Profile\Organization\OrganizationRepository;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\Profile\Representative\RepresentativeRepository;
use App\Model\Work\Procedure\Entity\Contract;
use App\Model\Work\Procedure\Entity\Document\File;
use App\Model\Work\Procedure\Entity\Document\FileType;
use App\Model\Work\Procedure\Entity\Document\Id as FileId;
use App\Model\Work\Procedure\Entity\Document\ProcedureDocument;
use App\Model\Work\Procedure\Entity\Id;
use App\Model\Work\Procedure\Entity\Lot\Auction\Id as AuctionId;
use App\Model\Work\Procedure\Entity\Lot\Lot;
use App\Model\Work\Procedure\Entity\Lot\LotRepository;
use App\Model\Work\Procedure\Entity\Lot\Nds;
use App\Model\Work\Procedure\Entity\Lot\Reload;
use App\Model\Work\Procedure\Entity\PriceForm;
use App\Model\Work\Procedure\Entity\Procedure;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use App\Model\Work\Procedure\Entity\Status;
use App\Model\Work\Procedure\Entity\Type;
use App\Model\Work\Procedure\Services\Procedure\NumberGenerator;
use App\Services\Uploader\FileUploader;
use Exception;
use Money\Money;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Handler
{
    /**
     * @var Flusher
     */
    private $flusher;

    /**
     * @var ProcedureRepository
     */
    private $procedureRepository;

    /**
     * @var LotRepository
     */
    private $lotRepository;

    /**
     * @var ProfileRepository
     */
    private $profileRepository;

    /**
     * @var OrganizationRepository
     */
    private $organizationRepository;

    /**
     * @var RepresentativeRepository
     */
    private $representativeRepository;

    /**
     * @var NumberGenerator
     */
    private $numberGenerator;

    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @var false|resource
     */
    private $tempfile;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param ProcedureRepository $procedureRepository
     * @param LotRepository $lotRepository
     * @param ProfileRepository $profileRepository
     * @param OrganizationRepository $organizationRepository
     * @param RepresentativeRepository $representativeRepository
     * @param NumberGenerator $numberGenerator
     * @param FileUploader $fileUploader
     */
    public function __construct(
        Flusher $flusher,
        ProcedureRepository $procedureRepository,
        LotRepository $lotRepository,
        ProfileRepository $profileRepository,
        OrganizationRepository $organizationRepository,
        RepresentativeRepository $representativeRepository,
        NumberGenerator $numberGenerator,
        FileUploader $fileUploader
    )
    {
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
        $this->profileRepository = $profileRepository;
        $this->organizationRepository = $organizationRepository;
        $this->representativeRepository = $representativeRepository;
        $this->numberGenerator = $numberGenerator;
        $this->fileUploader = $fileUploader;
        $this->tempfile = tmpfile();
    }


    /**
     * @param Command $command
     * @throws Exception
     */
    /**
     * @param Command $command
     * @throws Exception
     */
    public function handle(Command $command)
    {
        $procedure = $this->procedureRepository->get(new Id($command->id));

        $profile = $this->profileRepository->getByUser(new \App\Model\User\Entity\User\Id($command->userId));
        $profile->checkExpiredContractPeriod();

        $organization = $this->organizationRepository->get($profile->getOrganization()->getId());
        $representative = $this->representativeRepository->get($profile->getRepresentative()->getId());

        $numberGenerator = $this->numberGenerator->next();

        $procedureNew = new Procedure(
            $procedureNewId = \App\Model\Work\Procedure\Entity\Id::next(),
            $numberGenerator,
            "Копия " . $procedure->getTitle(),
            new \DateTimeImmutable(),
            Contract::onSite(),
            new Type($procedure->getType()->getValue()),
            $procedure->getOrganizerFullName(),
            $procedure->getOrganizerEmail(),
            $procedure->getOrganizerPhone(),
            Status::new(),
            $profile,
            $organization,
            $representative,
            new PriceForm($procedure->getPricePresentationForm()->getName()),
            $procedure->getInfoPointEntry(),
            $procedure->getInfoTradingVenue(),
            $procedure->getInfoBiddingProcess(),
            $procedure->getTenderingProcess(),
            $command->clientIp
        );


        foreach ($procedure->getLots() as $lot) {
            $auction = $lot->getAuction();
            $procedureNew->addLot(
                $lot->getIdNumber(),
                $lot->getArrestedPropertyType(),
                $lot->getReloadLot(),
                $lot->getTenderBasic(),
                $lot->getNds(),
                $lot->getDateEnforcementProceedings(),
                $lot->getStartDateOfApplications(),
                $lot->getClosingDateOfApplications(),
                $lot->getSummingUpApplications(),
                $lot->getDebtorFullName(),
                $lot->getDebtorFullNameDateCase(),
                $lot->getAdvancePaymentTime(),
                $lot->getRequisite(),
                $lot->getLotName(),
                $lot->getRegion(),
                $lot->getLocationObject(),
                $lot->getAdditionalObjectCharacteristics(),
                $lot->getStartingPrice(),
                $lot->getDepositAmount(),
                $lot->getDepositPolicy(),
                $lot->getBailiffsName(),
                $lot->getBailiffsNameDativeCase(),
                $lot->getPledgeer(),
                $lot->getExecutiveProductionNumber(),
                $command->clientIp,
                $auction->getOfferAuctionTime(),
                $auction->getAuctionStep(),
                $auction->getStartTradingDate()
            );
        }
		
	    
		
	
        foreach ($procedure->getDocuments() as $document) {
            $uploaded = $this->fileUploader->copyFile(
                $document->file,
                true
            ); 

            $procedureNew->dublicateFile(
						 $document->file->getPath(),
                         $document->file->getName(),
                         $uploaded->getSize(),
                         $document->file->getRealName(),
                         $uploaded->getHash(),
						 $document->fileType->getValue()
			);
			
        }

     
		$this->procedureRepository->add($procedureNew);
		$this->flusher->flush();


    }
}