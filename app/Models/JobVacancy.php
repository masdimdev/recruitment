<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobVacancy extends Model
{
    use HasFactory;

    const TYPE_INTERNSHIP = 'INTERNSHIP';
    const TYPE_FULL_TIME = 'FULL_TIME';
    const TYPE_PART_TIME = 'PART_TIME';
    const TYPE_FREELANCE = 'FREELANCE';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'job_type',
        'job_category_id',
        'company_id',
    ];

    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class, 'company_id');
    }
}
