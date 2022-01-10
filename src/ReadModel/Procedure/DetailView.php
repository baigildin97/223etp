<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure;

use App\Helpers\Filter;
use App\Model\Work\Procedure\Entity\Status;

class DetailView{

    public $id;
    public $id_number;
    public $title;
    public $type;
    public $profile_id;
    public $price_presentation_form;
    public $tender_basic;
    public $bailiffs_name;
    public $executive_production_number;
    public $trade_procedure_rerun;
    public $auction_step;
    public $status;
    public $debtor_full_name;
    public $start_trading_time;
    public $info_point_entry;
    public $info_trading_venue;
    public $info_bidding_process;
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
    public $web_site;
    public $requisite;
    public $organizer_full_name;
    public $organizer_email;
    public $organizer_phone;

    public $representative_first_name;
    public $representative_middle_name;
    public $representative_last_name;
    public $representative_phone;
    public $certificate_thumbprint;
    public $incorporated_form;
    public $owner;
    public $created_at;


    public function organizerFullName(): string{
        return $this->representative_last_name.' '.$this->representative_first_name.' '.$this->representative_middle_name;
    }

    public function statusIsNew(): bool{
        return (new Status($this->status))->isNew();
    }

    public function statusIsRejected(): bool{
        return (new Status($this->status))->isRejected();
    }

    public function getStatus(): Status {
        return new Status($this->status);
    }

    public function getFactAddress(): string{
        return $this->organization_fact_index . ', ' . $this->getCountry($this->organization_fact_country). ', ' . $this->organization_fact_region . ', '. $this->organization_fact_city. ', '. $this->organization_fact_street. ', '. $this->organization_fact_house;
    }

    private function getCountry(string $country){
        return Filter::country($country);
    }





}