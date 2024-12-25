<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DatabaseBackupController extends Controller
{
    public function backupDatabase()
    {
        $dbHost = env('DB_HOST', '127.0.0.1');
        $dbName = env('DB_DATABASE', 'database');
        $dbUser = env('DB_USERNAME', 'username');
        $dbPassword = env('DB_PASSWORD', 'password');

        // Set the backup file name
        $fileName = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $headers = [
            'Content-Type' => 'application/sql',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        // Query to fetch table names, excluding views
        $tableQuery = "SELECT table_name FROM information_schema.tables WHERE table_schema = ? AND table_type = 'BASE TABLE'";
        $tables = DB::select($tableQuery, [$dbName]);

        if (empty($tables)) {
            return response()->json(['message' => 'No tables found in the database.'], 404);
        }

        // Escape table names for shell command
        $tableNames = array_map(function ($table) {
            return escapeshellarg($table->table_name);
        }, $tables);

        // Prepare the mysqldump command
        $tableList = implode(' ', $tableNames);
        $command = sprintf(
            "mysqldump --host=%s --user=%s --password=%s %s %s",
            escapeshellarg($dbHost),
            escapeshellarg($dbUser),
            escapeshellarg($dbPassword),
            escapeshellarg($dbName),
            $tableList
        );

        // Stream the mysqldump output as a response
        return Response::stream(function () use ($command) {
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
            } else {
                echo "Failed to execute the backup process.";
            }
        }, 200, $headers);
    }
}
