<?php

namespace App;
use Illuminate\Support\Str;
trait UsesUuid
{
    public static function bootUsesUuid()
    {
        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }
}
