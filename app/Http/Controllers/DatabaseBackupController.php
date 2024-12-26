<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDO;
use Exception;

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

        try {
            // Create a new PDO instance
            $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName}", $dbUser, $dbPassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Get a list of tables (excluding views)
            $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);

            // Start building the SQL dump
            $sqlDump = "-- Database Backup\n-- Generated on " . date('Y-m-d H:i:s') . "\n\n";
            foreach ($tables as $table) {
                // Add the CREATE TABLE statement
                $createTableStmt = $pdo->query("SHOW CREATE TABLE {$table}")->fetch(PDO::FETCH_ASSOC)['Create Table'];
                $sqlDump .= "-- Structure for table `{$table}`\n";
                $sqlDump .= "{$createTableStmt};\n\n";

                // Add the INSERT statements for table data
                $sqlDump .= "-- Dumping data for table `{$table}`\n";
                $rows = $pdo->query("SELECT * FROM {$table}")->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    foreach ($rows as $row) {
                        $values = array_map([$pdo, 'quote'], $row);
                        $sqlDump .= "INSERT INTO `{$table}` VALUES (" . implode(", ", $values) . ");\n";
                    }
                }
                $sqlDump .= "\n";
            }

            // Return the SQL dump as a response
            return response()->stream(function () use ($sqlDump) {
                echo $sqlDump;
            }, 200, $headers);

        } catch (Exception $e) {
            // Log the error and return a response
            \Log::error("Database backup failed: " . $e->getMessage());
            return response()->json(['error' => 'Database backup failed. Please check the logs for more details.'], 500);
        }
    }
}
