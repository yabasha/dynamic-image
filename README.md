# DynamicImage Laravel Package

Easily display dynamic images on your login page from one or more folders in storage. Supports random or timed rotation modes.

## Installation

1. Add the package to your `composer.json` (path repository) and run `composer require yabasha/dynamicimage`.
2. Publish the config:
   ```bash
   php artisan vendor:publish --provider="Yabasha\\DynamicImage\\DynamicImageServiceProvider" --tag=config
   ```
3. Configure `config/dynamicimage.php` with your folders and settings.

## Usage

In your Blade view:
```blade
<img src="{{ dynamic_image() }}" alt="Art">
<img src="{{ dynamic_image('timed') }}" alt="Timed Art">
```

Or in PHP:
```php
$image = dynamic_image(); // random or timed based on config
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
