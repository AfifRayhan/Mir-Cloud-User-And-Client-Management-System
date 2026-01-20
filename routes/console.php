<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('db:backup', function () {
    $this->info('Starting database backup...');

    $connection = config('database.default');
    $driver = config("database.connections.{$connection}.driver");
    $database = config("database.connections.{$connection}.database");
    
    $backupPath = 'backups';
    if (!Storage::exists($backupPath)) {
        Storage::makeDirectory($backupPath);
    }

    $timestamp = date('Y-m-d-H-i-s');
    $filename = "backup-{$timestamp}";

    if ($driver === 'sqlite') {
        $filename .= '.sqlite';
        if (file_exists($database)) {
            Storage::putFileAs($backupPath, new \Illuminate\Http\File($database), $filename);
            $this->info("Backup created: {$filename}");
        } else {
            $this->error("Database file not found: {$database}");
            return;
        }
    } elseif ($driver === 'mysql') {
        $filename .= '.sql';
        $host = config("database.connections.{$connection}.host");
        $port = config("database.connections.{$connection}.port");
        $username = config("database.connections.{$connection}.username");
        $password = config("database.connections.{$connection}.password");
        
        $path = storage_path("app/private/{$backupPath}/{$filename}");
        
        // Ensure directory exists specifically for the file path if using storage_path
        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        // Use array for arguments to avoid shell injection, but mysqldump > file requires shell
        // We construct the command carefully
        $dumpBinary = env('DB_DUMP_PATH', 'mysqldump');
        $dumpCommand = escapeshellarg($dumpBinary) . " --user=" . escapeshellarg($username) . 
                       " --password=" . escapeshellarg($password) . 
                       " --host=" . escapeshellarg($host) . 
                       " --port=" . escapeshellarg($port) . 
                       " " . escapeshellarg($database) . 
                       " > " . escapeshellarg($path);

        // Hide password in log
        $this->info("Running backup for MySQL database...");

        $returnVar = null;
        $output = [];
        // 2>&1 to capture errors
        exec($dumpCommand . " 2>&1", $output, $returnVar);

        if ($returnVar === 0) {
             $this->info("Backup created: {$filename}");
        } else {
             $this->error("Backup failed with exit code: {$returnVar}");
             $this->error(implode("\n", $output));
             return;
        }
    } else {
        $this->error("Backup not supported for driver: {$driver}");
        return;
    }

    // Cleanup old backups (keep last 7)
    $files = Storage::files($backupPath);
    if (count($files) > 7) {
        rsort($files); // Newest first
        $filesToDelete = array_slice($files, 7);
        Storage::delete($filesToDelete);
        $this->info("Deleted " . count($filesToDelete) . " old backups.");
    }

})->purpose('Backup the database');

Schedule::command('db:backup')->weekly()->at('02:00')->when(function () {
    return date('W') % 2 == 0;
});

Schedule::call(function () {
    array_map('unlink', glob(storage_path('logs/*.log')));
})->weekly()->at('02:05')->when(function () {
    return date('W') % 2 == 0;
});


