<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Live extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'user_id',
        'title',
        'description',
        'cover',
        'privacy',
        'tip_options',
    ];

    protected $casts = [
        'tip_options' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePublic($query)
    {
        return $query->where('privacy', 'public');
    }

    public function scopeFollowers($query)
    {
        return $query->where('privacy', 'followers');
    }

    public function scopeSubscribers($query)
    {
        return $query->where('privacy', 'subscribers');
    }
}
