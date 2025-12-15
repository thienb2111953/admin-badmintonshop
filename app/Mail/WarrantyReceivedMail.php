<?php

namespace App\Mail;

use App\Models\YeuCauBaoHanh;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WarrantyReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $yeuCau;

    public function __construct(YeuCauBaoHanh $yeuCau)
    {
        $this->yeuCau = $yeuCau;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Xác Nhận Yêu Cầu Bảo Hành #' . $this->yeuCau->id_yeu_cau_bao_hanh,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.warranty_received',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        $attachments = [];
        if ($this->yeuCau->attachment) {
            foreach ($this->yeuCau->attachment as $path) {
                $realPath = public_path($path);
                if (file_exists($realPath)) {
                    $attachments[] = Attachment::fromPath($realPath);
                }
            }
        }
        return $attachments;
    }
}
