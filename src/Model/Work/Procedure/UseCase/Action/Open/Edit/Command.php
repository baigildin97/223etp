<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\UseCase\Action\Open\Edit;


use App\ReadModel\Procedure\DetailView;
use Symfony\Component\Validator\Constraints as Assert;

class Command
{
    /**
     * @var string
     */
    public $procedure_id;

    public $procedureType;

    public $pricePresentationForm;

    /**
     * @var string
     * @Assert\NotBlank
     */
    public $procedureName;

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
     * Command constructor.
     * @param string $procedure_id
     * @param string $userId
     */
    public function __construct(string $procedure_id, string $userId)
    {
        $this->procedure_id = $procedure_id;
        $this->userId = $userId;
    }

    public static function edit(DetailView $detailView, string $userId): self{
        $me = new self($detailView->id, $userId);
        $me->procedureType = $detailView->type;
        $me->pricePresentationForm = $detailView->price_presentation_form;
        $me->procedureName = $detailView->title;
        $me->infoPointEntry = $detailView->info_point_entry;
        $me->infoTradingVenue = $detailView->info_trading_venue;
        $me->infoBiddingProcess = $detailView->info_bidding_process;
        return $me;
    }
}