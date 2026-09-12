<?php

declare(strict_types=1);

/**
 * Tamil Calendar
 *
 * A lightweight Tamil civil calendar implementation.
 *
 * This implementation uses fixed civil month boundary rules.
 * It is not an astronomical Panchangam and does not calculate
 * astronomical Sankranti transition times.
 * https://github.com/santhoshanand/
 */
final class TamilCalendar
{
    private const DEFAULT_TIMEZONE = 'Asia/Kolkata';

    /**
     * Tamil New Year begins on April 14 in this civil-calendar model.
     */
    private const TAMIL_NEW_YEAR_MONTH = 4;
    private const TAMIL_NEW_YEAR_DAY = 14;

    /**
     * The 60-year Tamil cycle starts with Prabhava in Gregorian year 1987.
     */
    private const YEAR_CYCLE_BASE_YEAR = 1987;
    private const YEAR_CYCLE_LENGTH = 60;

    /**
     * Tamil weekday names.
     */
    private const WEEKDAYS = [
        'Sunday'    => 'ஞாயிறு',
        'Monday'    => 'திங்கள்',
        'Tuesday'   => 'செவ்வாய்',
        'Wednesday' => 'புதன்',
        'Thursday'  => 'வியாழன்',
        'Friday'    => 'வெள்ளி',
        'Saturday'  => 'சனி',
    ];

    /**
     * Tamil month definitions.
     *
     * Each entry contains:
     * [Tamil month name, Gregorian month, Gregorian starting day]
     *
     * Panguni is included at both ends so that month calculations
     * around the Tamil year boundary remain straightforward.
     */
    private const MONTHS = [
        ['பங்குனி', 3, 14],
        ['சித்திரை', 4, 14],
        ['வைகாசி', 5, 15],
        ['ஆனி', 6, 15],
        ['ஆடி', 7, 16],
        ['ஆவணி', 8, 17],
        ['புரட்டாசி', 9, 17],
        ['ஐப்பசி', 10, 17],
        ['கார்த்திகை', 11, 16],
        ['மார்கழி', 12, 16],
        ['தை', 1, 14],
        ['மாசி', 2, 13],
        ['பங்குனி', 3, 14],
    ];

    private DateTimeImmutable $date;

    /**
     * Cached 60-year cycle.
     *
     * @var list<string>
     */
    private static array $yearCycle = [];

    public function __construct(
        string $datetime = 'now',
        string $timezone = self::DEFAULT_TIMEZONE
    ) {
        $this->date = $this->createDate($datetime, $timezone);

        self::loadYearCycle();
    }

    /**
     * Get the Gregorian date represented by this instance.
     *
     * This is useful for debugging and consumers that need the
     * normalized date/time value.
     */
    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * Get the Tamil weekday name.
     */
    public function getTamilWeekday(): string
    {
        $weekday = $this->date->format('l');

        return self::WEEKDAYS[$weekday];
    }

    /**
     * Get the Tamil year number.
     *
     * Example:
     * 2026-04-13 => 2025
     * 2026-04-14 => 2026
     */
    public function getTamilYear(): int
    {
        $year = (int) $this->date->format('Y');
        $month = (int) $this->date->format('n');
        $day = (int) $this->date->format('j');

        if (
            $month < self::TAMIL_NEW_YEAR_MONTH
            || (
                $month === self::TAMIL_NEW_YEAR_MONTH
                && $day < self::TAMIL_NEW_YEAR_DAY
            )
        ) {
            return $year - 1;
        }

        return $year;
    }

    /**
     * Get the Tamil 60-year cycle name.
     *
     * Example:
     * 1987 => பிரபவ வருடம்
     */
    public function getTamilYearName(): string
    {
        $tamilYear = $this->getTamilYear();

        $index = (
            $tamilYear - self::YEAR_CYCLE_BASE_YEAR
        ) % self::YEAR_CYCLE_LENGTH;

        if ($index < 0) {
            $index += self::YEAR_CYCLE_LENGTH;
        }

        if (!isset(self::$yearCycle[$index])) {
            throw new RuntimeException(
                'Tamil year cycle data is incomplete.'
            );
        }

        return self::$yearCycle[$index] . ' வருடம்';
    }

    /**
     * Get the current Tamil month name.
     */
    public function getTamilMonth(): string
    {
        return $this->getMonthData()['name'];
    }

    /**
     * Get the current Tamil month day.
     *
     * The first day of a Tamil month is 1.
     */
    public function getTamilDay(): int
    {
        $data = $this->getMonthData();

        $startDate = $data['start']->setTime(0, 0, 0);
        $currentDate = $this->date->setTime(0, 0, 0);

        return $startDate->diff($currentDate)->days + 1;
    }

    /**
     * Return the complete Tamil calendar date as an array.
     *
     * @return array{
     *     day: int,
     *     month: string,
     *     year: int,
     *     year_name: string,
     *     weekday: string
     * }
     */
    public function toArray(): array
    {
        return [
            'day' => $this->getTamilDay(),
            'month' => $this->getTamilMonth(),
            'year' => $this->getTamilYear(),
            'year_name' => $this->getTamilYearName(),
            'weekday' => $this->getTamilWeekday(),
        ];
    }

