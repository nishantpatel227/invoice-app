<?php

return [
    'node_binary_path'   => env('PDF_NODE_BINARY_PATH', '/usr/bin/node'),
    'npm_binary_path'    => env('PDF_NPM_BINARY_PATH', '/usr/bin/npm'),
    'chrome_binary_path' => env('PDF_CHROME_BINARY_PATH', '/usr/bin/chromium-browser'),
    'temp_path'          => env('PDF_TEMP_PATH', storage_path('app/temp')),

    'pdf' => [
        'options' => [
            'args' => ['--no-sandbox'],
        ],
    ],
];

