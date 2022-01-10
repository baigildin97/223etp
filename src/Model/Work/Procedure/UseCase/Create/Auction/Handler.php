<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Create\Auction;



use App\Model\Flusher;
use App\Model\User\Entity\Profile\Organization\OrganizationRepository;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\Profile\Representative\RepresentativeRepository;
use App\Model\User\Entity\User\Id;
use App\Model\Work\Procedure\Entity\ConductingType;
use App\Model\Work\Procedure\Entity\Contract;
use App\Model\Work\Procedure\Entity\Lot\Nds;
use App\Model\Work\Procedure\Entity\Lot\Reload;
use App\Model\Work\Procedure\Entity\PriceForm;
use App\Model\Work\Procedure\Entity\Procedure;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use App\Model\Work\Procedure\Entity\Status;
use App\Model\Work\Procedure\Entity\Type;
use App\Model\Work\Procedure\Services\Procedure\NumberGenerator;
use Exception;
use Money\Money;

/**
 * Class Handler
 * @package App\Model\Work\Procedure\UseCase\Create
 */
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
     * @var \App\Model\Work\Procedure\Services\Procedure\Lot\NumberGenerator
     */
    private $numberGeneratorLot;

    /**
     * Handler constructor.
     * @param Flusher $flusher
     * @param ProcedureRepository $procedureRepository
     * @param ProfileRepository $profileRepository
     * @param OrganizationRepository $organizationRepository
     * @param RepresentativeRepository $representativeRepository
     * @param NumberGenerator $numberGenerator
     * @param  \App\Model\Work\Procedure\Services\Procedure\Lot\NumberGenerator $numberGeneratorLot
     */
    public function __construct(Flusher $flusher,
                                ProcedureRepository $procedureRepository,
                                ProfileRepository $profileRepository,
                                OrganizationRepository $organizationRepository,
                                RepresentativeRepository $representativeRepository,
                                NumberGenerator $numberGenerator,
                                \App\Model\Work\Procedure\Services\Procedure\Lot\NumberGenerator $numberGeneratorLot
    ){
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
        $this->profileRepository = $profileRepository;
        $this->organizationRepository = $organizationRepository;
        $this->representativeRepository = $representativeRepository;
        $this->numberGenerator = $numberGenerator;
        $this->numberGeneratorLot = $numberGeneratorLot;
    }

    /**
     * @param Command $command
     * @throws Exception
     */
    public function handle(Command $command): void
    {
        $profile = $this->profileRepository->getByUser(new Id($command->userId));
        $profile->checkExpiredContractPeriod();

        $organization = $this->organizationRepository->get($profile->getOrganization()->getId());
        $representative = $this->representativeRepository->get($profile->getRepresentative()->getId());

        $numberGenerator = $this->numberGenerator->next();

        $procedure = new Procedure(
            new \App\Model\Work\Procedure\Entity\Id($command->newProcedureId),
            $numberGenerator,
            $command->procedureName,
            new \DateTimeImmutable(),
            Contract::onSite(),
            Type::auction(),
            $command->organizerFullName,
            $command->organizerEmail,
            $command->organizerPhone,
            Status::new(),
            $profile,
            $organization,
            $representative,
            new PriceForm($command->pricePresentationForm),
            $command->infoPointEntry,
            $command->infoTradingVenue,
            $command->infoBiddingProcess,
            $command->tenderingProcess,
            $command->clientIp,
            new ConductingType($command->procedureType),
        );

        $this->procedureRepository->add($procedure);

        $this->flusher->flush();
    }
}