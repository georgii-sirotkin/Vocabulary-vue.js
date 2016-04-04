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

    /**
     * Minimum number of characters per one spelling mistake (levenshtein distance of 1) to consider answer correct.
     */
    'min_number_of_chars_per_one_mistake' => 6,

    /**
     * Minimum number of characters per one spelling mistake (levenshtein distance of 1) to consider a word similar when searching.
     */
    'min_number_of_chars_per_one_mistake_in_search' => 4,
];
