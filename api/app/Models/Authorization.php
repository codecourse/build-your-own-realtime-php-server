<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Authorization extends Model
{
    protected $table = 'authorizations';

    protected $fillable = [
        'channel', 'session_id'
    ];

    public function scopeByChannelAndSessionId(Builder $builder, $channel, $sessionId)
    {
        return $builder->where('channel', $channel)
            ->where('session_id', $sessionId);
    }
}