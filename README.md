# TamilCalendar PHP

A lightweight, dependency-free Tamil civil solar calendar library for PHP.

`TamilCalendar` converts Gregorian dates into Tamil calendar information including:

* Tamil day
* Tamil month
* Tamil year
* 60-year Tamil year name
* Tamil weekday
* Formatted Tamil dates

The library is designed to be simple enough for business applications, SaaS products, CMS platforms, websites, and cultural applications that need Tamil calendar support without requiring a large astronomical library.

> **Important:** This library implements a deterministic Tamil civil-calendar model using defined month-boundary rules. It is **not an astronomical Panchangam** and does not calculate exact astronomical Sankranti transition times.

---

## 🌟 Features

* **Tamil 60-year cycle**

  * Supports the traditional 60-year Samvatsara cycle.
  * Year names are loaded from `years.json`.

* **Tamil New Year calculation**

  * Tamil year rollover is based on April 14 in the civil-calendar model used by this library.

* **Tamil month calculation**

  * Converts Gregorian dates into Tamil months using the defined civil month-boundary rules.

* **Tamil day calculation**

  * Returns the day number within the current Tamil month.

* **Tamil weekdays**

  * Provides Tamil names for all seven weekdays.

* **Timezone aware**

  * Uses PHP's `DateTimeImmutable` and `DateTimeZone`.
  * Defaults to `Asia/Kolkata`.

* **UTF-8 Tamil support**

  * Tamil month, weekday, and year names are stored directly as UTF-8 strings.

* **Zero dependencies**

  * Uses only PHP's built-in date/time and JSON functionality.

* **Immutable date handling**

  * Uses `DateTimeImmutable` internally to prevent accidental date mutation.

* **Simple API**

  * Individual calendar components are available through dedicated methods.
  * Structured output is available through `toArray()`.

---

## 📦 Project Structure

The library intentionally keeps a simple structure:

```text
TamilCalendar/
├── LICENSE
├── README.md
├── TamilCalendar.php
└── years.json
```

There are no external dependencies or framework requirements.

---

## 📋 Requirements

* PHP **8.2 or higher**
* PHP `DateTimeImmutable`
* PHP `DateTimeZone`
* PHP JSON extension

No Composer packages are required.

> `mbstring` is not required by the current implementation.

---

## 🚀 Installation

Download the following files:

```text
TamilCalendar.php
years.json
```

Keep both files in the same directory.

For example:

```text
my-project/
├── TamilCalendar.php
└── years.json
```

Then include the PHP file:

```php
require_once __DIR__ . '/TamilCalendar.php';
```

---

## 📖 Usage

### Basic Example

```php
require_once __DIR__ . '/TamilCalendar.php';

$tamil = new TamilCalendar('2018-04-24 14:37:53');

echo $tamil->format();
```

Output:

```text
11 சித்திரை 2018
```

---

### Full Tamil Date

Use `formatFull()` to include the Tamil 60-year cycle name:

```php
require_once __DIR__ . '/TamilCalendar.php';

$tamil = new TamilCalendar('2018-04-24 14:37:53');

echo $tamil->formatFull();
```

Output:

```text
11 சித்திரை 2018 - விளம்பி வருடம்
```

---

### Gregorian + Tamil Date

Use `formatDual()` when you want both dates:

```php
$tamil = new TamilCalendar('2018-04-24 14:37:53');

echo $tamil->formatDual();
```

Output:

```text
2018-04-24 | 11 சித்திரை 2018
```

---

## 🧩 Accessing Individual Components

You can retrieve each part of the Tamil date independently.

```php
$tamil = new TamilCalendar('2024-01-15');

echo $tamil->getTamilDay();
echo $tamil->getTamilMonth();
echo $tamil->getTamilYear();
echo $tamil->getTamilYearName();
echo $tamil->getTamilWeekday();
```

Example:

```text
1
தை
2023
சோபகிருது வருடம்
திங்கள்
```

The Tamil year is determined by the April 14 rollover rule used by this library.

---

## 📊 Structured Output

For applications that need structured data, use `toArray()`:

```php
$tamil = new TamilCalendar('2024-01-15');

print_r($tamil->toArray());
```

