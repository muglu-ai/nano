<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadController extends Controller
{
    public function show()
    {
        return view('admin.upload.form');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'max:1048576', // 1GB in kilobytes
            ]
        ]);

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::slug($file->getClientOriginalName());

            // Store file in the storage/app/uploads directory
            $path = $file->storeAs('uploads', $fileName);

            // You might want to save the file information to database
            // FileUpload::create([
            //     'name' => $fileName,
            //     'path' => $path,
            //     'size' => $file->getSize(),
            //     'mime_type' => $file->getMimeType()
            // ]);

            return back()->with('success', 'File uploaded successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to upload file: ' . $e->getMessage()]);
        }
    }
}
