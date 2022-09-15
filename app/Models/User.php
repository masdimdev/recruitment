<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const TYPE_CANDIDATE = 1;
    const TYPE_COMPANY = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'user_type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'user_type_name',
    ];

    public function isCandidate(): bool
    {
        return $this->user_type == self::TYPE_CANDIDATE;
    }

    public function isCompany(): bool
    {
        return $this->user_type == self::TYPE_COMPANY;
    }

    public function getUserTypeNameAttribute($value): ?string
    {
        switch ($this->user_type) {
            case self::TYPE_CANDIDATE:
                return 'candidate';
            case self::TYPE_COMPANY:
                return 'company';
            default:
                return null;
        }
    }

    public function companyProfile(): HasOne
    {
        return $this->hasOne(CompanyProfile::class);
    }

    public function candidateProfile(): HasOne
    {
        return $this->hasOne(CandidateProfile::class);
    }
}
