<?php

use App\Utils\RawMigration;


class CreatePermissionsTables extends RawMigration
{
    /**
     * @inheritdoc
     * @return string
     */
    function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `components` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(45) NOT NULL COMMENT '',
  `class_name` VARCHAR(100) NOT NULL COMMENT 'The class used to store row instances in the application',
  `created_at` DATETIME NOT NULL COMMENT '',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '')
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE TABLE IF NOT EXISTS `component_permissions` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `component_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `instance_id` INT(10) UNSIGNED NOT NULL COMMENT 'The id of the instance of the relevant component',
  `permission` ENUM('r', 'rw') NOT NULL DEFAULT 'r' COMMENT '',
  `user_id` INT(10) UNSIGNED COMMENT '',
  `granted_by` INT(10) UNSIGNED NOT NULL COMMENT '',
  `created_at` DATETIME NOT NULL COMMENT '',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `component_permissions_component_fk_idx` (`component_id` ASC)  COMMENT '',
  INDEX `component_permissions_user_fk_idx` (`user_id` ASC)  COMMENT '',
  INDEX `component_permissions_user_granted_fk_idx` (`granted_by` ASC)  COMMENT '',
  CONSTRAINT `component_permissions_component_fk`
    FOREIGN KEY (`component_id`)
    REFERENCES `components` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `component_permissions_user_fk`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `component_permissions_user_granted_fk`
    FOREIGN KEY (`granted_by`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

INSERT INTO `components` (`name`, `class_name`, `created_at`, `updated_at`) VALUES
('User Account', 'User', NOW(), NOW()),
('Project', 'Project', NOW(), NOW()),
('Workspace', 'Workspace', NOW(), NOW()),
('Workspace App', 'WorkspaceApp', NOW(), NOW()),
('File', 'File', NOW(), NOW()),
('Asset', 'Asset', NOW(), NOW()),
('Scanner App', 'ScannerApp', NOW(), NOW()),
('Event', 'Event', NOW(), NOW()),
('Rule', 'Rule', NOW(), NOW());

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;
    }
}
