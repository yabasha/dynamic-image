<?php
return [
    'folders' => [
        'app/public/art', // relative to storage_path()
    ],
    'extensions' => ['jpg', 'jpeg', 'png', 'webp', 'avif', 'gif'],
    'interval_minutes' => 10,
    'mode' => 'random', // or 'timed'
    'default_image' => null, // e.g. 'app/public/default.jpg'
    'disk' => 'public', // specify the filesystem disk used by dynamic_image
];
