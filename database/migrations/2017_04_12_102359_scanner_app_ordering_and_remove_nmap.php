<?php

use App\Utils\RawMigration;

class ScannerAppOrderingAndRemoveNmap extends RawMigration
{
    /**
     * @return string
     */
    public function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

DELETE FROM `scanner_apps` WHERE `name` = 'nmap';

ALTER TABLE `scanner_apps`
  ADD COLUMN `friendly_name` VARCHAR(255) NOT NULL COMMENT '' AFTER `name`,
  ADD COLUMN `order` INT(2) UNSIGNED NULL DEFAULT 0 COMMENT '' AFTER `logo`;
  
UPDATE `scanner_apps` SET `id` = 1, `friendly_name` = 'Ruggedy App', `order` = 1 WHERE `name` = 'ruggedy';
UPDATE `scanner_apps` SET `friendly_name` = 'Burp', `order` = 2 WHERE `name` = 'burp';
UPDATE `scanner_apps` SET `friendly_name` = 'Nessus', `order` = 3 WHERE `name` = 'nessus';
UPDATE `scanner_apps` SET `friendly_name` = 'Nexpose', `order` = 4 WHERE `name` = 'nexpose';
UPDATE `scanner_apps` SET `friendly_name` = 'Netsparker', `order` = 5 WHERE `name` = 'netsparker';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;

    }
}
