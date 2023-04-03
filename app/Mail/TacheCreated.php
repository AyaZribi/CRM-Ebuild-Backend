<?php

namespace App\Mail;

use App\Models\Project;
use App\Models\Tache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TacheCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The project instance.
     *
     * @var Tache
     */
    public $tache;

    /**
     * Create a new message instance.
     *
     * @param  Tache  $tache
     * @return void
     */
    public function __construct(Tache $tache)
    {
        $this->tache = $tache;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "New Tache: {$this->tache->intitule}";


        $email = $this->subject($subject)
            ->view('emails.tache-created')
            ->with([
                'intitule' => $this->tache->intitule,
                'deadline' => $this->tache->deadline,
                'description' => $this->tache->description,
            ]);



        return $email;
    }


}
