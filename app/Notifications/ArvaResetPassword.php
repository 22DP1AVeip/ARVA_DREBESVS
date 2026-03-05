<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ArvaResetPassword extends ResetPassword
{
    public function toMail($notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Paroles atiestatīšana — ArvA')
            ->greeting('Sveiki!')
            ->line('Tu saņēmi šo e-pastu, jo tika pieprasīta paroles atiestatīšana.')
            ->action('Atiestatīt paroli', $url)
            ->line('Šī saite būs derīga 60 minūtes.')
            ->line('Ja tu to nepieprasīji, vari ignorēt šo e-pastu.');
    }
}