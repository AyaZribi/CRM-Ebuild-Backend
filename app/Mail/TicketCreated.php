<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Ticket;
use App\Models\Project;

class TicketCreated extends Mailable
{
    use SerializesModels;

    public $ticket;
    public $project;

    /**
     * Create a new message instance.
     *
     * @param Ticket $ticket
     * @param Project $project
     * @return void
     */
    public function __construct(Ticket $ticket, Project $project)
    {
        $this->ticket = $ticket;
        $this->project = $project;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.ticket_created')
            ->subject('[EBUILD] New Ticket Created')
            ->with([
                'ticket' => $this->ticket,
                'project' => $this->project,
            ]);
    }
}
