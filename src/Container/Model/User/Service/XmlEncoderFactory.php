<?php
declare(strict_types=1);
namespace App\Container\Model\User\Service;

use Symfony\Component\Serializer\Encoder\XmlEncoder;


/**
 * Class XmlEncoderFactory
 * @package App\Container\Model\User\Service
 */
class XmlEncoderFactory
{
    /**
     * @param string $rootNodeName
     * @param string $encoding
     * @param bool $removeEmptyTags
     * @return XmlEncoder
     */
    public function create(string $rootNodeName = 'document', string $encoding = 'utf-8', bool $removeEmptyTags = false): XmlEncoder {
        return new XmlEncoder([
            'as_collection' => true,
            'xml_encoding' =>$encoding,
            'xml_root_node_name' => $rootNodeName,
            'remove_empty_tags' => $removeEmptyTags
        ]);
    }
}