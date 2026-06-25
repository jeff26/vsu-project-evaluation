<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTrust extends Model
{
    /**
     * The table associated with the model.
     * * 🌟 CRITICAL: By default, Laravel expects a table named "project_trusts".
     * If your migration table is named "project_thrusts" (with an H),
     * you MUST define it explicitly below so it won't throw a SQL error.
     *
     * @var string
     */
    protected $table = 'project_thrusts'; // Change to 'project_trusts' if that's what you named your migration table!

    /**
     * The attributes that are mass assignable.
     * Allows you to use ProjectTrust::create(['name' => '...']) safely.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', // Maps to your string column name (e.g., 'name' or 'title')
    ];

    public function projects(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        // 1st param: The child model class
        // 2nd param: The foreign key column living on the 'projects' table
        return $this->hasMany(Project::class, 'project_thrusts_id');
    }
}
