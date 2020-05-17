<?php
namespace App\Console\Commands;

use App\Mail\DeployNotification;
use App\Services\Api\MailgunnerApi;
use App\Services\IpLoggingService;
use App\Services\NetworkService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MonitorCommand extends Command
{

    protected $signature = 'util:monitor';
    protected $description = "Monitor the stuff";

    public function handle()
    {
        // can't do anything if the mailgunner api is not working
        if (!MailgunnerApi::ping()) {
            return false;
        }
        foreach (NetworkService::arpScan() as $mac => $data) {
            IpLoggingService::logIp($mac, $data);
        }
        $this->checkForIntrusions();
    }

    private function checkForIntrusions()
    {
        foreach (IpLoggingService::getNewFiles() as $file) {
            Log::info("Dumping File: {$file}");
            $data = json_decode(file_get_contents($file));
            $this->deployMail($data);

            unlink($file);
        }
    }

    private function deployMail($data)
    {
        $data = json_decode('{"ip":"192.168.11.207","mac":"00:11:d9:94:30:7e","descr":"TiVo","hostname":"192.168.11.207","ts":1588432135}', true);
        $body = view('mails.deploy', $data)->render();
        $to = env('MAIL_TO_ADDRESS');
        $from = env("MAIL_FROM_ADDRESS");
        $subject = "Deploy Notification.";
        $apiObj = app()->make(MailgunnerApi::class, [
            'username' => env('MAILGUNNER_USERNAME'),
            'password' => env('MAILGUNNER_PASSWORD')
        ]);
        $apiObj->sendMail($to, $from, $subject, $body);

    }
}
