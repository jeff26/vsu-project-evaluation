<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /**
     * The attributes that are mass assignable.
     * * This tells Laravel it's safe to insert data into these columns
     * during a create or update operation from our controller.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'project_thrusts_id',
        'unit_center',
        'attachment_path',
        'label'
    ];

    /**
     * Optional Safeguard: Default attribute values
     * * If a project is registered without a specific center unit,
     * this automatically assigns a default string at the model level.
     */
    protected $attributes = [
        'unit_center' => 'Unassigned Center',
    ];

    public function thrust(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        // 1st param: The target model class name (ProjectTrust)
        // 2nd param: The foreign key column living on your 'projects' table
        return $this->belongsTo(ProjectTrust::class, 'project_thrusts_id');
    }

    public function members(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProjectMember::class, 'project_id');
    }

    public function evaluators(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        // Uses standard project_user pivot mapping definitions
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id')
            ->where('role', 'evaluator');
    }

    public function evaluations(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Evaluations::class);
    }

    public function evaluationScores()
    {
        // A project has many scores through evaluations
        return $this->hasManyThrough(
            EvaluationScores::class, // The final table (scores)
            Evaluations::class,      // The intermediate table (evaluations)
            'project_id',           // Foreign key on evaluations table
            'evaluation_id',        // Foreign key on evaluation_scores table
            'id',                   // Local key on projects table
            'id'                    // Local key on evaluations table
        )->where('evaluations.status', '=', 'completed');
    }
}
