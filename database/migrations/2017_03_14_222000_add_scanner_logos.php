<?php

use App\Utils\RawMigration;

class AddScannerLogos extends RawMigration
{
    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

UPDATE `scanner_apps` SET `logo` = '/img/burp-logo.png' WHERE `name` = 'burp';
UPDATE `scanner_apps` SET `logo` = '/img/nessus-logo.png' WHERE `name` = 'nessus';
UPDATE `scanner_apps` SET `logo` = '/img/netsparker-logo.png' WHERE `name` = 'netsparker';
UPDATE `scanner_apps` SET `logo` = '/img/nmap-logo.png' WHERE `name` = 'nmap';
UPDATE `scanner_apps` SET `logo` = '/img/ruggedy-logo.png' WHERE `name` = 'ruggedy';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;
    }
}
