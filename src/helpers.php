<?php

use Yabasha\DynamicImage\Helpers\DynamicImageHelper;

if (!function_exists('dynamic_image')) {
    /**
     * Get a dynamic image path or URL.
     *
     * @param string|null $mode 'random' or 'timed'
     * @param bool $asUrl If true (default), returns a URL. If false, returns the relative path.
     * @param string|null $options If set and $asUrl is false, inserts options after the top folder (e.g., images/OPTIONS/...)
     * @param null $disk
     * @return string|null
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
        if ($options && is_string($path)) {
            // If absolute URL, parse and insert options after disk root in the path
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                $parts = parse_url($path);
                if (isset($parts['path'])) {
                    // Insert options after disk root
                    $diskRoot = trim($resolvedDisk, '/');
                    $pattern = '#^(/?' . preg_quote($diskRoot, '#') . '/)#';
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
                // Relative path: insert after first segment
                $path = preg_replace('#^([^/]+/)#', '$1' . $options . '/', $path, 1);
            }
        }
        return $path;
    }
}

