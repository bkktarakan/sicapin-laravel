<?php

namespace App\Notifications;

use App\Models\Sertifikat;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SertifikatStatusNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Sertifikat $sertifikat,
        protected string $action
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = new MailMessage;

        if ($this->action === 'approved') {
            $mail->subject('Sertifikat Disetujui - ' . $this->sertifikat->nama_pelatihan)
                 ->greeting('Halo, ' . $notifiable->nama . '!')
                 ->line('Sertifikat pelatihan "' . $this->sertifikat->nama_pelatihan . '" telah **disetujui**.')
                 ->line('JPL: ' . $this->sertifikat->jpl)
                 ->action('Lihat Sertifikat', url('/sertifikat'))
                 ->line('Terima kasih telah meningkatkan kompetensi Anda.');
        } elseif ($this->action === 'rejected') {
            $mail->subject('Sertifikat Ditolak - ' . $this->sertifikat->nama_pelatihan)
                 ->greeting('Halo, ' . $notifiable->nama . '!')
                 ->line('Sertifikat pelatihan "' . $this->sertifikat->nama_pelatihan . '" **ditolak**.')
                 ->line('Alasan: ' . ($this->sertifikat->catatan_verifikasi ?? '-'))
                 ->action('Perbaiki & Kirim Ulang', url('/sertifikat'))
                 ->line('Silakan perbaiki dan kirim ulang sertifikat Anda.');
        } elseif ($this->action === 'resubmitted') {
            $mail->subject('Sertifikat Dikirim Ulang - ' . $this->sertifikat->nama_pelatihan)
                 ->greeting('Perhatian Admin,')
                 ->line($notifiable->nama . ' telah mengirim ulang sertifikat "' . $this->sertifikat->nama_pelatihan . '" untuk diverifikasi.')
                 ->action('Verifikasi Sekarang', url('/sertifikat/pending'))
                 ->line('Mohon segera ditinjau.');
        }

        return $mail;
    }
}
