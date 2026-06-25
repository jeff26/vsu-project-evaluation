<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationScores extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'criterion_id',
        'rating'
    ];

    /**
     * The parent evaluation sheet this score belongs to.
     */
    public function evaluation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Evaluations::class);
    }

    /**
     * The specific rule/criterion being graded.
     */
    public function criterion(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(EvaluationCriteria::class, 'criterion_id');
    }
}
