<?php

namespace App\Services;

class IpLoggingService
{
    public static function logIp($mac, $data)
    {
        $storagePath = storage_path();
        $folder = "ips";
        $storagePath .= "/{$folder}";
        if (!is_dir($storagePath)) {
            mkdir($storagePath);
        }
        $filePath = $storagePath . "/{$mac}.json";
        if (!file_exists($filePath)) {
            self::saveNewFile($mac, $data);
        }
        file_put_contents($filePath, json_encode($data));
    }

    public static function saveNewFile($mac, $data)
    {
        $storagePath = storage_path();
        $folder = "new";
        $storagePath .= "/{$folder}";
        if (!is_dir($storagePath)) {
            mkdir($storagePath);
        }
        $filePath = $storagePath . "/{$mac}.json";
        file_put_contents($filePath, json_encode($data));
    }

    public static function getNewFiles()
    {
        $storagePath = storage_path();
        $folder = "new";
        $storagePath .= "/{$folder}";
        $files = [];

        foreach (scandir($storagePath) as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            $files[] = "{$storagePath}/{$file}";
        }
        return $files;
    }
}
