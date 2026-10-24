<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class GymClass extends Model
{
    protected $guarded = [];
    protected $casts = ['days' => 'array'];

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function startsAt(): Carbon
    {
        return now()->setTimeFromTimeString($this->start_time);
    }

    public function endsAt(): Carbon
    {
        return $this->startsAt()->addMinutes($this->duration_min);
    }

    public function toApi(): array
    {
        $n = ['', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $s = $this->startsAt();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'trainer_id' => $this->trainer_id,
            'trainer_name' => $this->trainer?->name,
            'days' => $this->days,
            'start_time' => $s->format('H:i'),
            'duration_min' => $this->duration_min,
            'capacity' => $this->capacity,
            'schedule_label' => implode('/', array_map(fn ($d) => $n[$d], $this->days)) . ' ' . $s->format('g:i A'),
            'time_label' => $s->format('g:i A') . ' – ' . $this->endsAt()->format('g:i A'),
        ];
    }
}