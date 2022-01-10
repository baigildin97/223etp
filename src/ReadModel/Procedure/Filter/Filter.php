<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\Filter;



class Filter
{
    public $title;
    public $profile_id;
    public $id_number;
    public $status;
    public $statusFilter;
    public $nameOrgInn;
    public $reloadType;

    /**
     * Filter constructor.
     * @param string|null $profile_id
     * @param array|null $status
     */
    public function __construct(? string $profile_id, ? array $status) {
        $this->profile_id = $profile_id;
        $this->status = $status;
    }

    public static function forUserProfile(string $profile_id): self{
        return new self($profile_id, null);
    }

    public static function forStatus(array $status): self {
        return new self(null, $status);
    }
}
