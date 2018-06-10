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
        while(true) {
            dump(NetworkService::arpScan());
            sleep(1);
        }


    }
}
