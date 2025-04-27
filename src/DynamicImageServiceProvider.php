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


    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/dynamicimage.php', 'dynamicimage'
        );
    }
}
