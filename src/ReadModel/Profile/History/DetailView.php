<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\History;

use App\Model\User\Entity\Profile\Organization\IncorporationForm;

class DetailView{


    public $generalInformation;

    public $organizationInfo;

    public $representativeInfo;

    public $documents;

    public $certificateInfo;

    public $createdAt;

    public $email;






    public function isNotIndividual(): bool {
        return $this->generalInformation['incorporatedFormOrigin'] !== IncorporationForm::individual()->getName();
    }

    public function isIndividualEntrepreneur(): bool {
        return $this->generalInformation['incorporatedFormOrigin'] === IncorporationForm::individualEntrepreneur()->getName();
    }

    public function isLegalEntity(): bool{
        return $this->generalInformation['incorporatedFormOrigin'] === IncorporationForm::legalEntity()->getName();
    }

    public function isIndividual(): bool {
        return $this->generalInformation['incorporatedFormOrigin'] === IncorporationForm::individual()->getName();
    }

    public function isIndividualOrIndividualEntrepreneur(): bool {
        if ($this->isIndividual() || $this->isIndividualEntrepreneur()){
            return true;
        }
        return false;
    }

}
