<?php

use App\Utils\RawMigration;

class AddNameAndDescriptionToFiles extends RawMigration
{
    public function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `files` 
DROP FOREIGN KEY `files_workspace_apps_fk`;

ALTER TABLE `files` 
DROP COLUMN `workspace_apps_id`,
ADD COLUMN `name` VARCHAR(255) NOT NULL COMMENT '' AFTER `id`,
ADD COLUMN `description` TEXT NULL DEFAULT NULL COMMENT '' AFTER `name`,
ADD COLUMN `workspace_app_id` INT(10) UNSIGNED NOT NULL COMMENT '' AFTER `user_id`,
DROP INDEX `files_workspace_apps_fk_idx` ,
ADD INDEX `files_workspace_apps_fk_idx` (`workspace_app_id` ASC)  COMMENT '';

ALTER TABLE `files` 
ADD CONSTRAINT `files_workspace_app_fk`
  FOREIGN KEY (`workspace_app_id`)
  REFERENCES `workspace_apps` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;
    }
}
