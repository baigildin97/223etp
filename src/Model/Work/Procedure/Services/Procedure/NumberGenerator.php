<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Procedure;


use App\ReadModel\Procedure\ProcedureFetcher;

class NumberGenerator
{
    private $procedureFetcher;

    public function __construct(ProcedureFetcher $procedureFetcher){
        $this->procedureFetcher = $procedureFetcher;
    }


    public function next(): int{
     return $this->procedureFetcher->findLastIdNumber() + 1;
    }

}