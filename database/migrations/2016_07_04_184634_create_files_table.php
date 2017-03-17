<?php

use App\Utils\RawMigration;

class CreateFilesTable extends RawMigration
{
    /**
     * @inheritdoc
     */
    function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

CREATE TABLE IF NOT EXISTS `files` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `path` VARCHAR(255) NOT NULL COMMENT '',
  `format` ENUM('xml', 'csv', 'json') NOT NULL COMMENT '',
  `size` FLOAT(14,2) NOT NULL COMMENT '',
  `user_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `workspace_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `asset_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT '',
  `processed` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '',
  `deleted` TINYINT(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '',
  `created_at` DATETIME NOT NULL COMMENT '',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `files_user_fk_idx` (`user_id` ASC)  COMMENT '',
  INDEX `files_workspace_fk_idx` (`workspace_id` ASC)  COMMENT '',
  INDEX `files_asset_fk_idx` (`asset_id` ASC)  COMMENT '',
  CONSTRAINT `files_user_fk`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `files_workspace_fk`
    FOREIGN KEY (`workspace_id`)
    REFERENCES `workspaces` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `files_asset_fk`
    FOREIGN KEY (`asset_id`)
    REFERENCES `assets` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;
    }

}
