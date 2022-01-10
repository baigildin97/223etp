<?php
declare(strict_types=1);
namespace App\Services\Document\Xml;


use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class XmlGenerator
{

    public function __construct() {
    }

    public static function serialize($object, bool $deleteEmpty=true): string
    {
        $serializer = new Serializer([new ObjectNormalizer()], [
            new XmlEncoder([
                'xml_encoding' => 'utf-8',
                'xml_root_node_name' => 'profile',
                'remove_empty_tags' => $deleteEmpty
            ])
        ]);

        return $serializer->serialize(self::prepareToSerialize($object), 'xml');
    }

    public static function prepareToSerialize($object): array
    {
        $result = [];

        foreach ($object as $key => $value) {
            if (is_object($value))
                $result[$key] = self::prepareToSerialize($value);
            else
                $result[$key] = $value;
        }

        return $result;
    }

    public static function formatXml(string $xml) {
        $document = new \DOMDocument('1.0');
        $document->preserveWhiteSpace = false;
        $document->formatOutput = true;
        $document->loadXML($xml);

        return $document->saveXML();
    }
}