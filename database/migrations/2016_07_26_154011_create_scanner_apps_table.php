<?php

use App\Utils\RawMigration;

class CreateScannerAppsTable extends RawMigration
{
    function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `files` 
ADD COLUMN `scanner_app_id` INT(10) UNSIGNED NOT NULL COMMENT '' AFTER `asset_id`,
ADD INDEX `files_scanner_app_fk_idx` (`scanner_app_id` ASC)  COMMENT '';

ALTER TABLE `vulnerabilities` 
ADD COLUMN `file_id` INT(10) UNSIGNED NOT NULL COMMENT '' AFTER `asset_id`,
ADD INDEX `vulnerabilities_file_fk_idx` (`file_id` ASC)  COMMENT '';

ALTER TABLE `open_ports` 
ADD COLUMN `file_id` INT(10) UNSIGNED NOT NULL COMMENT '' AFTER `asset_id`,
ADD INDEX `open_ports_file_fk_idx` (`file_id` ASC)  COMMENT '';

CREATE TABLE IF NOT EXISTS `scanner_apps` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(45) NOT NULL COMMENT '',
  `description` VARCHAR(255) NULL DEFAULT NULL COMMENT '',
  `created_at` DATETIME NOT NULL COMMENT '',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '')
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

ALTER TABLE `files` 
ADD CONSTRAINT `files_scanner_app_fk`
  FOREIGN KEY (`scanner_app_id`)
  REFERENCES `scanner_apps` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `vulnerabilities` 
ADD CONSTRAINT `vulnerabilities_file_fk`
  FOREIGN KEY (`file_id`)
  REFERENCES `files` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `open_ports` 
ADD CONSTRAINT `open_ports_file_fk`
  FOREIGN KEY (`file_id`)
  REFERENCES `files` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;
    }
}
