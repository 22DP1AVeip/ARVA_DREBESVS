<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class ArvaVerifyEmail extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        // Ģenerē drošo signed URL tieši tāpat kā Laravel default
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Apstiprini savu e-pastu — ArvA')
            ->greeting('Sveiki!')
            ->line('Paldies, ka izveidoji ArvA kontu.')
            ->line('Lūdzu, apstiprini savu e-pasta adresi, nospiežot pogu zemāk:')
            ->action('Apstiprināt e-pastu', $url)
            ->line('Ja tu šo kontu neveidoji, vari ignorēt šo e-pastu.');
    }
}
