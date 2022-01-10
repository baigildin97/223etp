<?php
declare(strict_types=1);
namespace App\Model\User\Service\Certificate\SubjectConverter;


class SubjectConverter implements SubjectConverterInterface
{
    /**
     * @var array
     */
    private $subjectNameData;

    public function __construct(string $subjectNameData) {
        $this->subjectNameData = $subjectNameData;
    }

    /**
     * @param string $signature
     * @return string|null
     */
    private function extractor(string $signature): ? string {
        preg_match("/(\s|^)" . $signature . "=(.*?)(,|$)/", $this->subjectNameData, $result);

        return isset($result[2]) ? $result[2] : null;
    }

    /**
     * Владелец
     * @return string
     */
    public function toExtractOwner(): string {
        return $this->extractor('CN');
    }

    /**
     * Ф.И.О представителя
     * @return string
     */
    public function toExtractUserName(): string {
        return $this->extractor('SN').' '.$this->extractor('G');
    }

    /**
     * Страна
     * @return string
     */
    public function toExtractCountry(): ? string {
        return $this->extractor('C');
    }

    /**
     * Регион
     * @return string
     */
    public function toExtractRegion(): ? string {
        return $this->extractor('S');
    }

    /**
     * @return string
     */
    public function toExtractStreet(): ? string {
        return $this->extractor('STREET');
    }

    /**
     * Нименование организации
     * @return string|null
     */
    public function toExtractOrganizationName(): ? string {
        return $this->extractor('O');
    }

    /**
     * Отдел или подразделение
     * @return string|null
     */
    public function toExtractOrganizationUnit(): ? string {
        return $this->extractor('OU');
    }

    /**
     * Должность
     * @return string
     */
    public function toExtractPosition(): ? string {
        return $this->extractor('T');
    }

    /**
     * @return string|null
     */
    public function toExtractOgrn(): ? string {
        return $this->extractor('OGRN');
    }

    /**
     * @return string|null
     */
    public function toExtractOgrnIp(): ? string {
        return $this->extractor('OGRNIP');
    }

    /**
     * @return string
     */
    public function toExtractSnils(): string {
        return $this->extractor('SNILS');
    }

    /**
     * @return string
     */
    public function toExtractInn(): string {
        return $this->extractor('INN');
    }

    /**
     * Email
     * @return string
     */
    public function toExtractEmail(): ? string {
        return $this->extractor('E');
    }

    /**
     * @return string|null
     */
    public function toExtractCity(): ? string {
        return $this->extractor('L');
    }
}