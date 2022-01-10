<?php


namespace App\Widget\Admin\User;


use App\ReadModel\Admin\Holidays\HolidaysFetcher;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DateCalcWidget extends AbstractExtension
{
    private $fetcher;
    public const PROCESSING_WORKING_DAYS_LIMIT = 5;

    public function __construct(HolidaysFetcher $fetcher)
    {
        $this->fetcher = $fetcher;
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('calc_processing_period', [$this, 'holidays'], ['needs_environment' => true, 'is_safe' =>['html']])
        ];
    }

    public function holidays(Environment $twig, string $date): string {
        $holidays = $this->fetcher->allActive();

        $processingDateStart = new \DateTimeImmutable($date);

        if (($weekDay = date('w', $processingDateStart->getTimestamp())) === '6')
            $processingDateEnd = $processingDateStart->add(
                new \DateInterval('P' . (self::PROCESSING_WORKING_DAYS_LIMIT + 1) . 'D')
            );
        elseif ($weekDay === '7')
            $processingDateEnd = $processingDateStart->add(
                new \DateInterval('P' . self::PROCESSING_WORKING_DAYS_LIMIT . 'D')
            );
        else
            $processingDateEnd = $processingDateStart->add(
                new \DateInterval('P' . (self::PROCESSING_WORKING_DAYS_LIMIT + 2) . 'D')
            );

        foreach ($holidays as $holiday)
        {
            if (!($processingDateEnd < ($holidayStart = new \DateTimeImmutable($holiday['date_start'])) ||
                $processingDateStart > ($holidayEnd = new \DateTimeImmutable($holiday['date_end']))))
            {
                $processingDateEnd = $processingDateEnd->add(
                    ($holidayStart < $processingDateStart ? $processingDateStart : $holidayStart)
                        ->diff($holidayEnd, true)
                );
            }
        }

        return $processingDateEnd->format('d.m.Y').'23:59:59';
    }
}