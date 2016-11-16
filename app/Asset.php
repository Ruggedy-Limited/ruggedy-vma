<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Spark\Spark;

class Asset extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'assets';

    /**
     * Many-to-many relationship for Vulnerabilities
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function vulnerabilities()
    {
        return $this->belongsToMany(Vulnerability::class, 'assets_vulnerabilities')->withPivot('created_at');
    }

    /**
     * Many-to-many relationship for Software Information
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function softwareInformation()
    {
        return $this->belongsToMany(SoftwareInformation::class, 'asset_software_information')->withPivot('created_at');
    }

    /**
     * One-to-many relationship for Open Ports
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function openPorts()
    {
        return $this->hasMany(OpenPort::class);
    }
}