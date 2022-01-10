<?php
declare(strict_types=1);

namespace App\Helpers;

/**
 * Class Filter
 * @package App\Helpers
 */
class Filter
{
    /**
     * @param string $value
     * @return string|string[]|null
     */
    public function onlyNumbers(string $value): string
    {
        return preg_replace("/[^0-9]/", '', $value);
    }

    /**
     * Фильтрация инн для юр. лица
     * @param string $number
     * @return string
     */
    public static function filterInnLegalEntity(string $number): string{
        return substr($number, 2);
    }

    /**
     * @param string $date
     * @param bool|null $only_date
     * @return string
     */
    public static function date(string $date, ?bool $only_date = false): string{
        if($only_date){
            return date("d.m.Y", strtotime($date));
        }
        return date("d.m.Y H:i:s", strtotime($date));
    }

    /**
     * @param string $value
     * @return string
     */
    public static function country(string $value): string{
        return "Российская Федерация";
    }

}