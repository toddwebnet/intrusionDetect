<?php
namespace App\Console\Commands;

use App\Mail\DeployNotification;
use App\Services\IpLoggingService;
use App\Services\NetworkService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MonitorCommand extends Command
{

    protected $signature = 'util:monitor';
    protected $description = "Monitor the stuff";

    public function handle()
    {
        $this->leaveIfAlreadyRunning();
        while (true) {
            foreach (NetworkService::arpScan() as $mac => $data) {
                IpLoggingService::logIp($mac, $data);
            }
            $this->checkForIntrusions();
            sleep(60);
        }
    }

    private function leaveIfAlreadyRunning()
    {
        $myPid = getmypid();
        $cmdPattern = "intrusionDetect/artisan util:monitor";
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

    private function checkForIntrusions()
    {
        foreach (IpLoggingService::getNewFiles() as $file) {
            $data = json_decode(file_get_contents($file));
            Mail::to("toddwebnet@gmail.com")->send(new DeployNotification($data));
            unlink($file);
        }
    }
}
