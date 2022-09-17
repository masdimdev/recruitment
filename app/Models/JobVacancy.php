<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class JobVacancy extends Model
{
    use HasFactory, SoftDeletes;

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
        if ($request->has('search')) {
            $query->where(function (Builder $query) use ($request) {
                $query->where('name', 'LIKE', "%%{$request->get('q')}%%")
                    ->orWhere('description', 'LIKE', "%%{$request->get('q')}%%");
            });
        }

        if ($request->has('type')) {
            $type = preg_replace("/[^A-Za-z,_]/", '', $request->get('type'));
            $types = explode(',', $type);

            $query->whereIn('job_type', $types);
        }

        if ($request->has('category')) {
            $categoryId = preg_replace("/[^0-9,]/", '', $request->get('category'));
            $categoryIds = explode(',', $categoryId);

            $query->whereIn('job_category_id', $categoryIds);
        }

        return $query;
    }

    public function jobCategory(): BelongsTo
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(CompanyProfile::class, 'company_id');
    }

}
