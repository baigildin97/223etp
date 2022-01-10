<?php
declare(strict_types=1);

namespace App\ReadModel\Profile;


use App\Helpers\Filter;
use App\Model\User\Entity\Certificate\CertificateRepository;
use App\Model\User\Entity\Certificate\Id;
use App\Model\User\Entity\Profile\Organization\IncorporationForm;
use App\Model\User\Entity\Profile\Role\Permission;
use App\Model\User\Entity\Profile\Role\Role;
use App\Model\User\Entity\Profile\Status;

class DetailView
{
    // Пользовательские данные
    public $user_email;
    public $user_name;

    // Информация о профиле
    public $id;
    public $user_id;
    public $role_constant;
    public $role_permissions;
    public $role_name;
    public $status;
    public $created_at;
    public $payment_id;
    public $subscribe_tariff_id;
    public $contract_period;

    // Информация об организации
    public $incorporated_form;
    public $full_title_organization;
    public $short_title_organization;
    public $kpp;
    public $ogrn;
    public $inn;
    public $snils;

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

    public $org_email;

    // Информация о представителе
    public $repr_passport_first_name;
    public $repr_passport_middle_name;
    public $repr_passport_last_name;
    public $repr_passport_inn;

    public $position;
    public $confirming_document;
    public $phone;
    public $web_site;
    public $passport_series;
    public $passport_number;
    public $passport_issuer;
    public $passport_issuance_date;
    public $passport_citizenship;
    public $passport_unit_code;
    public $passport_birth_day;

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

    public $passport_inn;
    public $passport_snils;


    // Информация о сертификате
    public $certificate_id;
    public $certificate_thumbprint;
    public $certificate_owner;
    public $certificate_valid_from;
    public $certificate_valid_to;
    public $certificate_subject_name_inn;
    public $certificate_subject_name_snils;

    // Реквизиты
    public $requisites;


    public function __construct()
    {
        $this->role_permissions = json_decode($this->role_permissions, true);
    }

    public function isLegalEntity(): bool {
        return $this->incorporated_form === IncorporationForm::legalEntity()->getName();
    }

    public function isActive(): bool{
        return $this->status === Status::active()->getName();
    }

    public function isReplacingEp(): bool{
        return $this->status === Status::replacingEp()->getName();
    }

    public function isIndividualEntrepreneur(): bool
    {
        return $this->incorporated_form === IncorporationForm::individualEntrepreneur()->getName();
    }

    public function isIndividual(): bool
    {
        return $this->incorporated_form === IncorporationForm::individual()->getName();
    }

    public function isNotIndividual(): bool
    {
        return $this->incorporated_form !== IncorporationForm::individual()->getName();
    }

    public function isIndividualOrIndividualEntrepreneur(): bool
    {
        if ($this->isIndividual() || $this->isIndividualEntrepreneur()) {
            return true;
        }
        return false;
    }

    public function isLegalEntityOrIndividualEntrepreneur(): bool
    {
        if ($this->isLegalEntity() || $this->isIndividualEntrepreneur()) {
            return true;
        }
        return false;
    }

    // TODO - не понятно зачем 2 метода с разными названиями
    public function getOwnerFullName(): string
    {
        return $this->repr_passport_first_name . ' ' . $this->repr_passport_last_name . ' ' . $this->repr_passport_middle_name;
    }


    public function getOwnerFullNameBid(): string
    {
        return $this->repr_passport_last_name . ' ' . $this->repr_passport_first_name . ' ' . $this->repr_passport_middle_name;
    }


    public function getDataPassword(): string
    {
        return 'дата рождения ' . $this->filterDate($this->passport_birth_day, true) . ', паспорт №' . $this->passport_series . ' ' . $this->passport_number . ', выдан ' . $this->passport_issuer . ', дата выдачи ' . $this->filterDate($this->passport_issuance_date, true) . ', код подразделения ' . $this->passport_unit_code . ', адрес регистрации ' . $this->passport_legal_index . ',' . $this->getLegalCountry(). ',' . $this->passport_legal_region . ','. $this->passport_legal_city. ','. $this->passport_legal_street. '.'. $this->passport_legal_house;
    }

    public function getDataOrganizer(): string
    {
        return $this->full_title_organization . ', ИНН ' . $this->inn . ', ОГРН ' . $this->ogrn . ', КПП ' . $this->kpp . ', адрес регистрации ' . $this->legal_index . ',' . $this->getLegalCountry(). ',' . $this->legal_region . ','. $this->legal_city. ','. $this->legal_street. '.'. $this->legal_house;
    }

    public function getFactAddress(): string{
        return $this->fact_index . ', ' . $this->getFactCountry(). ', ' . $this->fact_region . ', '. $this->fact_city. ', '. $this->fact_street. ', '. $this->fact_house;
    }

    public function getLegalAddress(): string{
        if ($this->isLegalEntity()) {
            return $this->legal_index . ', ' . $this->getLegalCountry(). ', ' . $this->legal_region . ', ' . $this->legal_city . ', ' . $this->legal_street . ', ' . $this->legal_house;
        }

        return $this->passport_legal_index . ', ' . $this->getLegalCountry() . ', ' . $this->passport_legal_region . ', ' . $this->passport_legal_city . ', ' . $this->passport_legal_street . ', ' . $this->passport_legal_house;

    }



    public function getLegalAddressIndividual(): string{
        return $this->legal_index . ', ' . $this->getLegalCountry(). ', ' . $this->legal_region . ', '. $this->legal_city. ', '. $this->legal_street. ', '. $this->legal_house;
    }

    public function getStatus(): Status
    {
        return new Status($this->status);
    }

    public function isGranted(string $permission): bool
    {
        return in_array($permission, $this->role_permissions);
    }

    public function isOrganizer(): bool
    {
        return $this->role_constant === Role::ROLE_ORGANIZER;
    }


    public function isParticipant(): bool
    {
        return $this->role_constant === Role::ROLE_PARTICIPANT;
    }


    public function isOrganizerOrIsParticipant(): bool
    {
        if ($this->isOrganizer() || $this->isParticipant()) {
            return true;
        }
        return false;
    }

    public function getInn(): string {
        $incorporatedForm = new IncorporationForm($this->incorporated_form);
        if ($incorporatedForm->isLegalEntity()){
            return Filter::filterInnLegalEntity($this->inn);
        }

        return $this->certificate_subject_name_inn;
    }

    public function isSignContract(): bool{
        if ($this->contract_period !== null){
            return true;
        }

        if ($this->contract_period === null){
            return false;
        }

        if (new \DateTimeImmutable($this->contract_period) > new \DateTimeImmutable()){
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    private function getLegalCountry(){
        if ($this->legal_country){
            return Filter::country($this->legal_country);
        }
        if ($this->passport_legal_country){
            return Filter::country($this->passport_legal_country);
        }
    }

    /**
     * @return string
     */
    private function getFactCountry(){
        if ($this->fact_country){
            return Filter::country($this->fact_country);
        }
        if ($this->passport_fact_country){
            return Filter::country($this->passport_fact_country);
        }
    }

    private function filterDate(string $date, ?bool $only_date = false){
        return Filter::date($date, $only_date);
    }

    public function getFullNameAccount(): string {
        if ($this->isLegalEntity()){
            return $this->full_title_organization;
        }
        if ($this->isIndividualEntrepreneur()){
            return 'ИП '.$this->getOwnerFullName();
        }
        return $this->getOwnerFullName();
    }

}
