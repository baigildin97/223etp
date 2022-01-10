<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Lot\Protocol;


use App\Model\Work\Procedure\Entity\Protocol\Type;

class DetailView
{
    public $id;
    public $procedure_id;
    public $id_number;
    public $type;
    public $status;
    public $created_at;
    public $xml_file;
    public $xml_sign_organizer;
    public $xml_hash_organizer;
    public $xml_signed_at;
    public $reason;
    public $organizer_comment;
    public $xml_certificate_thumbprint_organizer;

    public $xml_sign_participant;
    public $xml_hash_participant;
    public $xml_signed_at_participant;
    public $xml_certificate_thumbprint_participant;

    public function getLocalizedType(): string {
        return Type::$names[$this->type];
    }
}