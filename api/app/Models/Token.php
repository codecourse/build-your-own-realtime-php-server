<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    public function scopeBySecret(Builder $builder, $secret)
    {
        return $builder->where('secret', $secret);
    }

    public function scopeByKey(Builder $builder, $key)
    {
        return $builder->where('key', $key);
    }
}