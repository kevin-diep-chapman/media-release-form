<?php

namespace App\Models;

use App\Models\Concerns\FormatsPacificTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use FormatsPacificTime;
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'event_date', 'event_end_date', 'location', 'status', 'created_by'
    ];

    protected $casts = [
        'event_date' => 'date',
        'event_end_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function getEventDatePacificAttribute(): string
    {
        return $this->formatEventDate($this->event_date);
    }

    public function getEventDateDisplayAttribute(): string
    {
        $start = $this->formatEventDate($this->event_date);

        if ($this->event_end_date) {
            return $start.' – '.$this->formatEventDate($this->event_end_date);
        }

        return $start;
    }

    public function getCreatedAtPacificAttribute(): string
    {
        return $this->formatPacificDate($this->created_at);
    }

    public function getDisplayStatusAttribute(): string
    {
        $today = Carbon::now('America/Los_Angeles')->startOfDay();
        $start = $this->eventDateInPacific($this->event_date);
        $end = $this->event_end_date
            ? $this->eventDateInPacific($this->event_end_date)
            : $start;

        if ($end->lt($today)) {
            return 'inactive';
        }

        if ($start->gt($today)) {
            return 'upcoming';
        }

        return 'active';
    }

    private function eventDateInPacific(Carbon|string $date): Carbon
    {
        $dateString = $date instanceof Carbon ? $date->toDateString() : (string) $date;

        return Carbon::createFromFormat('Y-m-d', $dateString, 'America/Los_Angeles')->startOfDay();
    }

    private function formatEventDate(Carbon|string|null $date): string
    {
        if (!$date) {
            return '';
        }

        return $this->eventDateInPacific($date)->format('M j, Y');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function mediaReleases()
    {
        return $this->hasMany(MediaRelease::class);
    }
}
