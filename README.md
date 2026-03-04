# TamilCalendar PHP

A lightweight, production-grade Tamil Solar Calendar module for PHP. This library provides accurate Gregorian to Tamil date conversion based on the traditional civil solar calendar.

Designed for business applications, SaaS products, CMS systems, and cultural platforms requiring reliable Tamil calendar integration without the overhead of heavy astronomical libraries.

## 🌟 Features

* **Correct 60-year Samvatsara cycle:** Automatically maps years to traditional names (e.g., Vilambi, Vikari, Sobakirthu).
* **April 14th Rollover Logic:** Corrects the year increment based on the Tamil New Year (Puthandu).
* **Full UTF-8 Support:** Safe Tamil translations for months, weekdays, and year names.
* **Solar Month Boundaries:** Approximates civil solar calendar boundaries.
* **Timezone-Aware:** Built on PHP's `DateTime` classes (defaults to `Asia/Kolkata`).
* **Zero Dependencies:** Pure PHP, no external packages required.

## 📋 Requirements

* PHP 7.4 or higher
* `mbstring` extension (for UTF-8 string handling)

## 🚀 Installation

Simply download `TamilCalendar.php` and include it in your project:
## 📖 Usage

### Basic Example
Get a fully formatted string in one call:

```php
use TamilCalendar\TamilCalendar;

// From a specific date and time
$tamil = new TamilCalendar("2018-04-24 14:37:53");
echo $tamil->formatFull(); 
// Output: செவ்வாய், சித்திரை 11, 2018 [விளம்பி வருடம்] 02:37:53 PM

// Current time
$now = new TamilCalendar();
echo $now->formatFull();
```

```php
require_once 'path/to/TamilCalendar.php';
```

### Accessing Individual Components
You can extract specific Tamil calendar parts for custom UI elements:

```php
$tamil = new TamilCalendar("2024-01-15");

echo $tamil->getTamilDay();       // 1 (Thai 1 - Pongal)
echo $tamil->getTamilMonth();     // தை
echo $tamil->getTamilYearName();  // சோபகிருது
echo $tamil->getTamilWeekday();   // திங்கள்
```

## ⚙️ Technical Notes

* **Logic Type:** This follows the Tamil Civil Solar Calendar.
* **Precision:** This library is designed for civil use (dates and months). It does not provide astronomical Panchangam precision (exact Sankranti transition moments) or lunar tithi/nakshatra calculations.
* **New Year:** The year rollover is fixed to April 14th, consistent with the standard Tamil civil calendar (Vishu/Puthandu).

## 📄License

[MIT](https://choosealicense.com/licenses/mit/)

This project is licensed under the MIT License - see the LICENSE file for details.

Developed for the Tamil community and developers worldwide.
