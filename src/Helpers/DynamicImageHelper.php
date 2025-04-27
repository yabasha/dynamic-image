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

    /**
     * Get a random image.
     * @param bool $asUrl If true (default), return a URL. If false, return the relative path.
     * @return string|null
     */
    public function randomImage($asUrl = true)
    {
        $images = $this->getAllImages();
        if (empty($images)) {
            if ($this->defaultImage) {
                return $asUrl ? Storage::url($this->defaultImage) : $this->defaultImage;
            }
            return null;
        }
        $randomImage = $images[array_rand($images)];
        return $asUrl ? Storage::url($randomImage) : $randomImage;
    }

    /**
     * Get an image based on time interval.
     * @param int $intervalMinutes
     * @param \DateTimeInterface|null $now
     * @param bool $asUrl If true (default), return a URL. If false, return the relative path.
     * @return string|null
     */
    public function timedImage($intervalMinutes = 10, $now = null, $asUrl = true)
    {
        $images = $this->getAllImages();
        if (empty($images)) {
            if ($this->defaultImage) {
                return $asUrl ? Storage::url($this->defaultImage) : $this->defaultImage;
            }
            return null;
        }
        $now = $now ?: now();
        $minutes = floor($now->timestamp / ($intervalMinutes * 60));
        $index = $minutes % count($images);
        return $asUrl ? Storage::url($images[$index]) : $images[$index];
    }
}
