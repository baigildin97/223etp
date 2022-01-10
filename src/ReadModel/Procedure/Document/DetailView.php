<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Document;

use App\Model\Work\Procedure\Entity\Document\FileType;

class DetailView
{
    public $id;
    public $procedure_id;
    public $file_type;
    public $status;
    public $created_at;
    public $client_ip;
    public $file_path;
    public $file_name;
    public $file_size;
    public $file_real_name;
    public $file_hash;
    public $file_sig;
    public $file_signed_at;

    public function isDRAFT_DEPOSIT_AGREEMENT(): bool{
        return $this->file_type !== FileType::DEPOSIT_AGREEMENT;
    }
}


