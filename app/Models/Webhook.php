<?php

namespace App\Models;

use App\Blameable;
use App\UsesUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Webhook extends Model
{
    use UsesUuid, Blameable;
    protected $casts = ['id' => 'string'];
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'application_name',
        'container_name',
        'image_name',
        'token',
    ];

    /**
     * Get all of the deployHistories for the Webhook
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deployHistories(): HasMany
    {
        return $this->hasMany(DeployHistory::class);
    }

    /**
     * Get all of the members for the Webhook
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * Get the creator associated with the Webhook
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function creator(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }
}
