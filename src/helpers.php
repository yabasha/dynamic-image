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
    function dynamic_image($mode = null, $asUrl = true)
    {
        $config = config('dynamicimage');
        $helper = new DynamicImageHelper(
            $config['folders'] ?? [],
            $config['extensions'] ?? [],
            $config['default_image'] ?? null
        );
        $mode = $mode ?: ($config['mode'] ?? 'random');
        if ($mode === 'timed') {
            return $helper->timedImage($config['interval_minutes'] ?? 10, null, $asUrl);
        }
        return $helper->randomImage($asUrl);
    }
}

