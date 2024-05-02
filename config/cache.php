<?php

/**
 *
 */
return [

    /**
     *
     */
    'default' => 'file',

    /**
     *
     */
    'stores' => [
        /**
         *
         */
        'file' => [
            'driver' => 'file',
            'path' => storage_path('cache/'),
            'lock_path' => storage_path('cache/'),
        ],

        /**
         *
         */
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],
    ]
];
