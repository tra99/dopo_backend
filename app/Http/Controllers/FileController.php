<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FileService;

class FileController extends Controller
{
    protected $fileService;

    // Constructor dependency injection for fileService
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function uploadFile(Request $request)
    {
        // Validate incoming data, including the file
        $validatedData = $request->validate([
            'school' => 'required|string|max:255', // Validate school input
            'type' => 'required|string|max:255',   // Validate type input
            'file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240', // Validate file: type and size (10MB max)
        ]);

        // Get the school and type values from the request
        $school = $request->input('school');
        $type = $request->input('type');

        // Use the fileService to handle the file upload
        $filePath = $this->fileService->uploadFile($validatedData['file'], $school, $type);

        // Check if the file was uploaded successfully
        if ($filePath) {
            return response()->json([
                'message' => 'File uploaded successfully',
                'file_path' => $filePath
            ], 200);
        }

        // Return an error response if the upload fails
        return response()->json(['error' => 'File upload failed'], 400);
    }

    /**
     * Handle file deletion.
     *
     * @param string $filePath
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFile(Request $request)
    {
        $filePath = $request->input('path');
        $deleted = $this->fileService->deleteFile($filePath);

        if ($deleted) {
            return response()->json(['message' => 'File deleted successfully'], 200);
        }

        return response()->json(['error' => 'File not found'], 404);
    }

    public function getFile(Request $request)
    {
        $filePath = $request->input('path');

        if ($filePath) {
            // Use FileService to retrieve the file response for viewing
            $fileResponse = $this->fileService->viewFile($filePath, 'public');

            // If the file exists, return the file response
            if ($fileResponse) {
                return $fileResponse;
            }
        }

        // Return an error response if the file is not found
        return response()->json(['error' => 'File not found'], 404);
    }
}
