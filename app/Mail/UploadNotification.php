<?php

namespace App\Mail;

use App\Models\UploadSession;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UploadNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $uploadSession;
    public $downloadLink;

    public function __construct(UploadSession $uploadSession, string $downloadLink)
    {
        $this->uploadSession = $uploadSession;
        $this->downloadLink = $downloadLink;
    }

    public function build()
    {
        return $this->subject('Your files have been uploaded')
                    ->markdown('emails.upload-notification');
    }
}
