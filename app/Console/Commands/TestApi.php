<?php

namespace App\Console\Commands;

use App\Services\Api\MailgunnerApi;
use Illuminate\Console\Command;

class TestApi extends Command
{
    protected $signature = 'testapi';

    public function handle()
    {
        $apiObj = app()->make(MailgunnerApi::class, ['username' => 'jtodd', 'password' => 'password']);

        $to = 'toddwebnet@gmail.com';
        $from = 'toddwebnet@gmail.com';
        $subject = 'test email';
        $body = 'This the body of my message';
        $apiObj->sendMail($to, $from, $subject, $body);

    }
}
