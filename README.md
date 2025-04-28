# DynamicImage Laravel Package

Easily display dynamic images on your login page from one or more folders in storage. Supports random or timed rotation modes.

## Installation

1. Add the package to your `composer.json` (path repository) and run `composer require yabasha/dynamic-image`.
2. **Publish the config file to your Laravel app:**
   ```bash
   php artisan vendor:publish --provider="Yabasha\\DynamicImage\\DynamicImageServiceProvider" --tag=config
   ```
   This copies the config file from `vendor/yabasha/dynamic-image/config/dynamicimage.php` to your application's `config/dynamicimage.php`.
3. Configure `config/dynamicimage.php` with your folders and settings.

## Usage

In your Blade view:
```blade
<img src="{{ dynamic_image() }}" alt="Art">
<img src="{{ dynamic_image('timed') }}" alt="Timed Art">
<!-- With options (URL): -->
<img src="{{ dynamic_image(null, true, 'compress,width=1200,blur=5') }}" alt="Custom Art">
```

Or in PHP:
```php
// Get asset-style URL (default):
$imageUrl = dynamic_image(); // https://your-app.test/images/art/Art01.avif

// Get asset-style URL with options inserted after the main folder:
$imageUrl = dynamic_image(null, true, 'compress,width=1200,blur=5');
// https://your-app.test/images/compress,width=1200,blur=5/art/Art01.avif

// Get relative path:
$relative = dynamic_image(null, false); // images/art/Art01.avif

// Get relative path with options:
$relative = dynamic_image(null, false, 'compress,width=1200,blur=5');
// images/compress,width=1200,blur=5/art/Art01.avif
```

### The `dynamic_image()` Helper Explained

This global function returns a path or URL to an image, selected according to your configuration and options.

**Signature:**
```php
dynamic_image($mode = null, $asUrl = true, $options = null)
```

| Parameter | Type        | Default | Meaning/Effect                                                                   |
|-----------|-------------|---------|---------------------------------------------------------------------------------|
| `$mode`   | string/null | null    | `'random'`, `'timed'`, or `null` for config default. Controls image selection.   |
| `$asUrl`  | bool        | true    | If true, returns asset() URL. If false, returns relative path.                   |
| `$options`| string/null | null    | If set, inserts options after the first folder in the path or URL.               |

**Parameter Details:**
- **`$mode`**: Controls how the image is selected.
  - `'random'`: Picks a random image from the configured folders.
  - `'timed'`: Picks an image based on a time interval (rotates images every X minutes).
  - `null`: Uses the default mode set in your config file (usually `'random'`).
- **`$asUrl`**: If `true` (default), returns a full asset() URL (e.g., `https://your-app.test/images/art/Art01.avif`). If `false`, returns just the relative path (e.g., `images/art/Art01.avif`).
- **`$options`**: If provided, inserts this string after the first folder in the path (e.g., `images/compress,width=1200/art/Art01.avif`). Useful for image processing/CDN tools.

**Examples:**
```php
// Default usage: asset URL, random image
dynamic_image();
// → https://your-app.test/images/art/Art01.avif

// Timed mode, asset URL
dynamic_image('timed');
// → https://your-app.test/images/art/Art02.avif (rotates by time)

// Relative path, random image
dynamic_image(null, false);
// → images/art/Art01.avif

// Relative path with options
dynamic_image(null, false, 'compress,width=1200');
// → images/compress,width=1200/art/Art01.avif

// Asset URL with options
dynamic_image(null, true, 'compress,width=1200');
// → https://your-app.test/images/compress,width=1200/art/Art01.avif
```

## Configuration

- `folders`: Array of folders (relative to storage_path) to scan for images.
- `extensions`: Allowed file extensions.
- `interval_minutes`: For timed rotation mode.
- `mode`: 'random' or 'timed'.
- `default_image`: Path to default image if no images found.

## Example config/dynamicimage.php
```php
return [
    'folders' => [
        'app/public/art',
        'app/public/other-art',
    ],
    'extensions' => ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'],
    'interval_minutes' => 10,
    'mode' => 'random',
    'default_image' => null,
];
```

---

## Testing

To run tests:

```bash
composer require --dev phpunit/phpunit
./vendor/bin/phpunit tests
```

---

## License

MIT
