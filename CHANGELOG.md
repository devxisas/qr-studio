# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## 1.0.0 (2026-03-18)


### Features

* add artisan command for QR code generation ([a51d20d](https://github.com/devxisas/qr-studio/commit/a51d20daaa4a43d57af3e7b6a3732e257df74f3d))
* add core QR code generator with image handling ([732ae43](https://github.com/devxisas/qr-studio/commit/732ae4360ed2093849ad94bec8c14a9e155c03f2))
* add data types for QR code content encoding ([6b36f3c](https://github.com/devxisas/qr-studio/commit/6b36f3c103a147c216933ed5fdcec0cacb2a26d0))
* add GD fallback for PNG and PointyEye support from BaconQrCode 3.x ([3888f9f](https://github.com/devxisas/qr-studio/commit/3888f9f8781e5f7b7402d6e15d5fe0032f69680b))
* add Laravel 13 / orchestra testbench 11 support ([05404b2](https://github.com/devxisas/qr-studio/commit/05404b24f91c82bb2d182759aff81dd6cb191b7c))
* add package configuration file ([9e0c7b0](https://github.com/devxisas/qr-studio/commit/9e0c7b0046569fa7103296a40eb79bae4719c092))
* add QR code enums for format, style, and error correction ([278768a](https://github.com/devxisas/qr-studio/commit/278768a28e139a3005213cd7c64e637aedc82645))
* add WhatsApp data type, saveToDisk(), themes, and config expansion ([fb0f9ef](https://github.com/devxisas/qr-studio/commit/fb0f9eff2639f6a5d0231bd6fda5d3140a9c1d49))
* redesign HasQrCode trait with qrCodeType()/qrCodeData() API and update README ([02b61c5](https://github.com/devxisas/qr-studio/commit/02b61c546ba4cb69e48495b75d12e95cd595633c))
* register Laravel service provider and QrCode facade ([a04703f](https://github.com/devxisas/qr-studio/commit/a04703ffaeb4fb38a80c57bae150e036ad120860))
* rename package to devxisas/qr-studio and add HasQrCode trait ([aea8035](https://github.com/devxisas/qr-studio/commit/aea8035250995daf9ab92d81c5a9910d69263641))
* respect config defaults in Blade directive, response macro, and encoding ([3ee8b33](https://github.com/devxisas/qr-studio/commit/3ee8b33e2472813f09198ee5366b4ba6886ee8b0))


### Bug Fixes

* [@qrcode](https://github.com/qrcode) directive wraps PNG output in &lt;img data-uri&gt; instead of echoing raw binary ([a1c0e44](https://github.com/devxisas/qr-studio/commit/a1c0e443463b3b03a21fd6c7d29d3a5170124fcc))
* **bacon:** replace deprecated DEFAULT_BYTE_MODE_ECODING with DEFAULT_BYTE_MODE_ENCODING ([92ee441](https://github.com/devxisas/qr-studio/commit/92ee441fe64c7089d5cb3617cd68a9a7376efa86))
* **ci:** drop Laravel 10 support (EOL) and update illuminate/contracts constraint ([fb167bf](https://github.com/devxisas/qr-studio/commit/fb167bf756e0d046db864b848fa53fab53bd57fc))
* **ci:** remove Laravel 13 until pest-plugin-laravel adds support ([612e6aa](https://github.com/devxisas/qr-studio/commit/612e6aaf17b053cbdb7099ba6a356f284c6c9a6f))
* **deps:** allow larastan ^3.0 to support Laravel 12 ([7f05f6a](https://github.com/devxisas/qr-studio/commit/7f05f6a7b758ed164eea74b4785b80efb5a40270))
* **deps:** upgrade phpstan ecosystem to v2 and add Laravel 13 support ([c19c297](https://github.com/devxisas/qr-studio/commit/c19c29761bde1fd3ea886342030919f0aaf18c9d))
* don't prepend base_path() when merge() receives an absolute path ([4cf63a4](https://github.com/devxisas/qr-studio/commit/4cf63a4cfa8dbb5b122a076d16be79e885e740d3))
* **mecard:** do not escape colons in URL field — prevents https:// becoming https\:// ([0b6bff7](https://github.com/devxisas/qr-studio/commit/0b6bff7fb73a91ca50eeb0d2a6b92926e9e7f81f))
* **tests:** remove missing Feature test directory from phpunit.xml ([f05f0d6](https://github.com/devxisas/qr-studio/commit/f05f0d677b7b832d089758ec0cdb4ff90833a1ca))


### CI/CD

* add GitHub Actions workflows for tests and code style ([1dce619](https://github.com/devxisas/qr-studio/commit/1dce619ae8f4c880782382fef6096b0b057ccf76))
* point release-please to config and manifest files ([a109089](https://github.com/devxisas/qr-studio/commit/a1090899fe41e3f9959d232ee509061d9ce90019))

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
