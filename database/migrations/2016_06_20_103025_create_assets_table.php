<?php

use App\Utils\RawMigration;


class CreateAssetsTable extends RawMigration
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

CREATE TABLE IF NOT EXISTS `assets` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `name` VARCHAR(255) NOT NULL COMMENT '',
  `cpe` VARCHAR(255) NULL DEFAULT NULL COMMENT '',
  `vendor` VARCHAR(45) NULL DEFAULT NULL COMMENT '',
  `ip_address_v4` VARCHAR(12) NULL DEFAULT NULL COMMENT '',
  `ip_address_v6` VARCHAR(45) NULL DEFAULT NULL COMMENT '',
  `hostname` VARCHAR(255) NULL DEFAULT NULL COMMENT '',
  `mac_address` VARCHAR(25) NULL DEFAULT NULL COMMENT '',
  `os_version` VARCHAR(20) NULL DEFAULT NULL COMMENT '',
  `workspace_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `user_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `created_at` DATETIME NOT NULL COMMENT '',
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `assets_workspace_fk_idx` (`workspace_id` ASC)  COMMENT '',
  INDEX `assets_user_fk_idx` (`user_id` ASC)  COMMENT '',
  CONSTRAINT `assets_workspace_fk`
    FOREIGN KEY (`workspace_id`)
    REFERENCES `workspaces` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `assets_user_fk`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
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
