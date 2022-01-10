<?php
declare(strict_types=1);
namespace App\Model\Work\Procedure\Services\Procedure\File;


use App\Model\Work\Procedure\Entity\Document\FileType;
use App\Model\Work\Procedure\Entity\Document\Status;
use App\ReadModel\Procedure\DetailView;

class FileHelper
{
    public static $typesFiles = [
        FileType::CONTRACT_OF_SALE,
        FileType::DOCUMENT_COMPOSITION,
        FileType::OTHER

    ];

    /**
     * @param array $files
     * @param \App\ReadModel\Procedure\Lot\DetailView $detailView
     * @return array
     */
    public static function rearrangeByType(array $files, \App\ReadModel\Procedure\Lot\DetailView $detailView): array
    {

        $filesSorted = array_fill_keys(
            self::$typesFiles,
            []
        );

        foreach ($files as $file) {
            if (array_key_exists($file->file_type, $filesSorted)) {
                $filesSorted[$file->file_type][] = $file;
            }
        }

        return $filesSorted;
    }

    /**
     * @param array $files
     * @param \App\ReadModel\Procedure\Lot\DetailView $detailView
     * @return array
     */
    public static function getFilesCount(array $files, \App\ReadModel\Procedure\Lot\DetailView $detailView): array
    {
            $filesCount = array_fill_keys(self::$typesFiles, [0, 0]);


        foreach ($files as $type => $filesInType) {
            foreach ($filesInType as $fileInType)
                if (array_key_exists($type, $filesCount)) {
                    $filesCount[$type][1] += 1;

                    if ($fileInType->status == Status::signed()->getName()) {
                        $filesCount[$type][0] += 1;
                    }
                }
        }

        return $filesCount;
    }
}