<?php

use Yabasha\DynamicImage\Helpers\DynamicImageHelper;
use Yabasha\DynamicImage\Helpers\UrlRootHelper; // Import the URL root helper

if (!function_exists('dynamic_image')) {
    /**
     * Get a dynamic image path or URL.
     *
     * This function will always prepend the disk's URL root (e.g., 'media/', 'storage/') to the returned path, using UrlRootHelper.
     * The $options parameter, if set, will be inserted after the disk root (e.g., 'media/OPTIONS/...').
     *
     * @param string|null $mode 'random' or 'timed'. If null, uses config default.
     * @param bool $asUrl If true (default), returns a full URL. If false, returns the relative path (including disk root).
     * @param string|null $options If set, inserts options after the disk root (e.g., media/OPTIONS/... or in the URL path).
     * @param string|null $disk Filesystem disk to use (e.g., 'media', 'public'). If null, uses config default.
     * @return string|null The image path or URL, with disk root and options as specified.
     *
     * @example // Get a random image URL from the 'media' disk
     *   dynamic_image('random', true, null, 'media');
     *   // Output: https://yourdomain.com/media/art/Art02.avif
     *
     * @example // Get a random image relative path from the 'media' disk
     *   dynamic_image('random', false, null, 'media');
     *   // Output: media/art/Art02.avif
     *
     * @example // Get a random image relative path with options inserted
     *   dynamic_image('random', false, 'compress,width=1200,flip=horizontal,invert', 'media');
     *   // Output: media/compress,width=1200,flip=horizontal,invert/art/Art02.avif
     *
     * @example // Get a random image URL with options inserted
     *   dynamic_image('random', true, 'compress,width=1200,flip=horizontal,invert', 'media');
     *   // Output: https://yourdomain.com/media/compress,width=1200,flip=horizontal,invert/art/Art02.avif
     *
     * @example // Using the default disk (e.g., 'public')
     *   dynamic_image('random', false, null);
     *   // Output: storage/art/Art02.avif
     */
    function dynamic_image(?string $mode = null, bool $asUrl = true, ?string $options = null, $disk = null): ?string
    {
        $config = config('dynamicimage');
        // Determine which disk to use (passed in or from config)
        $resolvedDisk = $disk ?: ($config['disk'] ?? 'public');
        $helper = new DynamicImageHelper(
            $config['folders'] ?? [],
            $config['extensions'] ?? [],
            $config['default_image'] ?? null,
            $resolvedDisk
        );
        $mode = $mode ?: ($config['mode'] ?? 'random');
        if ($mode === 'timed') {
            $path = $helper->timedImage($config['interval_minutes'] ?? 10, null, $asUrl);
        } else {
            $path = $helper->randomImage($asUrl);
        }
        // Get the disk's relative URL root (e.g., 'media/', 'storage/')
        $diskRoot = UrlRootHelper::getUrlRootPath($resolvedDisk);
        // Ensure disk root is included in relative path
        if (!$asUrl && is_string($path)) {
            if ($diskRoot && strpos($path, $diskRoot) !== 0) {
                $path = rtrim($diskRoot, '/') . '/' . ltrim($path, '/');
            }
        }
        if ($options && is_string($path)) {
            // If absolute URL, parse and insert options after disk root in the path
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                $parts = parse_url($path);
                if (isset($parts['path'])) {
                    // Insert options after disk root
                    $pattern = '#^(/?' . preg_quote(rtrim($diskRoot, '/'), '#') . '/)#';
                    $parts['path'] = preg_replace($pattern, '$1' . $options . '/', $parts['path'], 1);
                    // Rebuild URL
                    $rebuilt = $parts['scheme'] . '://' . $parts['host'];
                    if (isset($parts['port'])) $rebuilt .= ':' . $parts['port'];
                    $rebuilt .= $parts['path'];
                    if (isset($parts['query'])) $rebuilt .= '?' . $parts['query'];
                    if (isset($parts['fragment'])) $rebuilt .= '#' . $parts['fragment'];
                    $path = $rebuilt;
                }
            } else {
                // Relative path: insert after disk root
                $pattern = '#^(' . preg_quote(rtrim($diskRoot, '/'), '#') . '/)#';
                $path = preg_replace($pattern, '$1' . $options . '/', $path, 1);
            }
        }
        return $path;
    }
}

