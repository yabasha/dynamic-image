<?php
namespace Yabasha\DynamicImage\Helpers;

use DateTimeInterface;
use Illuminate\Support\Facades\Storage;

class DynamicImageHelper
{
    protected array $folders;
    protected array $extensions;
    protected $defaultImage;
    protected $disk;

    public function __construct(array $folders, array $extensions, $defaultImage = null, $disk = 'public')
    {
        $this->folders = $folders;
        $this->extensions = $extensions;
        $this->defaultImage = $defaultImage;
        $this->disk = $disk ?: 'public';
    }

    protected function getAllImages(): array
    {
        $images = [];
        $storage = $this->getStorage();
        foreach ($this->folders as $folder) {
            $files = $storage->files($folder);
            foreach ($files as $file) {
                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (in_array($ext, $this->extensions)) {
                    $images[] = $file;
                }
            }
        }
        return $images;
    }

    protected function getStorage()
    {
        return \Illuminate\Support\Facades\Storage::disk($this->disk);
    }

    /**
     * Get a random image.
     * @param bool $asUrl If true (default), return a URL. If false, return the relative path.
     * @return string|null
     */
    public function randomImage(bool $asUrl = true): ?string
    {
        $images = $this->getAllImages();
        if (empty($images)) {
            if ($this->defaultImage) {
                return $asUrl ? $this->getStorage()->url($this->defaultImage) : $this->defaultImage;
            }
            return null;
        }
        $randomImage = $images[array_rand($images)];
        return $asUrl ? $this->getStorage()->url($randomImage) : $randomImage;
    }

    /**
     * Get an image based on time interval.
     * @param int $intervalMinutes
     * @param DateTimeInterface|null $now
     * @param bool $asUrl If true (default), return a URL. If false, return the relative path.
     * @return string|null
     */
    public function timedImage(int $intervalMinutes = 10, DateTimeInterface $now = null, bool $asUrl = true): ?string
    {
        $images = $this->getAllImages();
        if (empty($images)) {
            if ($this->defaultImage) {
                return $asUrl ? $this->getStorage()->url($this->defaultImage) : $this->defaultImage;
            }
            return null;
        }
        $now = $now ?: now();
        $minutes = floor($now->timestamp / ($intervalMinutes * 60));
        $index = $minutes % count($images);
        return $asUrl ? $this->getStorage()->url($images[$index]) : $images[$index];
    }
}
