<?php

use App\Utils\RawMigration;

class AddNmapAndBurpScanners extends RawMigration
{
    function getRawSqlMigration()
    {
        return <<<SQL
INSERT INTO `scanner_apps` (`name`, `description`, `created_at`, `updated_at`) VALUES
('nmap', 'NMAP Port Scanner Utility', NOW(), NOW()),
('burp', 'Burp Web Vulnerability Scanner', NOW(), NOW());
SQL;

    }
}
