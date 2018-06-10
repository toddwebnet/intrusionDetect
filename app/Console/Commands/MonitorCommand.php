<?php
namespace App\Console\Commands;

use App\Services\IpLoggingService;
use App\Services\NetworkService;
use Illuminate\Console\Command;

class MonitorCommand extends Command
{

    protected $signature = 'util:monitor';
    protected $description = "Monitor the stuff";

    public function handle()
    {
        self::leaveIfAlreadyRunning();
        while (true) {
            foreach (NetworkService::arpScan() as $mac => $data) {
                IpLoggingService::logIp($mac, $data);
            }
            sleep(60);
        }
    }

    function leaveIfAlreadyRunning()
    {
        $myPid = getmypid();
        $cmdPattern = "/artisan util:monitor";
        $cmd = 'ps -ef | awk \'/artisan util:monitor/{print $2"@"$8" "$9" " $10}\'';
        foreach (NetworkService::runCmd($cmd) as $line) {
            $ar = explode("@", $line);
            print $line . "\n";
            if (strpos($ar[1], $cmdPattern) && $ar[0] != $myPid) {
                print "leaving";
                exit();
            }
        }
    }
}
