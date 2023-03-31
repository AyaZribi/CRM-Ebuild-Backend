<?php

namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FacturePdf extends Mailable implements ShouldQueue
{
use Queueable, SerializesModels;

public $pdf;

/**
* Create a new message instance.
*
* @param BinaryFileResponse $pdf
* @return void
*/
public function __construct(BinaryFileResponse $pdf)
{
$this->pdf = $pdf;
}

/**
* Build the message.
*
* @return $this
*/
public function build()
{
return $this->subject('Your Facture PDF')->view('emails.facture-pdf')->attachData($this->pdf->getContent(), 'facture.pdf', [
'mime' => $this->pdf->headers->get('content-type'),
]);
}
}