    /**
     * Format the Tamil date.
     *
     * Example:
     * 11 சித்திரை 2026
     */
    public function format(): string
    {
        return sprintf(
            '%d %s %d',
            $this->getTamilDay(),
            $this->getTamilMonth(),
            $this->getTamilYear()
        );
    }

    /**
     * Format the Tamil date including the Tamil year name.
     *
     * Example:
     * 11 சித்திரை 2026 - பராபவ வருடம்
     */
    public function formatFull(): string
    {
        return sprintf(
            '%d %s %d - %s',
            $this->getTamilDay(),
            $this->getTamilMonth(),
            $this->getTamilYear(),
            $this->getTamilYearName()
        );
    }

    /**
     * Format the Gregorian and Tamil dates together.
     *
     * Example:
     * 2026-04-24 | 11 சித்திரை 2026
     */
    public function formatDual(): string
    {
        return sprintf(
            '%s | %s',
            $this->date->format('Y-m-d'),
            $this->format()
        );
    }

    /**
     * Create an immutable date with the requested timezone.
     */
    private function createDate(
        string $datetime,
        string $timezone
    ): DateTimeImmutable {
        try {
            $dateTimezone = new DateTimeZone($timezone);

            return new DateTimeImmutable(
                $datetime,
                $dateTimezone
            );
        } catch (Exception $exception) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid date or timezone: "%s" / "%s".',
                    $datetime,
                    $timezone
                ),
                0,
                $exception
            );
        }
    }

    /**
     * Load the 60-year Tamil cycle from years.json.
     */
    private static function loadYearCycle(): void
    {
        if (self::$yearCycle !== []) {
            return;
        }

        $path = __DIR__ . DIRECTORY_SEPARATOR . 'years.json';

        if (!is_file($path)) {
            throw new RuntimeException(
                sprintf(
                    'Tamil year cycle file not found: %s',
                    $path
                )
            );
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException(
                sprintf(
                    'Unable to read Tamil year cycle file: %s',
                    $path
                )
            );
        }

        try {
            $data = json_decode(
                $contents,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException $exception) {
            throw new RuntimeException(
                'Tamil year cycle file contains invalid JSON.',
                0,
                $exception
            );
        }

        if (!is_array($data)) {
            throw new RuntimeException(
                'Tamil year cycle data must be a JSON array.'
            );
        }

        if (count($data) !== self::YEAR_CYCLE_LENGTH) {
            throw new RuntimeException(
                sprintf(
                    'Tamil year cycle must contain exactly %d names; %d found.',
                    self::YEAR_CYCLE_LENGTH,
                    count($data)
                )
            );
        }

        foreach ($data as $index => $name) {
            if (!is_string($name) || trim($name) === '') {
                throw new RuntimeException(
                    sprintf(
                        'Invalid Tamil year name at index %d.',
                        $index
                    )
                );
            }
        }

        /** @var list<string> $data */
        self::$yearCycle = array_values($data);
    }

    /**
     * Find the current Tamil month and its starting date.
     *
     * @return array{
     *     name: string,
     *     start: DateTimeImmutable
     * }
     */
    private function getMonthData(): array
    {
        $year = (int) $this->date->format('Y');

        /*
         * Work with calendar dates at midnight instead of timestamps.
         *
         * This avoids treating a DST transition as a calendar-day
         * boundary and makes the calculation deterministic.
         */
        $currentDate = $this->date->setTime(0, 0, 0);

        /*
         * Check Chithirai through Panguni.
         *
         * If the current date is before the current month's start,
         * it belongs to the previous Tamil month.
         */
        for ($i = 1; $i <= 12; $i++) {
            $month = self::MONTHS[$i];

            $start = $this->createMonthStart(
                $year,
                $month[1],
                $month[2]
            );

            if ($currentDate < $start) {
                $previousMonth = self::MONTHS[$i - 1];

                $previousYear = $i === 1
                    ? $year - 1
                    : $year;

                $previousStart = $this->createMonthStart(
                    $previousYear,
                    $previousMonth[1],
                    $previousMonth[2]
                );

                return [
                    'name' => $previousMonth[0],
                    'start' => $previousStart,
                ];
            }
        }

        /*
         * Dates from March 14 onwards belong to Panguni.
         */
        $panguni = self::MONTHS[12];

        return [
            'name' => $panguni[0],
            'start' => $this->createMonthStart(
                $year,
                $panguni[1],
                $panguni[2]
            ),
        ];
    }

    /**
     * Create a month-start date in the same timezone as the current date.
     */
    private function createMonthStart(
        int $year,
        int $month,
        int $day
    ): DateTimeImmutable {
        return (new DateTimeImmutable(
            'now',
            $this->date->getTimezone()
        ))->setDate(
            $year,
            $month,
            $day
        )->setTime(0, 0, 0);
    }
}
