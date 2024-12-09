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

        // Set headers for download
        $fileName = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $headers = [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        // Stream mysqldump output to the browser
        $command = "mysqldump --host={$dbHost} --user={$dbUser} --password={$dbPassword} {$dbName}";

        return response()->stream(function () use ($command) {
            $process = proc_open($command, [
                1 => ['pipe', 'w'], // Standard output
                2 => ['pipe', 'w'], // Standard error
            ], $pipes);

            if (is_resource($process)) {
                while ($line = fgets($pipes[1])) {
                    echo $line;
                    flush();
                }

                fclose($pipes[1]);
                proc_close($process);
            }
        }, 200, $headers);
    }
}
