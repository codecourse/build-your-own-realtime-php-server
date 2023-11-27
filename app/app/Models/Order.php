<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function belongsToUser(User $user)
    {
        return $this->user->id === $user->id;
    }
}
