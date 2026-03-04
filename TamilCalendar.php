<?php

final class TamilCalendar
{
    private DateTime $date;
    private static array $yearCycle = [];
    
    private static array $weekdays = [
        'Sunday'    => 'ஞாயிறு',
        'Monday'    => 'திங்கள்',
        'Tuesday'   => 'செவ்வாய்',
        'Wednesday' => 'புதன்',
        'Thursday'  => 'வியாழன்',
        'Friday'    => 'வெள்ளி',
        'Saturday'  => 'சனி'
    ];

    // Added the month index to make calculations easier
    private static array $months = [
        ['பங்குனி', 3, 14], // Start with March to handle the year-end transition better
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
        ['பங்குனி', 3, 14] 
    ];

    public function __construct(string $datetime = 'now', string $timezone = 'Asia/Kolkata')
    {
        $this->date = new DateTime($datetime, new DateTimeZone($timezone));

        if (empty(self::$yearCycle)) {
            $path = __DIR__ . '/years.json';
            self::$yearCycle = file_exists($path) 
                ? json_decode(file_get_contents($path), true) 
                : [];
        }
    }

    public function getTamilWeekday(): string 
    {
        return self::$weekdays[$this->date->format('l')];
    }

    public function getTamilYear(): int
    {
        $year = (int)$this->date->format('Y');
        $month = (int)$this->date->format('n');
        $day = (int)$this->date->format('j');

        // Tamil New Year is April 14th
        if ($month < 4 || ($month == 4 && $day < 14)) {
            return $year - 1;
        }
        return $year;
    }

    public function getTamilYearName(): string
    {
        $tamilYear = $this->getTamilYear();
        $baseYear = 1987; // Prabhava starts here

        $index = ($tamilYear - $baseYear) % 60;
        if ($index < 0) $index += 60;

        return (self::$yearCycle[$index] ?? "Unknown") . " வருடம்";
    }

    /**
     * Helper to find the current Tamil Month index and its start date
     */
    private function getMonthData(): array
    {
        $year = (int)$this->date->format('Y');
        $currentTimestamp = $this->date->getTimestamp();
        
        // We check from Chithirai (index 1) to Panguni (index 12)
        for ($i = 1; $i <= 12; $i++) {
            $m = self::$months[$i];
            $start = new DateTime("$year-{$m[1]}-{$m[2]}", $this->date->getTimezone());
            
            // If current date is before this month's start, it belongs to the previous month
            if ($currentTimestamp < $start->getTimestamp()) {
                $prevMonth = self::$months[$i - 1];
                $prevYear = ($i === 1) ? $year - 1 : $year;
                $prevStart = new DateTime("$prevYear-{$prevMonth[1]}-{$prevMonth[2]}", $this->date->getTimezone());
                
                return [
                    'name' => $prevMonth[0],
                    'start' => $prevStart
                ];
            }
        }

        // Fallback for dates after March 14 (Panguni)
        $panguni = self::$months[12];
        return [
            'name' => $panguni[0],
            'start' => new DateTime("$year-{$panguni[1]}-{$panguni[2]}", $this->date->getTimezone())
        ];
    }

    public function getTamilMonth(): string
    {
        return $this->getMonthData()['name'];
    }

    public function getTamilDay(): int
    {
        $data = $this->getMonthData();
        return (int)$data['start']->diff($this->date)->days + 1;
    }

    // ... formatFull and formatDual remain the same ...
}
