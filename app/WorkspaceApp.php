<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkspaceApp extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'workspace_apps';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function scannerApp()
    {
        return $this->belongsTo(ScannerApp::class);
    }
}