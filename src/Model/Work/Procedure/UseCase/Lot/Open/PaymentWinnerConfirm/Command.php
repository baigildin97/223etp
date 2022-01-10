<?php
declare(strict_types=1);

namespace App\Model\Work\Procedure\UseCase\Lot\Open\PaymentWinnerConfirm;


class Command{

    /**
     * @var string
     */
    public $lot_id;

    /**
     * @var string
     */
    public $userId;

    /**
     * @var string
     */
    public $procedure_id;

    public function __construct(string $lot_id, string $procedure_id, string $userId)
    {
        $this->lot_id = $lot_id;
        $this->procedure_id = $procedure_id;
        $this->userId = $userId;
    }
}