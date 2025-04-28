<?php

use Yabasha\DynamicImage\Helpers\DynamicImageHelper;

if (!function_exists('dynamic_image')) {
    /**
     * Get a dynamic image path or URL.
     *
     * @param string|null $mode 'random' or 'timed'
     * @param bool $asUrl If true (default), returns a URL. If false, returns the relative path.
     * @return string|null
     */
    /**
     * Get a dynamic image path or URL.
     *
     * @param string|null $mode 'random' or 'timed'
     * @param bool $asUrl If true (default), returns a URL. If false, returns the relative path.
     * @param string|null $options If set and $asUrl is false, inserts options after the top folder (e.g., images/OPTIONS/...)
     * @return string|null
     */
    function dynamic_image($mode = null, $asUrl = true, $options = null)
    {
        $config = config('dynamicimage');
        $helper = new DynamicImageHelper(
            $config['folders'] ?? [],
            $config['extensions'] ?? [],
            $config['default_image'] ?? null
        );
        $mode = $mode ?: ($config['mode'] ?? 'random');
        if ($mode === 'timed') {
            $path = $helper->timedImage($config['interval_minutes'] ?? 10, null, $asUrl);
        } else {
            $path = $helper->randomImage($asUrl);
        }
        // Insert options if needed
        if ($options && is_string($path)) {
            // Insert after the first folder segment (e.g., images/OPTIONS/rest/of/path)
            $path = preg_replace('#^([^/]+/)#', '$1' . $options . '/', $path, 1);
        }
        // If $asUrl is true, return as asset() URL
        if ($asUrl && is_string($path)) {
            return asset($path);
        }
        return $path;
    }
}

