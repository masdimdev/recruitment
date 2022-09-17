<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class JobApplication extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_PENDING = 'PENDING';
    const STATUS_SHORTLISTED = 'SHORTLISTED';
    const STATUS_INTERVIEW = 'INTERVIEW';
    const STATUS_HIRED = 'HIRED';
    const STATUS_REJECTED = 'REJECTED';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cover_letter',
        'application_status',
        'job_vacancy_id',
        'candidate_id',
    ];

    /**
     * Scope a query to only include data of a given filter.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request              $request
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $query, Request $request): Builder
    {
        if ($request->has('status')) {
            $status = preg_replace("/[^A-Za-z,_]/", '', $request->get('status'));
            $statuses = explode(',', $status);

            $query->whereIn('application_status', $statuses);
        }

        return $query;
    }

    public function jobVacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(CandidateProfile::class, 'candidate_id');
    }
}
