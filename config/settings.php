<?php

return [
    'authentication_services' => [
        'facebook',
        'google',
    ],
    'image' => [
        'max_width' => 400,
        'max_height' => 300,
        'max_filesize' => 300,
        'mime_types' => [
            'jpeg',
            'png',
            'gif',
        ],
        'folder' => env('IMAGES_FOR_WORDS_FOLDER'),
    ],

    /**
     * The number of most recent words (words that were recently returned by getNewRandomWord method) to remember.
     * When we fetch a new random word from the database, we will make these words unlikely to be found.
     */
    'number_of_words_to_remember' => 5,
];
