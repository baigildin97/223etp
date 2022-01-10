<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Procedure\Protocols;


use App\Model\Work\Procedure\Entity\Id;

interface ProtocolGeneratorInterface
{
    public function generate(Id $procedureId, ?string $organizerComment, ?string $requisiteId): XmlDocument;
}