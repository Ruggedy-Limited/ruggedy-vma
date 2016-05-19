<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Spark\Spark;


class Workspace extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'workspaces';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'user_id', 'project_id'];

    /**
     * Get the owner of the workspace.
     */
    public function owner()
    {
        return $this->belongsTo(Spark::userModel(), 'user_id');
    }

    /**
     * Get the Project that contains this Workspace
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}