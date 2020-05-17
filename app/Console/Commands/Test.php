<?php

namespace App\Console\Commands;

use App\Services\Api\MailgunnerApi;
use Illuminate\Console\Command;

class Test extends Command
{
    protected $signature = 'test';

    public function handle()
    {
        $ping = MailgunnerApi::ping();
        dd($ping);
    }

}