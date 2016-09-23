<?php

use App\Utils\RawMigration;

class FieldTypeAndPropertyTweaks extends RawMigration
{
    function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `ruggedy_blank`.`files` 
CHANGE COLUMN `size` `size` INT(16) UNSIGNED NOT NULL COMMENT '' ;

ALTER TABLE `ruggedy_blank`.`vulnerabilities` 
CHANGE COLUMN `severity` `severity` DECIMAL(2,2) UNSIGNED NULL DEFAULT NULL COMMENT '' ,
CHANGE COLUMN `pci_severity` `pci_severity` DECIMAL(2,2) UNSIGNED NULL DEFAULT NULL COMMENT '' ,
CHANGE COLUMN `cvss_score` `cvss_score` DECIMAL(2,2) UNSIGNED NULL DEFAULT NULL COMMENT '' ,
CHANGE COLUMN `http_status_code` `http_status_code` INT(4) UNSIGNED NULL DEFAULT NULL COMMENT '' ;

ALTER TABLE `ruggedy_blank`.`vulnerability_reference_codes` 
CHANGE COLUMN `value` `value` TEXT NOT NULL COMMENT '' ;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;

    }
}
