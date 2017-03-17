<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScannerApp extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'scanner_apps';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function workspaceApps()
    {
        return $this->hasMany(WorkspaceApp::class);
    }
}