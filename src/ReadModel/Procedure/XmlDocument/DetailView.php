<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\XmlDocument;


use App\Model\Work\Procedure\Entity\XmlDocument\Status;
use App\Model\Work\Procedure\Entity\XmlDocument\Type;

class DetailView
{
    public $id;
    public $status;
    public $number;
    public $file;
    public $type;
    public $status_tactic;
    public $hash;
    public $sign;
    public $created_at;
    public $signed_at;
    public $moderator_comment;
    public $procedure_id;

    public function isRejected(): bool{
        return (new Status($this->status))->isRejected();
    }

    public function isRecalled(): bool{
        return (new Status($this->status))->isRecalled();
    }

    public function isSigned(): bool{
        return (new Status($this->status))->isSigned();
    }

    public function isModerate(): bool {
        return (new Status($this->status))->isModerate();
    }

    public function isApproved(): bool {
        return (new Status($this->status))->isApproved();
    }

    public function isCancelType(): bool {
        return (new Type($this->type))->isNotifyCancel();
    }
}