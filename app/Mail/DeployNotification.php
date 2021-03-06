<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class DeployNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The demo object instance.
     *
     * @var Demo
     */
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $data->ts = Carbon::createFromTimestamp($data->ts)->toDateTimeString();
        $this->data = (array)$data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $from = env("MAIL_FROM_ADDRESS");
        $to = env('MAIL_TO_ADDRESS');
        return $this->from($from)
            ->to($to)
            ->text('mails.deploy')
            ->with($this->data);
    }
}
