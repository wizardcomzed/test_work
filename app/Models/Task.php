<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','description','status','importance','deadline'
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];

    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline->isPast();
    }

    public function getPriorityScoreAttribute(): float
    {
        $days = now()->diffInDays($this->deadline, false);
        if ($days <= 0) return 0;
        return round($this->importance * (1 / $days), 2);
    }
}
