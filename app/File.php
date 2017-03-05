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

    /**
     * Many-to-many relationship to Software Information
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function softwareInformation()
    {
        return $this->belongsToMany(SoftwareInformation::class, 'files_software_information')->withPivot('created_at');
    }

    /**
     * Many-to-many relationship to Open Ports
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function openPorts()
    {
        return $this->belongsToMany(OpenPort::class, 'files_open_ports')->withPivot('created_at');
    }

    /**
     * Many-to-many relationship to Audits
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function audits()
    {
        return $this->belongsToMany(Audit::class, 'files_audits')->withPivot('created_at');
    }
}