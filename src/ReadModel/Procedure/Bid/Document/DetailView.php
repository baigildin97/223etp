<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Bid\Document;


class DetailView
{
    public $id;
    public $bid_id;
    public $status;
    public $created_at;
    public $participant_ip;
    public $file_path;
    public $file_name;
    public $file_size;
    public $file_real_name;
    public $file_sign;
    public $file_hash;
    public $document_name;
    public $signed_at;

}