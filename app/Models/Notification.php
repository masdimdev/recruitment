<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'trans_attributes' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'header_key',
        'content_key',
        'trans_attributes',
        'notifiable_id',
        'notifiable_type',
    ];

    /**
     * Get the parent notifiable model (candidate or company).
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
