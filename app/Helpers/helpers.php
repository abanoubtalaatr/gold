<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('getFileFullUrl')) {
    function getFileFullUrl($path)
    {
        return $path ? asset('/storage/' . $path) : null;
    }
}

if (!function_exists('storeFile')) {

    function storeFile($name, $path, $file, $data, $old_file = null)
    {

        unset($data[$name]);

        $array = array_merge([
            $name => $file->store($path, 'public'),
        ], $data);

        if (null !== $old_file) {
            Storage::disk('public')->delete($old_file);
        }

        return $array;
    }
}

if (!function_exists('mobileHandler')) {

    function mobileHandler($mobile)
    {
        $mbil   = str_replace(' ', '', $mobile);
        $mobile   = str_starts_with($mbil, '0') ? substr($mbil, 1, 20) : $mbil;

        return $mobile;
    }
}