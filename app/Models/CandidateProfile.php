<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateProfile extends Model
{
    use HasFactory;

    const SEX_MALE = 1;
    const SEX_FEMALE = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'phone_number',
        'address',
        'date_of_birth',
        'sex',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
