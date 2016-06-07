<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Spark\Spark;


class Project extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projects';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * Get the owner of the project.
     */
    public function owner()
    {
        return $this->belongsTo(Spark::userModel(), 'user_id');
    }

    /**
     * Get the Workspaces contained in the Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workspaces() {
        return $this->hasMany(Workspace::class);
    }
}