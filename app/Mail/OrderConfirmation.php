<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'ARVA — Pasūtījums #' . $this->order->id . ' apstiprināts',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-confirmation',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.order-receipt', ['order' => $this->order]);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                'kvits-' . $this->order->id . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}