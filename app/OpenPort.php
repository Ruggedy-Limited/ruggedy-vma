<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OpenPort extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'open_ports';

    /**
     * Many-to-many relationship for Files
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function files()
    {
        return $this->belongsToMany(File::class, 'files_vulnerabilities')->withPivot('created_at');
    }
}