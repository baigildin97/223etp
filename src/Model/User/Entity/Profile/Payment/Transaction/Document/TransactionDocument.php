<?php
declare(strict_types=1);
namespace App\Model\User\Entity\Profile\Payment\Transaction\Document;

use App\Model\User\Entity\Profile\Payment\Transaction\Transaction;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class TransactionDocument
 * @package App\Model\User\Entity\Profile\Payment\Transaction\Document
 * @ORM\Entity()
 * @ORM\Table(name="transaction_documents")
 */
class TransactionDocument
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="transaction_documents_id")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $hash;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $sign;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $xml;

    private $createdAt;

    /**
     * @var Transaction
     * @ORM\OneToOne(targetEntity="App\Model\User\Entity\Profile\Payment\Transaction\Transaction")
     * @ORM\JoinColumn(referencedColumnName="id", name="transaction_id")
     */
    private $transaction;

    public function __construct(Id $id, string $hash, string $sign, string $xml, \DateTimeImmutable $createdAt) {
        $this->id = $id;
        $this->hash = $hash;
        $this->sign = $sign;
        $this->xml = $xml;
        $this->createdAt = $createdAt;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return string
     */
    public function getSign(): string
    {
        return $this->sign;
    }

    /**
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * @return string
     */
    public function getXml(): string
    {
        return $this->xml;
    }

    /**
     * @return Transaction
     */
    public function getTransaction(): Transaction
    {
        return $this->transaction;
    }

}