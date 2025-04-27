<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Yabasha\DynamicImage\Helpers\DynamicImageHelper;

class DynamicImageHelperTest extends TestCase
{
    public function testReturnsNullIfNoImagesAndNoDefault()
    {
        $helper = new DynamicImageHelper([], [], null);
        $this->assertNull($helper->randomImage());
        $this->assertNull($helper->timedImage());
    }

    public function testReturnsDefaultIfNoImagesButDefaultSet()
    {
        $helper = new DynamicImageHelper([], [], 'default.jpg');
        // Storage::url() is not mocked, so we just check the string for now
        $this->assertEquals('default.jpg', $helper->randomImage());
        $this->assertEquals('default.jpg', $helper->timedImage());
    }

    // More tests would be added with a proper Laravel test environment
}
