<?php
declare(strict_types=1);
namespace App\ReadModel\Profile\XmlDocument\History\Filter;

use App\Model\User\Entity\Profile\XmlDocument\History\Action;

/**
 * Class Filter
 * @package App\ReadModel\Profile\XmlDocument\History\Filter
 */
class Filter
{
    /**
     * @var string|null
     */
    public $xmlDocument;

    /**
     * @var string|null
     */
    public $email;

    /**
     * @var array|null
     */
    public $actions;

    /**
     * Filter constructor.
     * @param string|null $xmlDocument
     * @param string|null $email
     * @param array|null $actions
     */
    private function __construct(? string $xmlDocument, ? string $email, ? array $actions)
    {
        $this->xmlDocument = $xmlDocument;
        $this->email = $email;
        $this->actions = $actions;
    }

    /**
     * @param string $xmlDocument
     * @return static
     */
    public static function fromXmlDocument(string $xmlDocument): self {
        return new self($xmlDocument, null, null);
    }

    /**
     * @param string $xmlDocument
     * @param string $email
     * @return static
     */
    public static function fromXmlDocumentParticipant(string $xmlDocument, string $email): self {
        return new self($xmlDocument, $email, [
            Action::send()->getName(),
            Action::approved()->getName(),
            Action::rejected()->getName(),
            Action::recalled()->getName()
        ]);
    }
}