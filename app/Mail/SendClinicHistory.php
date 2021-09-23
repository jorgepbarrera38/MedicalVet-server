<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendClinicHistory extends Mailable
{
    use Queueable, SerializesModels;

    public $company;
    public $petName;
    public $owner;
    public $typeSubject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($company, $petName, $owner, $typeSubject)
    {   
        $this->company = $company;
        $this->petName = $petName;
        $this->owner = $owner;
        $this->typeSubject = $typeSubject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this::view('clinic-histories.reports.mails.clinic-history')->subject(ucfirst($this->typeSubject) . ' de ' . $this->petName)
                                ->attach(public_path('temp.pdf'), ['as' =>  ucfirst($this->typeSubject) .'.pdf', 'mime' => 'application/pdf']);
    }
}
