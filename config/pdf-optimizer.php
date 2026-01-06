<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ghostscript Binary Path
    |--------------------------------------------------------------------------
    |
    | This package relies on Ghostscript to optimize PDF files.
    | You can specify the path to your Ghostscript executable here.
    |
    | Default: gs
    |
    */

    'gs' => env('PDF_OPTIMIZER_GS', 'gswin64c'),

    /*
    |--------------------------------------------------------------------------
    | Process Timeout
    |--------------------------------------------------------------------------
    |
    | Set timeout to control how long the process should run.
    | If the timeout is reached, a ProcessTimedOutException will be thrown.
    |
    | Default: 300 seconds (5 minutes)
    |
    */

    'timeout' => 300,

    /*
    |--------------------------------------------------------------------------
    | Queue
    |--------------------------------------------------------------------------
    |
    | Sometimes optimizing process is very heavy, so you have to queue the
    | process and do it in background.
    |
    */

    'queue' => [
        'enabled' => false,
        'name' => 'default',
        'connection' => null,
        'timeout' => 900, // seconds (15 minutes)
    ],
];
