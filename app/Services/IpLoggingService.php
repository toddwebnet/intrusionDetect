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
        $data = json_encode($data);
        if (!file_exists($filePath)) {
            self::saveNewFile($mac, $data);
        }
        file_put_contents($filePath, $data);
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
        $data = json_encode($data);

        file_put_contents($filePath, $data);
    }
}
