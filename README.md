# devxisas/laravel-qrcode

[![Latest Version on Packagist](https://img.shields.io/packagist/v/devxisas/laravel-qrcode.svg?style=flat-square)](https://packagist.org/packages/devxisas/laravel-qrcode)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/devxisas/laravel-qrcode/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/devxisas/laravel-qrcode/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/devxisas/laravel-qrcode.svg?style=flat-square)](https://packagist.org/packages/devxisas/laravel-qrcode)
[![License](https://img.shields.io/packagist/l/devxisas/laravel-qrcode.svg?style=flat-square)](LICENSE)

A modern QR code generator for Laravel. Inspired by and built upon the foundation of [simplesoftwareio/simple-qrcode](https://github.com/SimpleSoftwareIO/simple-qrcode), this package brings full PHP 8.2+ type safety, enum-based APIs, new data types (vCard, MeCard, Calendar Events), a Blade directive, a response macro, a `toDataUri()` helper, and an Artisan command — while keeping the same familiar fluent interface.

> [!NOTE]
> **Migrating from `simplesoftwareio/simple-qrcode`?** See the [migration guide](#migrating-from-simplesoftwareiosimple-qrcode) at the bottom of this page.

---

<!-- SCREENSHOT: Full demo page showing QR codes in various styles, colors, and formats -->
<!-- Place a screenshot of the demo page here once captured -->

---

## Requirements

| Package version | Laravel | PHP   |
|-----------------|---------|-------|
| 1.x             | 11, 12  | 8.2+  |

## Installation

```bash
composer require devxisas/laravel-qrcode
```

The service provider is registered automatically via Laravel's package auto-discovery.

For PNG generation, the `ext-gd` extension is required (installed by default on most servers). For best PNG quality, `ext-imagick` is recommended:

```bash
composer require ext-imagick
```

### Publish the config (optional)

```bash
php artisan vendor:publish --tag="laravel-qrcode-config"
```

This creates `config/laravel-qrcode.php` where you can set package-wide defaults.

---

## Basic Usage

Use the `QrCode` facade anywhere in your application:

```php
use Devxisas\LaravelQrCode\Facades\QrCode;

// Generate an SVG (default) — safe to render directly in Blade with {!! !!}
$svg = QrCode::generate('https://devxisas.com');

// Save to a file
QrCode::generate('https://devxisas.com', '/path/to/qrcode.svg');
```

In a Blade template:

```blade
{!! QrCode::size(200)->generate('https://devxisas.com') !!}
```

---

## Configuration

```php
// config/laravel-qrcode.php
return [
    'format'           => 'svg',   // svg | eps | png
    'size'             => 200,
    'margin'           => 0,
    'error_correction' => 'M',     // L | M | Q | H
];
```

---

## Formats

Three output formats are supported. You can pass a string or the `Format` enum.

```php
use Devxisas\LaravelQrCode\Enums\Format;

QrCode::generate('...');                          // SVG (default)
QrCode::format('png')->generate('...');           // PNG
QrCode::format(Format::Png)->generate('...');     // PNG via enum
QrCode::format('eps')->generate('...');           // EPS (vector, no browser preview)
```

<!-- SCREENSHOT: Three cards side by side — SVG, PNG, EPS placeholder -->

---

## Size & Margin

```php
QrCode::size(300)->generate('...');
QrCode::size(300)->margin(4)->generate('...');
```

---

## Error Correction

Higher levels allow the QR code to be read even when partially obscured (e.g. with a logo overlay).

```php
use Devxisas\LaravelQrCode\Enums\ErrorCorrection;

QrCode::errorCorrection('H')->generate('...');
QrCode::errorCorrection(ErrorCorrection::High)->generate('...');
```

| Level | Enum                        | Data recovery |
|-------|-----------------------------|---------------|
| `L`   | `ErrorCorrection::Low`      | 7%            |
| `M`   | `ErrorCorrection::Medium`   | 15% (default) |
| `Q`   | `ErrorCorrection::Quartile` | 25%           |
| `H`   | `ErrorCorrection::High`     | 30%           |

<!-- SCREENSHOT: Four QR codes labeled L / M / Q / H -->

---

## Module Styles

```php
use Devxisas\LaravelQrCode\Enums\Style;

QrCode::style('square')->generate('...');          // default
QrCode::style('dot', 0.5)->generate('...');
QrCode::style('round', 0.7)->generate('...');

// Enum API
QrCode::style(Style::Dot, 0.5)->generate('...');
```

<!-- SCREENSHOT: Square / Dot / Round side by side -->

---

## Eye Styles

```php
use Devxisas\LaravelQrCode\Enums\EyeStyle;

QrCode::eye('square')->generate('...');   // default
QrCode::eye('circle')->generate('...');

// Enum API
QrCode::eye(EyeStyle::Circle)->generate('...');
```

<!-- SCREENSHOT: Square eyes vs Circle eyes -->

---

## Colors

### Foreground & background

```php
QrCode::color(59, 130, 246)->generate('...');
QrCode::color(255, 255, 255)->backgroundColor(15, 23, 42)->generate('...');

// With alpha (0–127, where 127 = fully transparent)
QrCode::color(59, 130, 246, 50)->generate('...');
```

### Per-eye colors

Each of the three finder eyes (0, 1, 2) can have independent inner and outer colors.

```php
QrCode::eyeColor(0, 239, 68, 68)           // eye 0 — red (inner = outer)
      ->eyeColor(1, 34, 197, 94)           // eye 1 — green
      ->eyeColor(2, 59, 130, 246)          // eye 2 — blue
      ->generate('...');

// With separate inner and outer colors
// eyeColor(eye, innerR, innerG, innerB, outerR, outerG, outerB)
QrCode::eyeColor(0, 255, 255, 255, 59, 130, 246)->generate('...');
```

<!-- SCREENSHOT: Color examples — blue, dark bg, custom eye colors -->

---

## Gradients

```php
use Devxisas\LaravelQrCode\Enums\GradientType;

// gradient(startR, startG, startB, endR, endG, endB, type)
QrCode::gradient(59, 130, 246, 168, 85, 247, 'radial')->generate('...');

// Enum API
QrCode::gradient(59, 130, 246, 168, 85, 247, GradientType::Radial)->generate('...');
```

| Type               | Enum                             |
|--------------------|----------------------------------|
| `horizontal`       | `GradientType::Horizontal`       |
| `vertical`         | `GradientType::Vertical`         |
| `diagonal`         | `GradientType::Diagonal`         |
| `inverse_diagonal` | `GradientType::InverseDiagonal`  |
| `radial`           | `GradientType::Radial`           |

<!-- SCREENSHOT: Five gradient types in a row -->

---

## Image Merging (Logo overlay)

Requires PNG format and `ErrorCorrection::High` (`H`) for reliable scanning.

```php
// From a file path
QrCode::format('png')
      ->errorCorrection('H')
      ->merge('/path/to/logo.png', 0.3)
      ->generate('https://devxisas.com');

// From a string (e.g. fetched via HTTP)
QrCode::format('png')
      ->errorCorrection('H')
      ->mergeString(file_get_contents('/path/to/logo.png'), 0.3)
      ->generate('https://devxisas.com');
```

The second argument is the percentage of the QR code the image should occupy (default `0.2`).

<!-- SCREENSHOT: QR code with logo centered -->

---

## Data URI (`toDataUri`)

Generates a base64-encoded data URI — ideal for embedding QR codes in emails, PDFs, or anywhere external URLs are unavailable.

```php
$uri = QrCode::size(200)->toDataUri('https://devxisas.com');
// → "data:image/svg+xml;base64,..."

// PNG
$uri = QrCode::size(200)->format('png')->toDataUri('https://devxisas.com');
// → "data:image/png;base64,..."
```

In Blade:

```blade
<img src="{{ QrCode::size(200)->toDataUri('https://devxisas.com') }}" alt="QR Code">
```

---

## Blade Directive

A `@qrcode` directive is registered automatically.

```blade
@qrcode('https://devxisas.com')

{{-- With format and size --}}
@qrcode('https://devxisas.com', 'svg', 200)

{{-- Using enums --}}
@php use Devxisas\LaravelQrCode\Enums\Format; @endphp
@qrcode('https://devxisas.com', Format::Png, 300)
```

---

## Response Macro

Stream a QR code directly as an HTTP response with the correct `Content-Type` header.

```php
// In a controller
return response()->qrcode('https://devxisas.com');

// With format and size
return response()->qrcode('https://devxisas.com', 'png', 300);

// Using enum
use Devxisas\LaravelQrCode\Enums\Format;
return response()->qrcode('https://devxisas.com', Format::Png, 300);
```

---

## Artisan Command

Generate QR codes from the command line.

```bash
# Print SVG to stdout
php artisan qrcode:generate "https://devxisas.com"

# Save PNG to a file
php artisan qrcode:generate "https://devxisas.com" --format=png --output=public/qr.png

# All options
php artisan qrcode:generate "https://devxisas.com" \
    --format=svg \
    --size=300 \
    --margin=2 \
    --error-correction=H \
    --output=/path/to/output.svg
```

| Option               | Default | Values             |
|----------------------|---------|--------------------|
| `--format`           | `svg`   | `svg`, `eps`, `png`|
| `--size`             | `200`   | 1–4096             |
| `--margin`           | `0`     | integer            |
| `--error-correction` | `M`     | `L`, `M`, `Q`, `H` |
| `--output`           | stdout  | file path          |

---

## Data Types

All data types return an `HtmlString` just like `generate()` and follow the same fluent interface.

### URL / Plain text

```php
QrCode::generate('https://devxisas.com');
QrCode::generate('Plain text content');
```

### Email

```php
QrCode::email('hello@example.com', 'Subject', 'Body text');
```

### Phone number

```php
QrCode::phoneNumber('+50312345678');
```

### SMS

```php
QrCode::sms('+50312345678', 'Message body');
```

### Geo location

```php
QrCode::geo(13.6929, -89.2182);  // latitude, longitude
```

### WiFi

```php
QrCode::wifi([
    'encryption' => 'WPA',       // WPA | WEP | nopass
    'ssid'       => 'NetworkName',
    'password'   => 'secret123',
    'hidden'     => false,        // optional
]);
```

### Bitcoin

```php
QrCode::btc('1A1zP1eP5QGefi2DMPTfTL5SLmv7Divf', '0.001', [
    'label'         => 'Donation',   // optional
    'message'       => 'Thank you',  // optional
    'returnAddress' => 'bc1q...',    // optional
]);
```

### vCard 3.0

```php
QrCode::errorCorrection('H')->vCard([
    'name'    => 'Hernandez;Elmer',   // LastName;FirstName
    'email'   => 'elmer@devxisas.com',
    'phone'   => '+50312345678',
    'org'     => 'Devxisas',
    'title'   => 'Developer',
    'url'     => 'https://devxisas.com',
    'address' => 'San Salvador, El Salvador',  // optional
    'note'    => 'Some note',                   // optional
]);
```

### MeCard (iOS / Android)

```php
QrCode::meCard([
    'name'    => 'Hernandez,Elmer',
    'phone'   => '+50312345678',
    'email'   => 'elmer@devxisas.com',
    'url'     => 'https://devxisas.com',
    'address' => 'San Salvador',   // optional
    'note'    => 'Some note',      // optional
]);
```

### Calendar Event (iCal)

Accepts ISO 8601 strings, Unix timestamps, or any `DateTimeInterface`.

```php
QrCode::calendarEvent([
    'summary'     => 'Laravel Meetup SV',
    'start'       => '2025-06-15 18:00:00',
    'end'         => '2025-06-15 20:00:00',
    'location'    => 'San Salvador, El Salvador',  // optional
    'description' => 'Monthly Laravel meetup',      // optional
    'url'         => 'https://devxisas.com',         // optional
]);

// With Carbon / DateTimeInterface
QrCode::calendarEvent([
    'summary' => 'Meeting',
    'start'   => now()->addDay(),
    'end'     => now()->addDay()->addHours(2),
]);
```

<!-- SCREENSHOT: Grid of all data type QR codes -->

---

## Combining Options

Options are fully composable via fluent chaining:

```php
QrCode::size(250)
      ->format('png')
      ->style('dot', 0.5)
      ->eye('circle')
      ->gradient(59, 130, 246, 99, 102, 241, 'radial')
      ->errorCorrection('H')
      ->generate('https://devxisas.com');
```

```php
QrCode::size(250)
      ->style('round', 0.7)
      ->color(16, 185, 129)
      ->backgroundColor(15, 23, 42)
      ->margin(2)
      ->generate('https://devxisas.com');
```

---

## Enum Reference

All enums are backed enums so they work alongside their string equivalents.

```php
use Devxisas\LaravelQrCode\Enums\Format;
use Devxisas\LaravelQrCode\Enums\Style;
use Devxisas\LaravelQrCode\Enums\EyeStyle;
use Devxisas\LaravelQrCode\Enums\ErrorCorrection;
use Devxisas\LaravelQrCode\Enums\GradientType;

Format::Svg          // 'svg'
Format::Eps          // 'eps'
Format::Png          // 'png'

Style::Square        // 'square'
Style::Dot           // 'dot'
Style::Round         // 'round'

EyeStyle::Square     // 'square'
EyeStyle::Circle     // 'circle'

ErrorCorrection::Low       // 'L'
ErrorCorrection::Medium    // 'M'
ErrorCorrection::Quartile  // 'Q'
ErrorCorrection::High      // 'H'

GradientType::Horizontal       // 'horizontal'
GradientType::Vertical         // 'vertical'
GradientType::Diagonal         // 'diagonal'
GradientType::InverseDiagonal  // 'inverse_diagonal'
GradientType::Radial           // 'radial'
```

---

## Testing

```bash
composer test           # all tests
composer test:unit      # unit tests only
composer analyse        # PHPStan static analysis
composer format         # Pint code style fix
composer format:check   # Pint check only
```

---

## Migrating from `simplesoftwareio/simple-qrcode`

This package was built as a modernized continuation of `simple-qrcode`. The core fluent API is identical, so most projects require only minor changes.

### Installation

```bash
composer remove simplesoftwareio/simple-qrcode
composer require devxisas/laravel-qrcode
```

### Facade

```php
// Before
use SimpleSoftwareIO\QrCode\Facades\QrCode;

// After
use Devxisas\LaravelQrCode\Facades\QrCode;
```

That's usually the only change needed. All existing method calls (`generate`, `size`, `format`, `color`, `style`, `eye`, `gradient`, `merge`, etc.) work identically.

### What's new

| Feature | simple-qrcode | laravel-qrcode |
|---------|:---:|:---:|
| PHP 8.2+ strict types | — | ✓ |
| Backed enums for all options | — | ✓ |
| `toDataUri()` | — | ✓ |
| vCard 3.0 data type | — | ✓ |
| MeCard data type | — | ✓ |
| Calendar Event data type | — | ✓ |
| `@qrcode` Blade directive | — | ✓ |
| `response()->qrcode()` macro | — | ✓ |
| Artisan `qrcode:generate` command | — | ✓ |
| Publishable config file | — | ✓ |
| Automatic state reset after `generate()` | — | ✓ |
| Laravel 11 / 12 support | ✓ | ✓ |
| SVG / EPS / PNG formats | ✓ | ✓ |
| Image merging (logo overlay) | ✓ | ✓ |
| Colors, gradients, eye colors | ✓ | ✓ |
| Email, Phone, SMS, Geo, WiFi, BTC | ✓ | ✓ |

---

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md).

## Security

See [SECURITY.md](SECURITY.md).

## License

MIT — see [LICENSE](LICENSE).
