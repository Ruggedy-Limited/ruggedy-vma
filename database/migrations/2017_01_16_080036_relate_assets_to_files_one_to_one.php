<?php

use App\Utils\RawMigration;

class RelateAssetsToFilesOneToOne extends RawMigration
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

ALTER TABLE `assets` 
DROP FOREIGN KEY `assets_workspace_fk`;

ALTER TABLE `assets` 
DROP COLUMN `workspace_id`,
ADD COLUMN `file_id` INT(10) UNSIGNED NOT NULL COMMENT '' AFTER `last_boot`,
DROP INDEX `assets_workspace_fk_idx` ;

ALTER TABLE `assets` 
ADD CONSTRAINT `assets_file_fk`
  FOREIGN KEY (`file_id`)
  REFERENCES `files` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;
  
DROP TABLE `files_assets`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;

    }

}
