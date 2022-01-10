<?php
declare(strict_types=1);
namespace App\Model\User\Service\Certificate\IssuerConverter;

interface IssuerConverterInterface
{
    /**
     * Неструктурированное имя
     * @return string|null
     */
    public function toExtractUnstructuredName(): ? string;

    /**
     * Удостоверяющий центр
     * @return string
     */
    public function toExtractCertificationCenter(): string;

    /**
     * Страна
     * @return string
     */
    public function toExtractCountry(): string;

    /**
     * Регион
     * @return string
     */
    public function toExtractRegion(): string;

    /**
     * Адрес
     * @return string
     */
    public function toExtractStreet(): string;

    /**
     * Нименование организации
     * @return string|null
     */
    public function toExtractOrganizationName(): ? string;

    /**
     * Отдел или подразделение
     * @return string|null
     */
    public function toExtractOrganizationUnit(): ? string;

    /**
     * Должность
     * @return string
     */
    public function toExtractPosition(): ? string;

    /**
     * @return string|null
     */
    public function toExtractOgrn(): ? string;

    /**
     * @return string|null
     */
    public function toExtractOgrnIp(): ? string;

    /**
     * @return string
     */
    public function toExtractSnils(): string;

    /**
     * @return string
     */
    public function toExtractInn(): string;

    /**
     * Email
     * @return null|string
     */
    public function toExtractEmail(): ?string;

    /**
     * @return string
     */
    public function toExtractCity(): string;
}