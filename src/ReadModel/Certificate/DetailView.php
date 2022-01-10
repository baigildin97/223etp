<?php
declare(strict_types=1);
namespace App\ReadModel\Certificate;


use App\Model\User\Entity\Profile\Organization\IncorporationForm;

class DetailView
{
    public $id;
    public $user_id;
    public $thumbprint;
    public $created_at;
    public $archived_date;
    public $valid_from;
    public $valid_to;
    public $status;

    public $subject_name_ogrn;
    public $subject_name_snils;
    public $subject_name_inn;
    public $subject_name_email;
    public $subject_name_owner;
    public $subject_name_position;
    public $subject_name_user_name;
    public $subject_name_region;
    public $subject_name_city;
    public $subject_name_street;

    public $issuer_name_issuer;
    public $issuer_name_unit_organization;
    public $issuer_name_inn;
    public $issuer_name_ogrn;
    public $subject_name_ogrn_ip;
    public $issuer_name_email;
    public $issuer_name_region;
    public $issuer_name_city;
    public $issuer_name_street;

    public function isLegalEntity(): bool {
        //Юридическое лицо
        return !empty($this->subject_name_ogrn);
    }

    public function isIndividualEntrepreneur(): bool {
        //Индивидуаьный предриниматель
        return !empty($this->subject_name_ogrn_ip);
    }

    public function isIndividual(): bool {
        //Физическое лицо
        return empty($this->subject_name_ogrn) && empty($this->subject_name_ogrn_ip);
    }

}