<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Spark\Spark;

class File extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'files';

    /**
     * Many-to-many relationship to Assets
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    /**
     * Many-to-many relationship to Vulnerabilities
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function vulnerabilities()
    {
        return $this->belongsToMany(Vulnerability::class, 'files_vulnerabilities')->withPivot('created_at');
    }
}