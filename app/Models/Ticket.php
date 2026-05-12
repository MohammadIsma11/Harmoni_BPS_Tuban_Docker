<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'tracking_id', 'view_token',
        'reporter_name', 'reporter_phone', 'reporter_email', 'reporter_organization',
        'category_id', 'subject', 'description', 'priority', 'attachment',
        'status', 'assigned_to_ids', 'unit_kerja', 'admin_notes', 'solution', 'finished_at',
        'user_id', 'pushed_to_kms', 'wa_status', 'wa_template', 'notification_method'
    ];

    protected $casts = [
        'finished_at' => 'datetime',
        'assigned_to_ids' => 'array',
        'pushed_to_kms' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        // Regenerasi Tracking ID jika kategori berubah
        static::updating(function ($ticket) {
            if ($ticket->isDirty('category_id')) {
                $ticket->tracking_id = $ticket->generateTrackingId($ticket->category_id);
            }
        });
    }

    public function generateTrackingId($categoryId)
    {
        $category = TicketCategory::find($categoryId);
        $prefixMap = [
            'Rekrutmen SE' => 'RSE',
            'Lapangan SE' => 'LSE',
            'Aplikasi FASIH' => 'AF'
        ];
        $catCode = $prefixMap[$category->name] ?? 'GEN';

        // Hitung sequence per kategori
        $count = Ticket::where('category_id', $categoryId)->count();
        $sequence = str_pad($count + 1, 4, '0', STR_PAD_LEFT);
        
        return "BPS3523-{$catCode}-{$sequence}";
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function activities()
    {
        return $this->hasMany(TicketActivity::class)->latest();
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
