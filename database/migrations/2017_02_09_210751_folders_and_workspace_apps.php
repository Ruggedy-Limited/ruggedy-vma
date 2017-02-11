<?php

use App\Utils\RawMigration;

class FoldersAndWorkspaceApps extends RawMigration
{
    /**
     * @inheritdoc
     * @return string
     */
    public function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `workspaces` 
ADD COLUMN `description` TEXT NULL DEFAULT NULL COMMENT '' AFTER `name`;

ALTER TABLE `files`
DROP FOREIGN KEY `files_workspace_fk`,
DROP FOREIGN KEY `files_scanner_app_fk`,
DROP COLUMN `scanner_app_id`,
DROP COLUMN `workspace_id`,
DROP INDEX `files_scanner_app_fk_idx` ,
DROP INDEX `files_workspace_fk_idx`,
ADD COLUMN `workspace_apps_id` INT(10) UNSIGNED NOT NULL COMMENT '' AFTER `user_id`,
ADD INDEX `files_workspace_apps_fk_idx` (`workspace_apps_id` ASC)  COMMENT '',
ADD CONSTRAINT `files_workspace_apps_fk`
  FOREIGN KEY (`workspace_apps_id`)
  REFERENCES `workspace_apps` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `vulnerabilities` 
CHANGE COLUMN `id` `id` INT(10) UNSIGNED NOT NULL COMMENT '' ;

ALTER TABLE `scanner_apps` 
ADD COLUMN `logo` VARCHAR(255) NULL DEFAULT NULL COMMENT '' AFTER `description`;

ALTER TABLE `software_information` 
CHANGE COLUMN `id` `id` INT(10) UNSIGNED NOT NULL COMMENT '' ;

CREATE TABLE IF NOT EXISTS `workspace_apps` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(255) NOT NULL COMMENT '',
  `description` TEXT NULL DEFAULT NULL COMMENT '',
  `scanner_app_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `workspace_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `created_at` DATETIME NOT NULL COMMENT '',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `workspace_apps_scanner_app_fk_idx` (`scanner_app_id` ASC)  COMMENT '',
  INDEX `workspace_apps_workspace_fk_idx` (`workspace_id` ASC)  COMMENT '',
  CONSTRAINT `workspace_apps_scanner_app_fk`
    FOREIGN KEY (`scanner_app_id`)
    REFERENCES `scanner_apps` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `workspace_apps_workspace_fk`
    FOREIGN KEY (`workspace_id`)
    REFERENCES `workspaces` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `folders` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(255) NOT NULL COMMENT '',
  `description` TEXT NULL DEFAULT NULL COMMENT '',
  `workspace_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `user_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `created_at` DATETIME NOT NULL COMMENT '',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `folder_workspace_fk_idx` (`workspace_id` ASC)  COMMENT '',
  INDEX `folder_user_fk_idx` (`user_id` ASC)  COMMENT '',
  CONSTRAINT `folder_workspace_fk`
    FOREIGN KEY (`workspace_id`)
    REFERENCES `ruggedy`.`workspaces` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `folder_user_fk`
    FOREIGN KEY (`user_id`)
    REFERENCES `ruggedy`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE `folders_vulnerabilities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `folder_id` int(10) unsigned NOT NULL,
  `vulnerability_id` int(10) unsigned NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`),
  CONSTRAINT `folders_vulnerabilities_file_fk` FOREIGN KEY (`folder_id`)
    REFERENCES `folders` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `folders_vulnerabilities_vulnerability_id` FOREIGN KEY (`vulnerability_id`)
    REFERENCES `vulnerabilities` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;
    }

}
