<?php

namespace App\Services;

class NetworkService
{

    public static function ping($domain)
    {
        $starttime = microtime(true);
        $errno = null;
        $errstr = null;
        try {
            $file = fsockopen($domain, 80, $errno, $errstr, 1);
        } catch (\Exception $e) {
            return 0;
        }
        $stoptime = microtime(true);
        $status = 0;
        if (!$file) $status = -1;  // Site is down
        else {
            fclose($file);
            $status = ($stoptime - $starttime);
        }
        return $status;
    }

    public static function arpScan()
    {
        // Must be run as root
        $arp_scan = shell_exec('arp-scan --localnet');
        $arp_scan = explode("\n", $arp_scan);
        $matches = null;
        foreach($arp_scan as $scan) {

            $matches = array();

            if(preg_match('/^([0-9\.]+)[[:space:]]+([0-9a-f:]+)[[:space:]]+(.+)$/', $scan, $matches) !== 1) {
                continue;
            }

            $ip = $matches[1];
            $mac = $matches[2];
            $desc = $matches[3];

            echo "Found device with mac address $mac ($desc) and ip $ip\n";
        }
    }

}
