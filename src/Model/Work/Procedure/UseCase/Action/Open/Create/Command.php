<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Action\Open\Create;


use Symfony\Component\Validator\Constraints as Assert;

class Command{

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $procedureType;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $organizerFullName;

    /**
     * @var string
     * @Assert\NotBlank
     * @Assert\Email()
     */
    public $organizerEmail;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $organizerPhone;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $pricePresentationForm;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $procedureName;

/*    /**
     * @var string
     * @Assert\NotBlank

    public $infoApplicationProcedure;*/

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $infoPointEntry;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $infoTradingVenue;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $infoBiddingProcess;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $userId;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $clientIp;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    public $newProcedureId;

    /**
     * Command constructor.
     * @param string $userId
     * @param string $clientIp
     * @param string $newProcedureId
     */
    public function __construct(string $userId, string $clientIp, string $newProcedureId){
        $this->userId = $userId;
        $this->clientIp = $clientIp;
        $this->newProcedureId = $newProcedureId;
    }


    /**
     * @param string $infoPointEntry
     * @param string $infoTradingVenue
     * @param string $infoBiddingProcess
     */
    public function setDefaultParams(string $infoPointEntry, string $infoTradingVenue, string $infoBiddingProcess){
        $this->infoPointEntry = $infoPointEntry;
        $this->infoTradingVenue = $infoTradingVenue;
        $this->infoBiddingProcess = $infoBiddingProcess;
    }

}