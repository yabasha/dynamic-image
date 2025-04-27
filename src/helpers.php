<?php

use Yabasha\DynamicImage\Helpers\DynamicImageHelper;

if (!function_exists('dynamic_image')) {
    function dynamic_image($mode = null)
    {
        $config = config('dynamicimage');
        $helper = new DynamicImageHelper(
            $config['folders'] ?? [],
            $config['extensions'] ?? [],
            $config['default_image'] ?? null
        );
        $mode = $mode ?: ($config['mode'] ?? 'random');
        if ($mode === 'timed') {
            return $helper->timedImage($config['interval_minutes'] ?? 10);
        }
        return $helper->randomImage();
    }
}
