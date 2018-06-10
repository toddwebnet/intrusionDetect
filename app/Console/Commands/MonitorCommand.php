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
        $cmdPattern = "/usr/bin/php ./snap.php";
        $cmd = 'ps -ef | awk \'/snap.php/{print $2"@"$8" "$9}\'';
        foreach (NetworkService::runCmd($cmd) as $line) {
            $ar = explode("@", $line);
            if ($ar[1] == $cmdPattern && $ar[0] != $myPid) {
                print "leaving";
                exit();
            }
        }
    }
}
