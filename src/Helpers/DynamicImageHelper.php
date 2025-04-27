<?php
namespace Yabasha\DynamicImage\Helpers;

use Illuminate\Support\Facades\Storage;

class DynamicImageHelper
{
    protected $folders;
    protected $extensions;
    protected $defaultImage;

    public function __construct(array $folders, array $extensions, $defaultImage = null)
    {
        $this->folders = $folders;
        $this->extensions = $extensions;
        $this->defaultImage = $defaultImage;
    }

    protected function getAllImages()
    {
        $images = [];
        foreach ($this->folders as $folder) {
            $files = Storage::files($folder);
            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, $this->extensions)) {
                    $images[] = $file;
                }
            }
        }
        return $images;
    }

    public function randomImage()
    {
        $images = $this->getAllImages();
        if (empty($images)) {
            return $this->defaultImage ? Storage::url($this->defaultImage) : null;
        }
        $randomImage = $images[array_rand($images)];
        return Storage::url($randomImage);
    }

    public function timedImage($intervalMinutes = 10, $now = null)
    {
        $images = $this->getAllImages();
        if (empty($images)) {
            return $this->defaultImage ? Storage::url($this->defaultImage) : null;
        }
        $now = $now ?: now();
        $minutes = floor($now->timestamp / ($intervalMinutes * 60));
        $index = $minutes % count($images);
        return Storage::url($images[$index]);
    }
}
