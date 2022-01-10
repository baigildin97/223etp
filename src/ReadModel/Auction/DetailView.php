<?php
declare(strict_types=1);
namespace App\ReadModel\Auction;

use App\Helpers\FormatMoney;
use App\Model\Work\Procedure\Entity\Lot\Auction\Status;
use Money\Money;

class DetailView{

    public $id;

    public $lot_id;

    public $lot_id_number;

    public $id_number;

    public $procedure_id;

    public $status;

    public $default_closed_time;

    public $current_cost;

    public $auction_step;

    public $starting_price;
    
    public $start_trading_date;


    public function getStatus(): Status {
        return new Status($this->status);
    }

    public function isActive(): bool{
        return $this->status === Status::active();
    }

    public function isCompleted(): bool{
        return $this->status === Status::completed();
    }
}