<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SoftwareInformation extends Model
{
    protected $table = 'software_information';

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
