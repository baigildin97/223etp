<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Bid;


use App\Helpers\Filter;
use App\Model\User\Entity\Profile\Organization\IncorporationForm;
use App\Model\Work\Procedure\Entity\Lot\Bid\Status;

class DetailView
{
    public $id;
    public $number;
    public $participant_id;
    public $lot_id;
    public $organizer_profile_id;
    public $status;
    public $place;
    public $lot_status;
    public $summing_up_applications;
    public $closing_date_of_applications;
    public $participant_ip;
    public $organizer_ip;
    public $organizer_ip_created_at;
    public $created_at;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $position;
    public $full_title_organization;
    public $inn_organization;
    public $lot_number;
    public $procedure_number;
    public $procedure_id;
    public $signed_at;
    public $certificate_thumbprint;
    public $subject_name_inn;
    public $cause_reject;
    public $temp_status;
    public $confirm_xml;
    public $confirm_hash;
    public $confirm_sign;
    public $requisite_id;
    public $organizer_comment;
    public $incorporated_form;


    //lot
    public $bailiffs_name_dative_case;
    public $executive_production_number;
    public $debtor_full_name;
    public $procedure_title;
    public $lot_created_at;
    public $tender_basic;
    public $organizer_full_title_organization;
    public $debtor_full_name_date_case;

    //auction
    public $start_trading_date;
    public $auction_id;


    public $fact_country;
    public $fact_region;
    public $fact_city;
    public $fact_index;
    public $fact_street;
    public $fact_house;

    public $legal_country;
    public $legal_region;
    public $legal_city;
    public $legal_index;
    public $legal_street;
    public $legal_house;

    public $passport_fact_country;
    public $passport_fact_region;
    public $passport_fact_city;
    public $passport_fact_index;
    public $passport_fact_street;
    public $passport_fact_house;

    public $passport_legal_country;
    public $passport_legal_region;
    public $passport_legal_city;
    public $passport_legal_index;
    public $passport_legal_street;
    public $passport_legal_house;

    public function getOwnerFullName(): string {
        return $this->first_name.' '.$this->middle_name.' '.$this->last_name;
    }

    public function getFullNameOrganizer(): string{
        return $this->full_title_organization;
    }

    public function getFullNumberLot(): string{
        return $this->procedure_number.'-'.$this->lot_number;
    }

    public function isNotSent(): bool{
        return $this->status !== Status::sent()->getName();
    }

    public function isNotNew(): bool{
        return $this->status !== Status::new()->getName();
    }

    public function getStatus(): Status {
        return new Status($this->status);
    }


    public function isConfirmedXmlParticipant(): bool{
        return $this->confirm_xml !== null;
    }

    public function isLegalEntity(): bool{
        return $this->incorporated_form === IncorporationForm::legalEntity()->getName();
    }

    public function isIndividualEntrepreneur(): bool {
        return $this->incorporated_form === IncorporationForm::individualEntrepreneur()->getName();
    }

    public function isIndividual(): bool {
        return $this->incorporated_form === IncorporationForm::individual()->getName();
    }


    public function isStatusAcceptingApplications(): bool {
        return $this->lot_status === \App\Model\Work\Procedure\Entity\Lot\Status::acceptingApplications()->getName();
    }


    public function getFullLegalAddressOrganization(): string {
        return $this->legal_index.', '.Filter::country($this->legal_country).', '.$this->legal_region.', '.$this->legal_city.', '.$this->legal_street.', '.$this->legal_house;
    }

    public function getFullFactAddressOrganization(): string {
        return $this->fact_index.', '.Filter::country($this->fact_country).', '.$this->fact_region.', '.$this->fact_city.', '.$this->fact_street.', '.$this->fact_house;
    }

    public function getFullPassportLegalAddress(): string {
        return $this->passport_legal_index.', '.Filter::country($this->passport_legal_country).', '.$this->passport_legal_region.', '.$this->passport_legal_city.', '.$this->passport_legal_street.', '.$this->passport_legal_house;
    }

    public function getFullPassportFactAddress(): string {
        return $this->passport_fact_index.', '.Filter::country($this->passport_fact_country).', '.$this->passport_fact_region.', '.$this->passport_fact_city.', '.$this->passport_fact_street.', '.$this->passport_fact_house;
    }


}
