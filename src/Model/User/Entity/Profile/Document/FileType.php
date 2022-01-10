<?php
declare(strict_types=1);

namespace App\Model\User\Entity\Profile\Document;


use App\ReadModel\Profile\DetailView;
use Webmozart\Assert\Assert;


class FileType
{
    public const EXTRACT_EGRUL = 'EXTRACT_EGRUL';
    public const COPIES_CONSTITUENT_DOCUMENTS = 'COPIES_CONSTITUENT_DOCUMENTS';
    public const COPY_TIN_CERTIFICATE = 'COPY_TIN_CERTIFICATE';
    public const IDENTITY_DOCUMENTS_HEAD = 'IDENTITY_DOCUMENTS_HEAD';
    public const IDENTITY_DOCUMENTS_MANAGER = 'IDENTITY_DOCUMENTS_MANAGER';
    public const COPY_THE_HEAD_TIN_CERTIFICATE = 'COPY_THE_HEAD_TIN_CERTIFICATE';
    public const COPY_SNILS_HEAD = 'COPY_SNILS_HEAD';
    public const QUESTIONNAIRE = 'QUESTIONNAIRE';
    public const EXTRACT_EGRIP = 'EXTRACT_EGRIP';
    public const IDENTITY_DOCUMENTS = 'IDENTITY_DOCUMENTS';
    public const COPY_SNILS = 'COPY_SNILS';
    public const OTHER = 'OTHER';


    public static $typesNamesLegalEntity = [
        FileType::EXTRACT_EGRUL => 'Копия выписки из ЕГРЮЛ',
        FileType::COPIES_CONSTITUENT_DOCUMENTS => 'Копии учредительных документов',
        FileType::COPY_TIN_CERTIFICATE => 'Копия свидетельства ИНН',
        FileType::IDENTITY_DOCUMENTS_HEAD => 'Копия документов, подтверждающих полномочия руководителя',
        FileType::IDENTITY_DOCUMENTS_MANAGER => 'Копия документов, подтверждающих личность руководителя',
        FileType::COPY_THE_HEAD_TIN_CERTIFICATE => 'Копия свидетельства ИНН руководителя',
        FileType::COPY_SNILS_HEAD => 'Копия СНИЛС руководителя',
        FileType::QUESTIONNAIRE => 'Опросный лист',
        FileType::OTHER => 'Дополнительные документы'
    ];

    public static $typesNamesIndividualEntrepreneur = [
        FileType::EXTRACT_EGRIP => 'Копия выписки из ЕГРИП',
        FileType::IDENTITY_DOCUMENTS => 'Копия документа удостоверяющего личность',
        FileType::COPY_TIN_CERTIFICATE => 'Копия свидетельства ИНН',
        FileType::COPY_SNILS => 'Копия СНИЛС',
        FileType::QUESTIONNAIRE => 'Опросный лист',
        FileType::OTHER => 'Дополнительные документы'
    ];

    public static $typesNamesIndividual = [
        FileType::IDENTITY_DOCUMENTS => 'Копия документа удостоверяющего личность',
        FileType::COPY_TIN_CERTIFICATE => 'Копия свидетельства ИНН',
        FileType::COPY_SNILS => 'Копия СНИЛС',
        FileType::QUESTIONNAIRE => 'Опросный лист',
        FileType::OTHER => 'Дополнительные документы'
    ];

    private $value;

    public static $keys = [
        FileType::EXTRACT_EGRUL,
        FileType::COPIES_CONSTITUENT_DOCUMENTS,
        FileType::COPY_TIN_CERTIFICATE,
        FileType::IDENTITY_DOCUMENTS_HEAD,
        FileType::IDENTITY_DOCUMENTS_MANAGER,
        FileType::COPY_THE_HEAD_TIN_CERTIFICATE,
        FileType::COPY_SNILS_HEAD,
        FileType::QUESTIONNAIRE,
        FileType::EXTRACT_EGRIP,
        FileType::IDENTITY_DOCUMENTS,
        FileType::COPY_SNILS,
        FileType::OTHER,
    ];

    /**
     * Status constructor.
     * @param string $value
     */
    public function __construct(string $value)
    {
        Assert::oneOf($value, [
            self::EXTRACT_EGRUL,
            self::COPIES_CONSTITUENT_DOCUMENTS,
            self::COPY_TIN_CERTIFICATE,
            self::IDENTITY_DOCUMENTS_HEAD,
            self::IDENTITY_DOCUMENTS_MANAGER,
            self::COPY_THE_HEAD_TIN_CERTIFICATE,
            self::COPY_SNILS_HEAD,
            self::QUESTIONNAIRE,
            self::EXTRACT_EGRIP,
            self::IDENTITY_DOCUMENTS,
            self::COPY_SNILS,
            self::OTHER
        ]);
        $this->value = $value;
    }

    /**
     * @param FileType $fileType
     * @return bool
     */
    public function isEqual(self $fileType): bool
    {
        return $this->getValue() === $fileType->getValue();
    }

    public function isIdentityDocument(): bool
    {
        return self::IDENTITY_DOCUMENTS === $this->getValue();
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    public function getLocalizedName(): string
    {
        $allCategories = array_merge(self::$typesNamesLegalEntity, self::$typesNamesIndividualEntrepreneur, self::$typesNamesIndividual);
        return $allCategories[$this->value];
    }

    /**
     * @param DetailView $detailView
     * @return array
     */
    public static function getFileCategories(DetailView $detailView)
    {
        if ($detailView->isLegalEntity()) {
            return self::$typesNamesLegalEntity;
        } elseif ($detailView->isIndividualEntrepreneur()) {
            return self::$typesNamesIndividualEntrepreneur;
        } elseif ($detailView->isIndividual()) {
            return self::$typesNamesIndividual;
        }

    }
}
