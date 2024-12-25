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

        // Set the backup file name and storage path
        $fileName = 'backup_' . now()->format('Y-m-d_H-i-s') . '.sql';
        $filePath = storage_path("app/{$fileName}");

        // Prepare the mysqldump command
        $command = sprintf(
            "mysqldump --host=%s --user=%s --password=%s %s > %s",
            escapeshellarg($dbHost),
            escapeshellarg($dbUser),
            escapeshellarg($dbPassword),
            escapeshellarg($dbName),
            escapeshellarg($filePath)
        );

        // Execute the command
        $output = null;
        $returnVar = null;
        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            return response()->json(['message' => 'Failed to create database backup.'], 500);
        }

        // Return the backup file as a download response
        return Response::download($filePath)->deleteFileAfterSend(true);
    }
}
