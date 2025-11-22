<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StorageController extends Controller
{
    /**
     * Serve files from public storage disk.
     * This allows serving files without needing the storage:link command.
     */
    public function serve(Request $request, $path)
    {
        // Security: Only allow files from public disk
        $fullPath = storage_path('app/public/' . $path);
        
        // Prevent directory traversal
        $fullPath = realpath($fullPath);
        $publicPath = realpath(storage_path('app/public'));
        
        if (!$fullPath || strpos($fullPath, $publicPath) !== 0) {
            abort(404);
        }
        
        // Check if file exists
        if (!file_exists($fullPath) || !is_file($fullPath)) {
            abort(404);
        }
        
        // Get MIME type
        $mimeType = mime_content_type($fullPath);
        if (!$mimeType) {
            $mimeType = 'application/octet-stream';
        }
        
        // Return file response
        return response()->file($fullPath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000', // Cache for 1 year
        ]);
    }
}

