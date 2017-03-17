<?php

use App\Utils\RawMigration;

class AddNetsparkerScanner extends RawMigration
{
    /**
     * @inheritdoc
     * @return string
     */
    public function getRawSqlMigration()
    {
        return <<<SQL
INSERT INTO `scanner_apps` (`name`, `description`, `created_at`, `updated_at`) VALUES
('netsparker', 'Netsparker Web Vulnerability Scanner', NOW(), NOW());
SQL;

    }
}
