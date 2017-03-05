<?php

use App\Utils\RawMigration;

class AddNessusScanner extends RawMigration
{
    /**
     * @inheritdoc
     * @return string
     */
    public function getRawSqlMigration()
    {
        return <<<SQL
INSERT INTO `scanner_apps` (`name`, `description`, `created_at`, `updated_at`) VALUES
('nessus', 'Nessus Vulnerability Scanner', NOW(), NOW());
SQL;

    }
}
