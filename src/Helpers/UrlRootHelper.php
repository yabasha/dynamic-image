<?php
namespace Yabasha\DynamicImage\Helpers;

use Illuminate\Support\Facades\Storage;

class UrlRootHelper
{
    /**
     * Get the URL root path (without domain/host) for a given disk.
     * For example, Storage::disk('media')->url('') => 'https://site.com/media/' -> returns 'media/'
     *
     * @param string $disk
     * @return string|null
     */
    public static function getUrlRootPath(string $disk): ?string
    {
        $url = Storage::disk($disk)->url('');
        if (!$url) return null;
        $parts = parse_url($url);
        // If path is present, trim leading slash
        if (isset($parts['path'])) {
            return ltrim($parts['path'], '/');
        }
        return null;
    }
}
