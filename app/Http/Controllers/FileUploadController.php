<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Services\FileService;
use Illuminate\Http\Request;

class FileUploadController extends Controller
{
    protected FileService $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function index()
    {
        $files = File::all();

        return view('files', compact('files'));
    }

    public function upload(Request $request)
    {
        // File validation
        $request->validate([
            'file' => 'required|file|mimes:pdf,docx|max:10240', // Max 10 MB
        ]);

        // Save file
        $fileRecord = $this->fileService->store($request->file('file'));

        return response()->json([
            'success' => true,
            'message' => 'File has been successfully upload!',
            'name' => $fileRecord->name,
            'id' => $fileRecord->id,
        ]);
    }

    public function delete($id)
    {
        $this->fileService->delete($id, true);

        return response()->json([
            'success' => true,
            'message' => 'File successfully deleted!',
        ]);
    }
}
