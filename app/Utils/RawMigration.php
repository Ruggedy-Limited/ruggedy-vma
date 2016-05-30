<?php

namespace App\Utils;

use Illuminate\Database\Migrations\Migration;
use DB;
use Exception;


abstract class RawMigration extends Migration
{
    /**
     * Get the RAW SQL to use for migrating the database
     *
     * @return string
     */
    abstract function getRawSqlMigration();

    /**
     * Execute a database migration
     */
    public function up()
    {
        DB::unprepared($this->getRawSqlMigration());
    }

    /**
     * Roll back a database migration: Not supported for raw SQL migrations
     * 
     * @throws Exception
     */
    public function down()
    {
        throw new Exception("Rollback of migrations is not supported!");
    }
}