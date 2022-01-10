<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\XmlDocument;


use App\Model\User\Entity\Profile\Organization\IncorporationForm;
use App\Model\User\Entity\Profile\Role\Role;
use App\Model\User\Entity\Profile\XmlDocument\Status;
use App\Model\User\Entity\Profile\XmlDocument\TypeStatement;

class DetailView
{
    public $id;
    public $profile_id;
    public $id_number;
    public $status;
    public $status_tactic;
    public $created_at;
    public $file;
    public $hash;
    public $sign;
    public $moderator_comment;
    public $certificate_owner;
    public $user_name;
    public $type;
    public $user_incorporated_form;
    public $organization_full_title;
    public $user_position;
    public $confirming_document;
    public $user_passport_series;
    public $user_passport_number;
    public $user_passport_issuer;
    public $user_passport_issuance_date;
    public $certificate_thumbprint;

    public function isRecalled(): bool {
        return $this->status === Status::recalled()->getName();
    }

    public function isSigned(): bool {
        return $this->status === Status::signed()->getName();
    }

    public function isTypeStatementRegistration(): bool {
        return (new TypeStatement($this->type))->isRegistration();
    }

    public function isTypeStatementEdit(): bool {
        return (new TypeStatement($this->type))->isEdit();
    }
    public function isTypeReplacingEp(): bool {
        return (new TypeStatement($this->type))->isReplacingEp();
    }


    public function userIsLegalEntity(): bool {
        return (new IncorporationForm($this->user_incorporated_form))->isLegalEntity();
    }
}