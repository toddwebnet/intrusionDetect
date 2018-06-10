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
        $cmdPattern = "php /home/jtodd/intrusionDetect/artisan util:monitor";
        $cmd = 'ps -aux | grep artisan';
        $results = [];

//        foreach (NetworkService::runCmd($cmd) as $line) {

        $lines = explode("\n", "root       937  0.0  0.2   1884  1084 ?        Ss   17:58   0:00 /bin/sh -c php /home/jtodd/intrusionDetect/artisan util:monitor
root       938  0.8  4.7  62160 21252 ?        S    17:58   0:01 php /home/jtodd/intrusionDetect/artisan util:monitor
root       964  0.0  0.2   1884  1104 ?        Ss   17:59   0:00 /bin/sh -c php /home/jtodd/intrusionDetect/artisan util:monitor
root       965  0.9  4.7  62160 20932 ?        S    17:59   0:01 php /home/jtodd/intrusionDetect/artisan util:monitor
root       983  0.0  0.2   1884  1136 ?        Ss   18:00   0:00 /bin/sh -c php /home/jtodd/intrusionDetect/artisan util:monitor
root       984  1.5  4.7  62160 21076 ?        S    18:00   0:01 php /home/jtodd/intrusionDetect/artisan util:monitor
root      1004  0.0  0.2   1884  1168 ?        Ss   18:01   0:00 /bin/sh -c php /home/jtodd/intrusionDetect/artisan util:monitor
root      1005  4.6  4.7  62160 21144 ?        S    18:01   0:01 php /home/jtodd/intrusionDetect/artisan util:monitor
jtodd     1024  0.0  0.4   4364  1940 pts/0    S+   18:01   0:00 grep --color=auto artisan");
        foreach($lines as $line){
            print $line . "\n";
            $flag = false;
            if (preg_match("/bin\/sh/i", $line)) {
                continue;
            }
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

    private function checkForIntrusions()
    {
        foreach (IpLoggingService::getNewFiles() as $file) {
            $data = json_decode(file_get_contents($file));
            Mail::to("toddwebnet@gmail.com")->send(new DeployNotification($data));
            unlink($file);
        }
    }
}
