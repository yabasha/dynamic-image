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
<!-- Use a custom disk: -->
<img src="{{ dynamic_image(null, true, null, 's3') }}" alt="Art from S3">
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

// Get an image from a different disk (e.g., S3):
$imageUrl = dynamic_image(null, true, null, 's3');
// https://your-s3-bucket-url/art/Art01.avif
```

### Overriding the Storage Disk

By default, `dynamic_image()` uses the disk specified in your config (`'disk' => 'public'`).
You can override this at runtime by passing a disk name as the fourth parameter:

```php
dynamic_image(null, true, null, 's3'); // Use the 's3' disk
dynamic_image('timed', false, null, 'local'); // Use the 'local' disk
```

This allows you to fetch images from any Laravel filesystem disk configured in your app (e.g., `public`, `s3`, `local`, etc.).

If no disk is provided, the helper will fall back to the value in your `config/dynamicimage.php` file.


### The `dynamic_image()` Helper Explained

This global function returns a path or URL to an image, selected according to your configuration and options.

**Signature:**
```php
dynamic_image($mode = null, $asUrl = true, $options = null, $disk = null)
```

| Parameter | Type        | Default | Meaning/Effect                                                                   |
|-----------|-------------|---------|---------------------------------------------------------------------------------|
| `$mode`   | string/null | null    | `'random'`, `'timed'`, or `null` for config default. Controls image selection.   |
| `$asUrl`  | bool        | true    | If true, returns asset() URL. If false, returns relative path.                   |
| `$options`| string/null | null    | If set, inserts options after the first folder in the path or URL.               |
| `$disk`   | string/null | null    | Filesystem disk to use (e.g., `'public'`, `'s3'`). Overrides config if set.      |

**Parameter Details:**
- **`$mode`**: Controls how the image is selected.
  - `'random'`: Picks a random image from the configured folders.
  - `'timed'`: Picks an image based on a time interval (rotates images every X minutes).
  - `null`: Uses the default mode set in your config file (usually `'random'`).
- **`$disk`**: The Laravel filesystem disk to use. If not provided, uses the disk from your configuration. Useful for serving images from S3, local, or other disks at runtime.

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

// Use the 's3' disk
$imageUrl = dynamic_image(null, true, null, 's3');

// Use the 'local' disk for timed mode
$imageUrl = dynamic_image('timed', false, null, 'local');
```

## Configuration

- `folders`: Array of folders (relative to storage_path) to scan for images.
- `extensions`: Allowed file extensions.
- `interval_minutes`: For timed rotation mode.
- `mode`: 'random' or 'timed'.
- `default_image`: Path to default image if no images found.
- `disk`: Filesystem disk to use for image storage (e.g., `'public'`, `'s3'`).

## Example config/dynamicimage.php
```php
return [
    'folders' => [
        'app/public/art', // relative to storage_path()
        'app/public/other-art',
    ],
    'extensions' => ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'],
    'interval_minutes' => 10,
    'mode' => 'random', // or 'timed'
    'default_image' => null, // e.g. 'app/public/default.jpg'
    'disk' => 'public', // specify the filesystem disk (e.g., 'public', 's3', 'local')
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
