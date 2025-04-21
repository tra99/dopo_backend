<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Handle file upload and store it.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $school
     * @param string $type
     * @return string|null
     */
    public function uploadFile(UploadedFile $file, string $school = 'school', string $type = 'questions'): ?string
    {
        try {
            if (!$file->isValid()) {
                return null;
            }
            // Define the disk for storing files
            $disk = 'public';

            // $fileType = explode('/', $file->getClientMimeType())[0];
            $fileType = $file->getClientOriginalExtension();

            // Set custom path based on school and type
            $path = $type . '/' . str_replace(' ', '_', strtolower($school));
            // Generate a unique filename using the original file name and adding a random string
            $fileName = $filename ?? (explode('.', str_replace(' ', '_', $file->getClientOriginalName()))[0] . Str::random(12) . '.' . $file->getClientOriginalExtension());
            // Store the file in the specified disk and path
            $filePath = $file->storeAs($path, $fileName, $disk);

            // Return the file path
            return $filePath;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Handle file deletion.
     *
     * @param string $filePath
     * @param string $disk
     * @return bool
     */
    public function deleteFile(string $filePath, string $disk = 'public'): bool
    {
        // Check if file exists in the specified disk and delete it if it exists
        return Storage::disk($disk)->exists($filePath) ? Storage::disk($disk)->delete($filePath) : false;
    }

    public function viewFile(string $filePath, string $disk = 'public')
    {
        if (Storage::disk($disk)->exists($filePath)) {
            // Get the full system path to the file
            $fullPath = Storage::disk($disk)->path($filePath);
            // Return a response that displays the file inline (if supported by the browser)
            return response()->file($fullPath);
        }

        return null;
    }


    public function readExcelFile(UploadedFile $file)
    {
        try {
            $extension = strtolower($file->getClientOriginalExtension());
            $data = [];

            if ($extension === 'xlsx') {
                // Use SimpleXLSX for XLSX files
                $xlsx = new \Shuchkin\SimpleXLSX($file->getRealPath());
                if ($xlsx->success()) {
                    $data = $xlsx->rows();
                } else {
                    throw new \Exception("Error reading XLSX file: " . $xlsx->error());
                }
            } elseif ($extension === 'xls') {
                // Use SimpleXLS for XLS files
                $xls = new \Shuchkin\SimpleXLS($file->getRealPath());
                if ($xls->success()) {
                    $data = $xls->rows();
                } else {
                    throw new \Exception("Error reading XLS file: " . $xls->error());
                }
            } elseif ($extension === 'csv') {
                // Use native PHP functions for CSV files
                if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
                    while (($row = fgetcsv($handle, 1000, ",")) !== false) {
                        $data[] = $row;
                    }
                    fclose($handle);
                } else {
                    throw new \Exception("Error reading CSV file.");
                }
            } else {
                throw new \Exception("Unsupported file type.");
            }

            return $data;
        } catch (\Exception $e) {
            return null;
        }
    }
}
