<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationCategories extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'weight_percentage'
    ];

    /**
     * The specific criteria/questions that belong to this category.
     */
    public function criteria(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(EvaluationCriteria::class, 'category_id');
    }
}
