<?php
declare(strict_types=1);
namespace App\Model\User\Service\Certificate\IssuerConverter;


class IssuerConverter implements IssuerConverterInterface
{
    /**
     * @var array
     */
    private $issuerNameData;

    public function __construct(string $issuerNameData) {
        $this->issuerNameData = $issuerNameData;
    }

    /**
     * @param string $signature
     * @return string
     */
    private function extractor(string $signature): ? string {
        preg_match("/(\s|^)" . $signature . "=(.*?)(,|$)/", $this->issuerNameData, $result);

        return isset($result[2]) ? $result[2] : null;
    }

    /**
     * Неструктурированное имя
     * @return string|null
     */
    public function toExtractUnstructuredName(): ? string {
        return $this->extractor('UnstructuredName');
    }

    /**
     * Удостоверяющий центр
     * @return string
     */
    public function toExtractCertificationCenter(): string {
        return $this->extractor('CN');
    }

    /**
     * Страна
     * @return string
     */
    public function toExtractCountry(): string {
        return $this->extractor('C');
    }

    /**
     * Регион
     * @return string
     */
    public function toExtractRegion(): string {
        return $this->extractor('S');
    }

    /**
     * Адрес
     * @return string
     */
    public function toExtractStreet(): string {
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
     * @return null|string
     */
    public function toExtractEmail(): ?string {
        return $this->extractor('E');
    }

    /**
     * @return string
     */
    public function toExtractCity(): string {
        return $this->extractor('L');
    }
}