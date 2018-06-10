<?php
namespace App\Console\Commands;

use App\Services\NetworkService;
use Illuminate\Console\Command;

class MonitorCommand extends Command
{

    protected $signature = 'util:monitor';
    protected $description = "Monitor the stuff";

    public function handle()
    {
        dump(NetworkService::arpScan());
        return;
        for ($x = 1; $x < 255; $x++) {
            $ip = "192.168.11.{$x}";
            print $ip . " - ";
            $ping = NetworkService::ping($ip);
            if ($ping == 0) {
                print "down\n";
            } else {
                print "up\n";
            }
        }

    }
}
