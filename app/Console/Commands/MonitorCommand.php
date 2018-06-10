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
        foreach (NetworkService::arpScan() as $mac => $data) {
            IpLoggingService::logIp($mac, $data);
        }
        $this->checkForIntrusions();
    }

    /*
        private function leaveIfAlreadyRunning()
        {
            if (file_exists('/tmp/artisan_util_monitor')) {
                print "leaving";
                exit();
            }
            $myPid = getmypid();
            file_put_contents('/tmp/artisan_util_monitor', $myPid);
            $cmd = 'ps -aux | grep artisan';
            $results = [];

            foreach (NetworkService::runCmd($cmd) as $line) {
                print $line . "\n";
                $flag = false;
                if (preg_match("/php/i", $line)) {
                    $pid = null;
                    foreach (explode(" ", $line) as $proc) {
                        if (!is_numeric($pid) && is_numeric($proc)) {
                            $pid = (int)$proc;
                        }
                        if ($proc == 'util:monitor') {
                            $flag = true;
                        }
                    }
                }
                if ($flag && $myPid != $pid) {
                    dump([
                        $line,
                        $pid
                    ]);
                    print "\nleaving\n";
                    exit();
                }
            }
        }
    */
    private function checkForIntrusions()
    {
        foreach (IpLoggingService::getNewFiles() as $file) {
            $data = json_decode(file_get_contents($file));
            Mail::to("toddwebnet@gmail.com")->send(new DeployNotification($data));
            unlink($file);
        }
    }
}
