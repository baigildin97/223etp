<?php


namespace App\Model\User\Service\Profile\File;


use App\Model\User\Entity\Profile\Document\FileType;
use App\Model\User\Entity\Profile\Document\Status;
use App\Model\User\Entity\Profile\Organization\IncorporationForm;
use App\Model\User\Entity\Profile\Profile;
use App\ReadModel\Profile\DetailView;

/**
 * Class FileHelper
 * @package App\Model\User\Service\Profile\File
 */
class FileHelper
{
    /**
     * Категория файлов для юридических лиц
     * @var array
     */
    public static $typesLegalEntity = [
        FileType::EXTRACT_EGRUL,
        FileType::COPIES_CONSTITUENT_DOCUMENTS,
        FileType::COPY_TIN_CERTIFICATE,
        FileType::IDENTITY_DOCUMENTS_HEAD,
        FileType::IDENTITY_DOCUMENTS_MANAGER,
        FileType::COPY_THE_HEAD_TIN_CERTIFICATE,
        FileType::COPY_SNILS_HEAD,
        FileType::QUESTIONNAIRE,
        FileType::OTHER
    ];

    /**
     * Категория файлов для Индивидуальных предпринимателей
     * @var array
     */
    public static $typesIndividualEntrepreneur = [
        FileType::EXTRACT_EGRIP,
        FileType::IDENTITY_DOCUMENTS,
        FileType::COPY_TIN_CERTIFICATE,
        FileType::COPY_SNILS,
        FileType::QUESTIONNAIRE,
        FileType::OTHER
    ];

    /**
     * Категории для физических лиц
     * @var array
     */
    public static $typesIndividual = [
        FileType::IDENTITY_DOCUMENTS,
        FileType::COPY_TIN_CERTIFICATE,
        FileType::COPY_SNILS,
        FileType::QUESTIONNAIRE,
        FileType::OTHER
    ];

    /**
     * @var string[]
     */
    public static $popoverList = [
        'IDENTITY_DOCUMENTS_HEAD' => 'Если электронная подпись выдана не на руководителя, наряду с копиями документов, подтверждающих полномочия руководителя, представляются копии документов, подтверждающих полномочия представителя (доверенность на осуществление действий, заверенная печатью и подписанная руководителем).',
        'IDENTITY_DOCUMENTS_MANAGER' => 'Если электронная подпись выдана не на руководителя, наряду с копией документа, удостоверяющего личность руководителя, представляется копия документа, удостоверяющего личность представителя юридического лица.',
        'COPY_THE_HEAD_TIN_CERTIFICATE' => 'Если электронная подпись выдана не на руководителя, наряду с копией свидетельства ИНН руководителя, представляется копия свидетельства ИНН  представителя юридического лица.',
        'COPY_SNILS_HEAD' => 'Если электронная подпись выдана не на руководителя, наряду с копией СНИЛС  руководителя, представляется копия СНИЛС представителя юридического лица.'
    ];


    /**
     * @param array $files
     * @param DetailView $profile
     * @return array
     */
    public static function rearrangeByType(array $files, DetailView $profile): array
    {
        if ($profile->isLegalEntity()) {
            $typeSelf = self::$typesLegalEntity;
        } elseif ($profile->isIndividualEntrepreneur()) {
            $typeSelf = self::$typesIndividualEntrepreneur;
        } elseif ($profile->isIndividual()) {
            $typeSelf = self::$typesIndividual;
        }

        $filesSorted = array_fill_keys(
            $typeSelf,
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
     * @param string $incorporatedForm
     * @return array
     */
    public static function rearrangeByTypeArray(array $files, string $incorporatedForm): array
    {
        if ($incorporatedForm === 'LEGAL_ENTITY') {
            $typeSelf = self::$typesLegalEntity;
        } elseif ($incorporatedForm === 'INDIVIDUAL_ENTREPRENEUR') {
            $typeSelf = self::$typesIndividualEntrepreneur;
        } elseif ($incorporatedForm === 'INDIVIDUAL') {
            $typeSelf = self::$typesIndividual;
        }

        $filesSorted = array_fill_keys(
            $typeSelf,
            []
        );
        foreach ($files as $file) {

            if (array_key_exists($file['fileTypeOrigin'], $filesSorted)) {
                $filesSorted[$file['fileTypeOrigin']][] = $file;
            }
        }

        return $filesSorted;
    }

    /**
     * @param array $files
     * @param DetailView $detailView
     * @param false $showHistory
     * @return array
     */
    public static function getFilesCount(array $files, DetailView $detailView, $showHistory = false): array
    {

        if ($detailView->isLegalEntity()) {
            $filesCount = array_fill_keys(self::$typesLegalEntity, [0, 0]);
        } elseif ($detailView->isIndividualEntrepreneur()) {
            $filesCount = array_fill_keys(self::$typesIndividualEntrepreneur, [0, 0]);
        } elseif ($detailView->isIndividual()) {
            $filesCount = array_fill_keys(self::$typesIndividual, [0, 0]);
        }

        foreach ($files as $type => $filesInType) {
            foreach ($filesInType as $fileInType) {
                if (array_key_exists($type, $filesCount)) {
                    $filesCount[$type][1] += 1;

                    if ($fileInType->status == Status::signed()->getName()) {
                        $filesCount[$type][0] += 1;
                    }
                }

            }
            if (!$showHistory){
                $filesCount[$type][2] = self::$popoverList[$type] ?? null;
            }
        }

        return $filesCount;
    }
}