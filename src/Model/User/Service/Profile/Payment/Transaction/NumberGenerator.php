<?php
declare(strict_types=1);
namespace App\Model\User\Service\Profile\Payment\Transaction;

use App\ReadModel\Profile\Payment\Transaction\TransactionFetcher;
use Doctrine\DBAL\Exception;

/**
 * Class NumberGenerator
 * @package App\Model\User\Service\Profile\Payment\Transaction
 */
class NumberGenerator
{
    /**
     * @var TransactionFetcher
     */
    private $transactionFetcher;

    /**
     * NumberGenerator constructor.
     * @param TransactionFetcher $transactionFetcher
     */
    public function __construct(TransactionFetcher $transactionFetcher){
        $this->transactionFetcher = $transactionFetcher;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function next(): string{
     return $this->transactionFetcher->findLastIdNumber() + 1;
    }

}