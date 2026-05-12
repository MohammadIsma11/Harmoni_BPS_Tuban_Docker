<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $agenda;

    public function __construct($agenda)
    {
        $this->agenda = $agenda;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toMail($notifiable)
    {
        $agenda = $this->agenda;
        $type = $agenda->activity_type_id;

        // Format tanggal dasar
        $startDate = \Carbon\Carbon::parse($agenda->event_date)->translatedFormat('d F Y');
        $endDate = \Carbon\Carbon::parse($agenda->end_date)->translatedFormat('d F Y');

        // LOGIKA TANGGAL: 
        // Jika Tugas Lapangan (1) atau Dinas Luar (3) dan tanggalnya beda, tampilkan rentang. 
        if (in_array($type, [1, 3]) && $agenda->event_date != $agenda->end_date) {
            $dateInfo = $startDate . ' s.d ' . $endDate;
            $labelTanggal = 'Rentang Waktu';
        } else {
            $dateInfo = $startDate;
            $labelTanggal = 'Tanggal Pelaksanaan';
        }

        return (new MailMessage)
                    ->subject('🔔 PENUGASAN BARU: ' . $agenda->title)
                    ->greeting('Halo, ' . $notifiable->nama_lengkap . '!')
                    ->line('Anda baru saja menerima penugasan baru di sistem **HARMONI BPS**. Berikut adalah rincian agenda Anda:')
                    ->line('---')
                    ->line('**📌 Judul Kegiatan:** ' . $agenda->title)
                    ->line('**📅 ' . $labelTanggal . ':** ' . $dateInfo)
                    ->line('**👥 Unit/Tim Pelaksana:** ' . ($agenda->team->nama_tim ?? 'Umum/Lainnya'))
                    ->line($type == 2 ? '**⏰ Waktu Mulai:** ' . ($agenda->start_time ?? '08:00') . ' WIB' : '')
                    ->line('---')
                    ->line('Untuk melihat rincian lengkap dan melakukan pelaporan, silakan akses dashboard **HARMONI** melalui domain resmi kami:')
                    ->line('🌐 [**harmoni.bpstuban.my.id**](https://harmoni.bpstuban.my.id)')
                    ->line('---')
                    ->line('Mohon segera laksanakan tugas sesuai jadwal dan laporkan hasilnya tepat waktu.')
                    ->salutation('Salam Kerja Sama,' . "\n" . 'Admin HARMONI BPS Tuban');
    }

    public function toDatabase($notifiable)
    {
        return [
            'agenda_id' => $this->agenda->id,
            'title'     => $this->agenda->title,
            'type'      => $this->agenda->activityType->name ?? 'Penugasan',
            'date'      => $this->agenda->event_date,
            'message'   => 'Anda menerima penugasan baru: ' . $this->agenda->title,
            'url'       => $this->agenda->activity_type_id == 1 ? route('task.index') : route('meeting.index'),
        ];
    }
}