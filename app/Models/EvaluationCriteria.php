<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationCriteria extends Model
{
    use HasFactory;

    // Explicitly define the table name since Laravel might guess 'evaluation_criterions'
    protected $table = 'evaluation_criteria';

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'weight_percentage'
    ];

    /**
     * The parent category this criterion belongs to.
     */
    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EvaluationCategories::class, 'category_id');
    }

    /**
     * All scores across the system given for this specific criterion.
     */
    public function scores(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EvaluationScores::class, 'criterion_id');
    }
}
