<?php

use App\Utils\RawMigration;

class IncreaseOsVersionLength extends RawMigration
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

ALTER TABLE `assets`
  CHANGE `os_version` `os_version` VARCHAR(255) NULL DEFAULT NULL COMMENT '';
  
ALTER TABLE `vulnerabilities`
  CHANGE `generic_output` `generic_output` LONGTEXT NULL DEFAULT NULL COMMENT '';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;

    }
}
