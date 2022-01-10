<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Action\Open\Create;

use App\Model\Flusher;
use App\Model\User\Entity\Profile\Organization\OrganizationRepository;
use App\Model\User\Entity\Profile\ProfileRepository;
use App\Model\User\Entity\Profile\Representative\RepresentativeRepository;
use App\Model\User\Entity\User\Id;
use App\Model\Work\Procedure\Entity\Contract;
use App\Model\Work\Procedure\Entity\IdNumber;
use App\Model\Work\Procedure\Entity\PriceForm;
use App\Model\Work\Procedure\Entity\Procedure;
use App\Model\Work\Procedure\Entity\ProcedureRepository;
use App\Model\Work\Procedure\Entity\Status;
use App\Model\Work\Procedure\Entity\Type;
use App\Model\Work\Procedure\Services\Procedure\NumberGenerator;

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

    public function __construct(Flusher $flusher,
                                ProcedureRepository $procedureRepository,
                                ProfileRepository $profileRepository,
                                OrganizationRepository $organizationRepository,
                                RepresentativeRepository $representativeRepository,
                                NumberGenerator $numberGenerator
    ){
        $this->flusher = $flusher;
        $this->procedureRepository = $procedureRepository;
        $this->profileRepository = $profileRepository;
        $this->organizationRepository = $organizationRepository;
        $this->representativeRepository = $representativeRepository;
        $this->numberGenerator = $numberGenerator;
    }

    public function handle(Command $command): void{

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
            new Type($command->procedureType),
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
            $command->clientIp
        );


        $this->procedureRepository->add($procedure);

        $this->flusher->flush();

    }

}
