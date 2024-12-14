<?php

namespace App\Models;

use App\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class DeployHistory extends Model
{
    use UsesUuid;
    protected $casts = ['id' => 'string'];
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'webhook_id',
        'user_id',
        'status',
        'trigger',
        'message',
    ];
}
