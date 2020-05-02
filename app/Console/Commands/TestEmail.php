<?php

namespace App\Console\Commands;

use App\Mail\DeployNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    protected $signature = 'test:email';

    public function handle()
    {
        $data = json_decode('{"ip":"192.168.11.121","mac":"d4:81:d7:90:28:32","descr":"(Unknown)","hostname":"192.168.11.121","ts":1588431320}');
        $to = env('MAIL_TO_ADDRESS');
        Mail::to($to)->send(new DeployNotification($data));

    }
}