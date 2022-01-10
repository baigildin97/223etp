<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Entity\Lot\Bid\XmlDocument;

use App\Model\Work\Procedure\Entity\Lot\Bid\Bid;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Mixed_;

/**
 * Class XmlDocument
 * @package App\Model\Work\Procedure\Entity\Lot\Bid\XmlDocument
 * @ORM\Entity()
 * @ORM\Table(name="bid_xml_documents")
 */
class XmlDocument
{
    /**
     * @var Id
     * @ORM\Id()
     * @ORM\Column(type="bid_xml_document_id")
     */
    private $id;

    /**
     * @var Status
     * @ORM\Column(type="bid_xml_document_status")
     */
    private $status;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $file;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $hash;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $sign;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private $signedAt;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var Category
     * @ORM\Column(type="bid_xml_document_category")
     */
    private $category;

    /**
     * @var Bid
     * @ORM\ManyToOne(targetEntity="App\Model\Work\Procedure\Entity\Lot\Bid\Bid", inversedBy="xmlDocuments", cascade={"all"})
     * @ORM\JoinColumn(name="bid_id", referencedColumnName="id")
     */
    private $bid;

    /**
     * Xml constructor.
     * @param Id $id
     * @param $status
     * @param string $file
     * @param string $hash
     * @param string $sign
     * @param Bid $bid
     * @param Category $category
     * @param \DateTimeImmutable $createdAt
     */
    public function __construct(
        Id $id,
        $status,
        string $file,
        string $hash,
        string $sign,
        Bid $bid,
        Category $category,
        \DateTimeImmutable $createdAt
    ) {
        $this->id = $id;
        $this->status = $status;
        $this->file = $file;
        $this->hash = $hash;
        $this->sign = $sign;
        $this->bid = $bid;
        $this->category = $category;
        $this->createdAt = $createdAt;
    }
}