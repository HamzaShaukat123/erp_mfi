<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDO;
use Exception;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class DatabaseBackupController extends Controller
{
    public function backupDatabase()
    {
        $dbHost = env('DB_HOST');
        $dbName = env('DB_DATABASE');
        $dbUser = env('DB_USERNAME');
        $dbPassword = env('DB_PASSWORD');
    
        // try {
        //     $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName}", $dbUser, $dbPassword);
        //     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //     die("Database connection successful.");
        // } catch (PDOException $e) {
        //     die("Database connection failed: " . $e->getMessage());
        //     return response()->json(['error' => 'Database connection failed. ' . $e->getMessage()], 500);
        // }

        $fileName = 'backup_' . date('Y-m-d_H-i-s') . '.sql';
        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];
    
        try {
            \Log::info("Connecting to database...");
            $pdo = new PDO("mysql:host={$dbHost};dbname={$dbName}", $dbUser, $dbPassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
            \Log::info("Fetching tables...");
            $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
    
            if (empty($tables)) {
                \Log::error("No tables found in the database.");
                return response()->json(['error' => 'No tables found in the database.'], 500);
            }
    
            $sqlDump = "-- Database Backup\n-- Generated on " . date('Y-m-d H:i:s') . "\n\n";
            foreach ($tables as $table) {
                \Log::info("Processing table: {$table}");
                $escapedTable = "`" . str_replace("`", "``", $table) . "`";
    
                $createTableStmt = $pdo->query("SHOW CREATE TABLE {$escapedTable}")->fetch(PDO::FETCH_ASSOC)['Create Table'];
                $sqlDump .= "-- Structure for table `{$table}`\n";
                $sqlDump .= "{$createTableStmt};\n\n";
    
                $rows = $pdo->query("SELECT * FROM {$escapedTable}");
                while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
                    $values = array_map([$pdo, 'quote'], $row);
                    $sqlDump .= "INSERT INTO `{$table}` VALUES (" . implode(", ", $values) . ");\n";
                }
                $sqlDump .= "\n";
            }
    
            return response()->stream(function () use ($sqlDump) {
                echo $sqlDump;
            }, 200, $headers);
    
        } catch (Exception $e) {
            \Log::error("Database backup failed: " . $e->getMessage());
            return response()->json(['error' => 'Database backup failed. ' . $e->getMessage()], 500);
        }
    }
    
    

    public function downloadZip()
    {
        // Path to the directory you want to zip
        $directoryPath = public_path('uploads'); // Assuming 'uploads' is in the 'public' directory

        if (!is_dir($directoryPath)) {
            return response()->json(['error' => 'The specified directory does not exist.'], 404);
        }

        // Create a temporary file to store the zip
        $zipFileName = 'uploads_' . date('Y-m-d_H-i-s') . '.zip';
        $tempPath = storage_path('app/temp');

        // Ensure the temp directory exists
        if (!is_dir($tempPath)) {
            mkdir($tempPath, 0755, true); // Create directory with proper permissions
        }

        $zipFilePath = $tempPath . '/' . $zipFileName;

        // Create a new ZipArchive instance
        $zip = new ZipArchive();

        // Open the zip file for writing
        if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
            // Add files to the zip
            $this->addFilesToZip($zip, $directoryPath);

            // Close the zip file
            $zip->close();

            // Stream the zip file to the browser for download
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        } else {
            return response()->json(['error' => 'Failed to create zip file.'], 500);
        }
    }

/**
 * Recursively add files and directories to the zip
 */
protected function addFilesToZip(ZipArchive $zip, $directoryPath, $zipPath = '')
{
    // Get all files and directories inside the given directory
    $files = glob($directoryPath . '/*');

    foreach ($files as $file) {
        $localPath = $zipPath . basename($file); // The path inside the zip file

        if (is_dir($file)) {
            // If it's a directory, add it to the zip and recursively add its contents
            $zip->addEmptyDir($localPath);
            $this->addFilesToZip($zip, $file, $localPath . '/');
        } else {
            // If it's a file, add it to the zip
            if (file_exists($file)) {
                $zip->addFile($file, $localPath);
            }
        }
    }
}

}
