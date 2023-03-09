<?php
namespace App\Mail;

use App\Models\Personnel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewPersonnelMail extends Mailable
{
use Queueable, SerializesModels;

public $personnel;
public $password;

/**
* Create a new message instance.
*
* @return void
*/
public function __construct(Personnel $personnel, $password)
{
$this->personnel = $personnel;
$this->password = $password;
}

/**
* Build the message.
*
* @return $this
*/
    public function build()
    {
        return $this->subject('Your new account has been created')
            ->view('new_personnel');
    }
}
