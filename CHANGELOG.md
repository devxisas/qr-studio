# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- `EyeStyle::Pointy` — new eye style from BaconQrCode 3.x (curved outer corner + circle inner)
- `GDLibRenderer` fallback for PNG when `ext-imagick` is not installed

### Fixed
- Replaced deprecated `Encoder::DEFAULT_BYTE_MODE_ECODING` (typo) with `Encoder::DEFAULT_BYTE_MODE_ENCODING`
- `getWriter()` now accepts `RendererInterface` instead of the concrete `ImageRenderer`

## [1.0.0] - 2026-03-17

### Added
- QR code generator with fluent chainable API via `QrCode` facade
- SVG, EPS, and PNG output formats (`Format` enum)
- Module styles: square, dot, round (`Style` enum) with configurable size parameter
- Eye styles: square, circle (`EyeStyle` enum)
- Gradient support with 5 types: horizontal, vertical, diagonal, inverse_diagonal, radial (`GradientType` enum)
- Error correction levels L / M / Q / H (`ErrorCorrection` enum)
- Foreground, background, and per-eye color customization
- Image merging (logo overlay) via `merge()` and `mergeString()` — PNG only
- `toDataUri()` for base64 data URI output (ideal for emails and PDFs)
- Data types: Email, PhoneNumber, SMS, Geo, WiFi, BTC, VCard 3.0, MeCard, CalendarEvent (iCal)
- `@qrcode` Blade directive with optional format and size parameters
- `response()->qrcode()` macro for HTTP streaming with correct `Content-Type`
- `php artisan qrcode:generate` Artisan command with `--format`, `--size`, `--margin`, `--error-correction`, `--output` options
- Publishable `config/laravel-qrcode.php` for package-wide defaults
- Automatic state reset after every `generate()` call
- Laravel 11 and 12 support, PHP 8.2+
- Full PHPStan level analysis and Pint code style enforcement
- Pest unit test suite

[Unreleased]: https://github.com/devxisas/laravel-qrcode/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/devxisas/laravel-qrcode/releases/tag/v1.0.0
