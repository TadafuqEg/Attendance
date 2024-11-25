<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendContactUs extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $message;
   

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name,$email,$message)
    {
        
        $this->name = $name;
        $this->email = $email;
        $this->message = $message;
      
    }


  /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        
        // return $this->view('emails.contact_us')
        //             ->subject('Tadafuq Attendance Contact Us')
        //             ->with([
        //                 'name' => $this->name,
        //                 'email' => $this->email,
        //                 'message' => $this->message,
                       
        //             ]);
                    return $this->view('emails.contact_us')->subject('Tadafuq Attendance Contact Us')
                    ->with([
                        'name' => $this->name,
                        'email' => $this->email,
                        'messageContent' => $this->message,
                    ]);
    }
}
