<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DatabaseBackupController extends Controller
{
    public function backupDatabase()
    {
        // Database credentials
$dbHost = env('DB_HOST', '127.0.0.1');
$dbName = env('DB_DATABASE', 'database');
$dbUser = env('DB_USERNAME', 'username');
$dbPassword = env('DB_PASSWORD', 'password');

// Headers for download
$fileName = 'backup_tables_' . date('Y-m-d_H-i-s') . '.sql';
$headers = [
    'Content-Type' => 'application/sql',
    'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
];

// Build mysqldump command
$command = "mysqldump --host={$dbHost} --user={$dbUser} --password={$dbPassword} {$dbName} --no-create-info --skip-add-drop-table";

return response()->stream(function () use ($command) {
    $process = proc_open($command, [
        1 => ['pipe', 'w'], // Standard output
        2 => ['pipe', 'w'], // Standard error
    ], $pipes);

    if (is_resource($process)) {
        // Capture standard error
        $errorOutput = stream_get_contents($pipes[2]);
        if (!empty($errorOutput)) {
            file_put_contents(storage_path('logs/mysqldump_error.log'), $errorOutput);
        }

        // Output standard output
        while ($line = fgets($pipes[1])) {
            echo $line;
            flush();
        }

        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);
    }
}, 200, $headers);
    }
}
