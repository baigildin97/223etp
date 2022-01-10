<?php
declare(strict_types=1);

namespace App\ReadModel\Procedure\Lot;


use App\Model\Work\Procedure\Entity\Lot\Status;

class DetailView
{
    public $id;
    public $auction_id;
    public $id_number;
    public $arrested_property_type;
    public $e;
    public $type;
    public $title;
    public $lot_name;
    public $status;
    public $region;
    public $requisite;
    public $location_object;
    public $additional_object_characteristics;
    public $starting_price;
    public $organizer_profile_id;
    public $pledgeer;
    public $bailiffs_name;
    public $bailiffs_name_dative_case;
    public $reload_lot;
    public $tender_basic;
    public $nds;
    public $auction_step;
    public $winner_id;
    public $advance_payment_time;
    public $start_date_of_applications;
    public $closing_date_of_applications;
    public $summing_up_applications;
    public $start_trading_date;
    public $procedure_id;
    public $executive_production_number;
    public $procedure_number;
    public $debtor_full_name;
    public $debtor_full_name_date_case;
    public $info_point_entry;
    public $info_trading_venue;
    public $info_bidding_process;
    public $bid_number;
    public $offer_auction_time;
    public $payment_winner_confirm;
    public $date_enforcement_proceedings;
    public $deposit_amount;
    public $deposit_policy;
    public $tendering_process;

    public $organization_short_title;
    public $organization_full_title;
    public $organization_inn;
    public $organization_snils;
    public $organization_fact_index;
    public $organization_fact_country;
    public $organization_fact_region;
    public $organization_fact_city;
    public $organization_fact_street;
    public $organization_fact_house;
    public $organization_legal_index;
    public $organization_legal_country;
    public $organization_legal_region;
    public $organization_legal_city;
    public $organization_legal_street;
    public $organization_legal_house;
    public $organization_kpp;
    public $organization_ogrn;
    public $organization_email;
    public $organizer_phone;
    public $web_site;
    public $organizer_full_name;
    public $organizer_email;

    public $payment_account;
    public $bank_name;
    public $bank_bik;
    public $correspondent_account;

    public function getStatus(): Status
    {
        return new Status($this->status);
    }

    /**
     * @return bool
     */
    public function isWinner(): bool{
        if ($this->winner_id === null){
            return false;
        }

        return true;
    }


}