Example result:

```php
[
    'day' => 1,
    'month' => 'தை',
    'year' => 2023,
    'year_name' => 'சோபகிருது வருடம்',
    'weekday' => 'திங்கள்',
]
```

This is useful for:

* APIs
* JSON responses
* CMS templates
* Database processing
* Frontend applications
* Custom UI components

For example:

```php
echo json_encode(
    $tamil->toArray(),
    JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
);
```

---

## 🗓️ Tamil Month Rules

The current implementation uses the following fixed civil month boundaries:

| Tamil Month | Starts       |
| ----------- | ------------ |
| சித்திரை    | April 14     |
| வைகாசி      | May 15       |
| ஆனி         | June 15      |
| ஆடி         | July 16      |
| ஆவணி        | August 17    |
| புரட்டாசி   | September 17 |
| ஐப்பசி      | October 17   |
| கார்த்திகை  | November 16  |
| மார்கழி     | December 16  |
| தை          | January 14   |
| மாசி        | February 13  |
| பங்குனி     | March 14     |

These rules are intentionally deterministic so that the same Gregorian date always produces the same Tamil calendar result.

---

## 🌅 Tamil New Year

The library uses **April 14** as the Tamil New Year boundary.

For example:

```text
2026-04-13
```

belongs to the previous Tamil year.

While:

```text
2026-04-14
```

starts the new Tamil year.

This behaviour is controlled by the civil-calendar rules in `TamilCalendar.php`.

---

## 🔄 60-Year Tamil Cycle

Tamil calendar years follow a repeating 60-year cycle.

The names are stored in:

```text
years.json
```

The library uses:

```text
1987
```

as the base Gregorian year for the beginning of the cycle:

```text
1987 → பிரபவ
```

After 60 years, the cycle repeats.

The library validates `years.json` and expects exactly **60 valid year names**.

If the file is missing, unreadable, invalid, or contains an incorrect number of entries, the library throws an exception instead of silently returning incorrect calendar data.

---

## 🌏 Timezone Support

The default timezone is:

```text
Asia/Kolkata
```

Example:

```php
$tamil = new TamilCalendar(
    '2026-04-14 00:30:00',
    'Asia/Kolkata'
);
```

A different PHP-supported timezone can be supplied:

```php
$tamil = new TamilCalendar(
    '2026-04-14 00:30:00',
    'Asia/Singapore'
);
```

Timezone handling is performed using PHP's `DateTimeZone`.

---

## 🕐 Current Date and Time

You can create an instance without providing a date:

```php
$tamil = new TamilCalendar();

echo $tamil->formatFull();
```

The current date and time are obtained from PHP using the configured timezone.

---

## 🧱 API Reference

### `getTamilDay(): int`

Returns the day number within the current Tamil month.

```php
$tamil->getTamilDay();
```

Example:

```text
11
```

---

### `getTamilMonth(): string`

Returns the Tamil month name.

```php
$tamil->getTamilMonth();
```

Example:

```text
சித்திரை
```

---

### `getTamilYear(): int`

Returns the Tamil civil year.

```php
$tamil->getTamilYear();
```

Example:

```text
2026
```

---

### `getTamilYearName(): string`

Returns the Tamil 60-year cycle name.

```php
$tamil->getTamilYearName();
```

Example:

```text
பராபவ வருடம்
```

---

### `getTamilWeekday(): string`

Returns the Tamil weekday.

```php
$tamil->getTamilWeekday();
```

Example:

```text
வெள்ளி
```

---

### `getDate(): DateTimeImmutable`

Returns the internal Gregorian date as a `DateTimeImmutable`.

```php
$date = $tamil->getDate();

echo $date->format('Y-m-d H:i:s');
```

---

### `toArray(): array`

Returns all major Tamil calendar components as an associative array.

```php
$tamil->toArray();
```

---

### `format(): string`

Returns a simple Tamil date.

```text
11 சித்திரை 2026
```

---

### `formatFull(): string`

Returns the Tamil date together with the Tamil year name.

```text
11 சித்திரை 2026 - பராபவ வருடம்
```

---

### `formatDual(): string`

Returns both Gregorian and Tamil dates.

