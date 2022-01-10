<?php
declare(strict_types=1);
namespace App\ReadModel\User;


use App\Model\User\Entity\Profile\Role\Role;
use App\Model\User\Entity\Profile\Status;

class DetailView
{
    public $id;
    public $email;
    public $role;
    public $status;
    public $created_at;
    public $profile_id;
    public $role_constant;
    public $profile_status;
    public $contract_period;

    public $incorporated_form;

    public function isOrganizer(): bool{
        return $this->role_constant === Role::ROLE_ORGANIZER;
    }

    public function isParticipant(): bool{
        return $this->role_constant === Role::ROLE_PARTICIPANT;
    }

    public function issetActiveProfile(): bool{
        if(isset($this->profile_status)){
            if($this->profile_status === Status::active()){
                return true;
            }
        }

        return false;
    }
}