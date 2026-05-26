<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('db:backup-local {--connection=mysql} {--name=} {--keep=10}', function () {
    $connectionName = (string) $this->option('connection');
    $connection = config("database.connections.{$connectionName}");

    if (!$connection) {
        $this->error("Database connection [{$connectionName}] is not configured.");

        return self::FAILURE;
    }

    $driver = $connection['driver'] ?? null;
    if ($driver !== 'mysql') {
        $this->error("Database backup currently supports only mysql connections. [{$connectionName}] uses [{$driver}].");

        return self::FAILURE;
    }

    $database = (string) ($connection['database'] ?? '');
    $username = (string) ($connection['username'] ?? '');
    $password = (string) ($connection['password'] ?? '');
    $host = (string) ($connection['host'] ?? 'mysql');
    $port = (string) ($connection['port'] ?? '3306');

    if ($database === '' || $username === '') {
        $this->error("Database connection [{$connectionName}] is missing required credentials.");

        return self::FAILURE;
    }

    $backupDirectory = storage_path('db-backups');
    File::ensureDirectoryExists($backupDirectory);

    $baseName = (string) ($this->option('name') ?: now()->format('Y-m-d_H-i-s')."_{$database}");
    $safeBaseName = preg_replace('/[^A-Za-z0-9._-]/', '_', $baseName) ?: now()->format('Y-m-d_H-i-s')."_{$database}";
    $backupPath = $backupDirectory.DIRECTORY_SEPARATOR.$safeBaseName.'.sql.gz';

    $command = [
        'mysqldump',
        '--single-transaction',
        '--quick',
        '--skip-lock-tables',
        '--host='.$host,
        '--port='.$port,
        '--user='.$username,
        '--password='.$password,
        $database,
    ];

    $dumpResult = Process::timeout(300)->run($command);

    if ($dumpResult->failed()) {
        $this->error('Database dump failed.');

        $errorOutput = trim($dumpResult->errorOutput() ?: $dumpResult->output());
        if ($errorOutput !== '') {
            $this->line($errorOutput);
        }

        return self::FAILURE;
    }

    $compressedDump = gzencode($dumpResult->output(), 9);

    if ($compressedDump === false) {
        $this->error('Database dump compression failed.');

        return self::FAILURE;
    }

    File::put($backupPath, $compressedDump);

    $keep = max((int) $this->option('keep'), 1);
    $backupFiles = collect(File::files($backupDirectory))
        ->filter(fn ($file) => str_ends_with($file->getFilename(), '.sql.gz'))
        ->sortByDesc(fn ($file) => $file->getMTime())
        ->values();

    $backupFiles->slice($keep)->each(function ($file): void {
        File::delete($file->getPathname());
    });

    $this->info('Database backup created successfully.');
    $this->line('File: '.$backupPath);
    $this->line('Retained backups: '.$backupFiles->take($keep)->count());

    return self::SUCCESS;
})->purpose('Create a local gzipped database backup in storage/db-backups');
