<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class FileUploadService
{
    public function upload($file, $path = 'uploads')
    {
        return $file ? Storage::disk('public')->put($path, $file) : null;
    }


    public function delete($path)
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }


    public function uploadMultiple($files, $path = 'uploads')
    {
        $paths = [];
        foreach ($files as $file) {
            $paths[] = $this->upload($file, $path);
        }
        return $paths;
    }

    public function deleteMultiple($paths)
    {
        foreach ($paths as $path) {


            $fullPath = storage_path('app/public/' . $path->path);
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }


    public function deleteFile($filePath)
    {
        $fullPath = storage_path('app/public/' . $filePath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
