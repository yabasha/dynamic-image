<?php
namespace Yabasha\DynamicImage;

use Illuminate\Support\ServiceProvider;
use Yabasha\DynamicImage\Helpers\DynamicImageHelper;

class DynamicImageServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/dynamicimage.php' => config_path('dynamicimage.php'),
        ], 'config');

        // Register global helper
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
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/dynamicimage.php', 'dynamicimage'
        );
    }
}
