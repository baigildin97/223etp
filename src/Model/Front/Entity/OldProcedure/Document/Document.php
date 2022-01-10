<?php
declare(strict_types=1);

namespace App\Model\Front\Entity\OldProcedure\Document;

use App\Model\Front\Entity\OldProcedure\OldProcedure;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Document
 * @package App\Model\Front\Entity\OldProcedure\Document
 * @ORM\Entity()
 * @ORM\Table(schema="old_records", name="documents")
 */
class Document
{
    /**
     * @var Id
     * @ORM\Column(type="old_document_id")
     * @ORM\Id
     */
    private $id;

    /**
     * @var OldProcedure
     * @ORM\ManyToOne(targetEntity="App\Model\Front\Entity\OldProcedure\OldProcedure", inversedBy="documents")
     * @ORM\JoinColumn(name="procedure_id", referencedColumnName="id", nullable=true)
     */
    private $procedure;

    /**
     * @var string
     * @ORM\Column(type="string", name="name", nullable=true)
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="string", name="type", nullable=true)
     */
    private $type;

    /**
     * @var string
     * @ORM\Column(type="text", name="path", nullable=true)
     */
    private $fullPath;


    public function __construct(
        Id $id,
        string $type,
        OldProcedure $oldProcedure,
        string $name,
        string $fullPath
    )
    {
        $this->id = $id;
        $this->type = $type;
        $this->procedure = $oldProcedure;
        $this->name = $name;
        $this->fullPath = $fullPath;
    }

    /**
     * @return Id
     */
    public function getId(): Id
    {
        return $this->id;
    }
}