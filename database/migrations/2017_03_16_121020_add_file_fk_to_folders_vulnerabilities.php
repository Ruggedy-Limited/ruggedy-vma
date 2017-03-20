<?php

use App\Utils\RawMigration;

class AddFileFkToFoldersVulnerabilities extends RawMigration
{
    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `folders_vulnerabilities` 
  ADD COLUMN `file_id` INT(10) UNSIGNED NOT NULL COMMENT '' AFTER `vulnerability_id`,
  ADD INDEX `folders_vulnerabilities_file_fk_idx` (`file_id` ASC)  COMMENT '';
  
ALTER TABLE `folders_vulnerabilities` 
ADD CONSTRAINT `folders_vulnerabilities_file_fk`
  FOREIGN KEY (`file_id`)
  REFERENCES `files` (`id`)
  ON DELETE CASCADE
  ON UPDATE NO ACTION;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;
    }
}
