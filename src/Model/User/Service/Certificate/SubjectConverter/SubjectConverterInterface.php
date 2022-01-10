<?php
declare(strict_types=1);
namespace App\Model\User\Service\Certificate\SubjectConverter;


interface SubjectConverterInterface
{
    /**
     * Владелец
     * @return string
     */
    public function toExtractOwner(): string;

    /**
     * Ф.И.О представителя
     * @return string
     */
    public function toExtractUserName(): string;

    /**
     * Страна
     * @return string
     */
    public function toExtractCountry(): ? string;

    /**
     * Регион
     * @return string
     */
    public function toExtractRegion(): ? string;

    /**
     * @return string
     */
    public function toExtractStreet(): ? string;

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
     * @return string
     */
    public function toExtractEmail(): ? string;

   /**
     * @return string|null
     */ 
    public function toExtractCity(): ? string;


}