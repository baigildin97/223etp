<?php
declare(strict_types=1);
namespace App\ReadModel\Procedure\XmlDocument\History;



use App\Model\Work\Procedure\Entity\XmlDocument\History\Action;

class Filter
{
    public $email;
    public $xml_document_id;
    public $actions;

    /**
     * Filter constructor.
     * @param string|null $xml_document_id
     * @param string|null $email
     * @param string|null $actions
     */
    public function __construct(? string $xml_document_id, ? string $email, ? array $actions)
    {
        $this->xml_document_id = $xml_document_id;
        $this->email = $email;
        $this->actions = $actions;
    }

    /**
     * @param $xml_document_id
     * @return static
     */
    public static function fromXmlDocument($xml_document_id): self {
        return new self($xml_document_id, null, null);
    }

    /**
     * @param string $xml_document_id
     * @param string $email
     * @return static
     */
    public static function fromXmlDocumentParticipant(string $xml_document_id, string $email): self {
        return new self($xml_document_id, $email, [
            Action::send()->getName(),
            Action::approved()->getName(),
            Action::rejected()->getName(),
            Action::recalled()->getName(),
            Action::cancelPublished()->getName()
        ]);
    }
}