```text
2026-04-24 | 11 சித்திரை 2026
```

---

## ⚙️ Technical Notes

### Calendar Model

This library follows a **Tamil civil solar calendar model**.

The implementation is intentionally lightweight and deterministic.

It does not attempt to reproduce astronomical calculations.

### What this library provides

```text
Gregorian Date
      ↓
Tamil Year
      ↓
Tamil Year Name
      ↓
Tamil Month
      ↓
Tamil Day
      ↓
Tamil Weekday
```

### What this library does not provide

This library does not calculate:

* Exact astronomical Sankranti times
* Sunrise and sunset
* Tithi
* Nakshatra
* Yoga
* Karana
* Rahu Kalam
* Yamagandam
* Gulikai
* Lunar calendar calculations
* Astronomical Panchangam data

If your application requires those calculations, an astronomical/Panchangam library should be used instead.

---

## 🛡️ Error Handling

The library validates the `years.json` file during initialization.

Exceptions are thrown when:

* `years.json` does not exist
* `years.json` cannot be read
* `years.json` contains invalid JSON
* The year cycle does not contain exactly 60 names
* A year-cycle entry is empty or invalid
* An invalid date is supplied
* An invalid timezone is supplied

This prevents invalid calendar data from being silently returned.

---

## 🔤 UTF-8

Tamil names are stored as native UTF-8 strings.

For example:

```php
echo $tamil->getTamilMonth();
```

produces:

```text
சித்திரை
```

Make sure your application, database, HTTP response, and HTML pages use UTF-8.

For HTML:

```html
<meta charset="UTF-8">
```

For JSON APIs:

```php
json_encode(
    $tamil->toArray(),
    JSON_UNESCAPED_UNICODE
);
```

---

## 🧪 Example

A complete example:

```php
<?php

require_once __DIR__ . '/TamilCalendar.php';

$tamil = new TamilCalendar(
    '2026-04-24 14:37:53',
    'Asia/Kolkata'
);

echo 'Tamil Date: ' . $tamil->format() . PHP_EOL;
echo 'Full Date: ' . $tamil->formatFull() . PHP_EOL;
echo 'Gregorian + Tamil: ' . $tamil->formatDual() . PHP_EOL;
echo 'Day: ' . $tamil->getTamilDay() . PHP_EOL;
echo 'Month: ' . $tamil->getTamilMonth() . PHP_EOL;
echo 'Year: ' . $tamil->getTamilYear() . PHP_EOL;
echo 'Year Name: ' . $tamil->getTamilYearName() . PHP_EOL;
echo 'Weekday: ' . $tamil->getTamilWeekday() . PHP_EOL;
```

---

## 📄 License

This project is licensed under the MIT License.

See the [`LICENSE`](LICENSE) file for the complete license text.

---

## 🤝 Contributing

Contributions, bug reports, corrections, and suggestions are welcome.

When proposing changes to calendar calculations, please include:

1. The Gregorian date.
2. The expected Tamil date.
3. The source or calendar convention used to determine the expected result.
4. An explanation of why the current implementation differs.

Calendar-rule changes should be made carefully because even a one-day boundary change can affect Tamil year, month, and day calculations.

---

## ⚠️ Calendar Accuracy

This project should be understood as a **civil-calendar implementation**, not an astronomical Panchangam.

The month boundaries are defined explicitly in the source code and are intended for deterministic civil/business use.

If your application requires astronomical accuracy, the exact transition time of the Sun's movement between zodiac signs, or traditional Panchangam calculations, this library is not intended to replace a dedicated astronomical calendar system.

---

## ❤️ About

Built for Tamil developers and applications that need a simple, lightweight, and dependency-free Tamil calendar implementation.

Useful for:

* Tamil websites
* Business applications
* SaaS products
* CMS platforms
* Cultural applications
* Tamil date displays
* APIs
* Educational projects
* Personal projects

---

## ⭐ Summary

```text
TamilCalendar
├── Zero external dependencies
├── PHP 8.2+
├── Tamil civil solar calendar
├── 60-year Tamil cycle
├── Tamil month/day conversion
├── Tamil weekday names
├── Timezone aware
├── UTF-8 Tamil support
└── Simple PHP API
```
