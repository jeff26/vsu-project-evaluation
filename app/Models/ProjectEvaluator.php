<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProjectEvaluator extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'role', 'project_thrusts_id'];

    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        // Handle Creation
        static::created(function ($evaluator) {
            User::updateOrCreate(
                ['email' => $evaluator->email], // Match by email
                [
                    'name' => $evaluator->name,
                    'password' => Hash::make('secret'),
                    'role' => 'evaluator',
                ]
            );
        });

        // Handle Updates (if name changes, update the User record)
        static::updated(function ($evaluator) {
            if ($evaluator->isDirty('name') || $evaluator->isDirty('email')) {
                User::where('email', $evaluator->getOriginal('email'))
                    ->update([
                        'name' => $evaluator->name,
                        'email' => $evaluator->email,
                    ]);
            }
        });

        // Handle Deletion (Optional: Remove the user account)
//        static::deleted(function ($evaluator) {
//            User::where('email', $evaluator->email)->delete();
//        });
    }

    // Relationship: The evaluator belongs to a Thrust (Department)
    public function thrust()
    {
        return $this->belongsTo(ProjectTrust::class, 'project_thrusts_id');
    }

    // Relationship: Link back to the user account
    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email');
    }
}
