<?php

namespace App;

use Illuminate\Support\Facades\Auth;

trait Blameable
{
    public static function bootBlameable()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::user()->id;
            $model->updated_by = Auth::user()->id;
        });

        static::updating(function ($model) {            
            $model->updated_by = Auth::user()->id;
        });
    }
}
