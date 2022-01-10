<?php
declare(strict_types=1);


namespace App\ReadModel\Profile;


use App\Model\User\Entity\Profile\Document\FileType;
use App\Model\User\Entity\Profile\Organization\IncorporationForm;
use App\Model\User\Service\Profile\File\FileHelper;
use Doctrine\Common\Collections\ArrayCollection;

class FileView
{
    /**
     * @var ArrayCollection|array
     */
    public $files_;

    public static function fetchInArray(array $files)
    {
        $me = new self();
        $me->files_ = $files;

        return $me;
    }

    public function isAccreditationValid(IncorporationForm $incorporationForm): bool
    {
        if (!is_null($this->files_)) {
            $fileTypes = $incorporationForm->isIndividualEntrepreneur()
                ? FileHelper::$typesIndividualEntrepreneur
                : FileHelper::$typesLegalEntity;

            $fileTypesCount = array_fill_keys($fileTypes, 0);

            foreach ($this->files_ as $file) {
                if ($file['status'] === 'STATUS_NEW')
                    return false;
                else
                    $fileTypesCount[$file['file_type']] += 1;
            }

            unset($fileTypesCount[FileType::OTHER]);

            if (in_array(0, $fileTypesCount))
                return false;
            else
                return true;
        }
        else
            throw new \Exception('Files where fetched with wrong method');
    }
}