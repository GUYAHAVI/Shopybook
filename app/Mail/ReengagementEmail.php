<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReengagementEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $bodyText;

    public function __construct(string $subjectLine, string $bodyText)
    {
        $this->subjectLine = $subjectLine;
        $this->bodyText = $bodyText;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->from('elvis@shopybook.com', 'Elvis Havi — Shopybook')
                    ->text('emails.reengagement')
                    ->with([
                        'bodyText' => $this->bodyText,
                    ]);
    }
}
