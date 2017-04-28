<?php

use App\Utils\RawMigration;

class AddNexposeScanner extends RawMigration
{
    /**
     * @inheritdoc
     * @return string
     */
    public function getRawSqlMigration()
    {
        return <<<SQL
INSERT INTO `scanner_apps` (`name`, `description`, `logo`, `created_at`, `updated_at`) VALUES
('nexpose', 'Nexpose Vulnerability Scanner', '/img/nexpose-logo.png', NOW(), NOW());
SQL;

    }
}

