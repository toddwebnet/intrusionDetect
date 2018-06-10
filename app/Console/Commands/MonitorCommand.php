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
        foreach (NetworkService::arpScan() as $mac => $data) {
            IpLoggingService::logIp($mac, $data);
        }
    }
}
