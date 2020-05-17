<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Email;
use Mailgun\Mailgun;

class MailgunnerService
{
    private $domain;
    private $mailgun;

    public function __construct()
    {
        // $this->domain = env('MAIL_DOMAIN');
        // $this->mailgun = Mailgun::create(env('MAIL_SECRET'));

    }

    public function sendMail(Email $email)
    {

        $domain = $email->account->domain;
        $secret = $email->account->apikey;
        if (strlen(trim($domain)) == 0 || strlen(trim($secret)) == 0) {
            $domain = env('MAIL_DOMAIN');
            $secret = env('MAIL_SECRET');
            $from = "noreply@mg.mytechtools.com";
        }


        $mailgun = Mailgun::create($secret);
        $feedback = $mailgun->messages()->send($domain, [
            'to' => $email->to,
            'from' => $email->from,
            'subject' => $email->subject,
            'html' => $email->body
        ]);

        return $feedback->getId();
    }

}
