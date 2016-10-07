<?php

use App\Utils\RawMigration;

class CreateAssetsVulnerabilitiesJoinTable extends RawMigration
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

ALTER TABLE `vulnerabilities`
  DROP INDEX `vulnerabilities_asset_fk_idx`,
  DROP FOREIGN KEY `vulnerabilities_asset_fk`,
  DROP COLUMN `asset_id`;

CREATE TABLE IF NOT EXISTS `assets_vulnerabilities` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `asset_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `vulnerability_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`) COMMENT '',
  UNIQUE KEY `asset_vulnerability_idx` (`asset_id`, `vulnerability_id`),
  CONSTRAINT `assets_vulnerabilities_asset_fk` FOREIGN KEY (`asset_id`)
    REFERENCES `assets` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION ,
  CONSTRAINT `assets_vulnerabilities_vulnerability_fk` FOREIGN KEY (`vulnerability_id`)
    REFERENCES `vulnerabilities` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION 
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE utf8_general_ci;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;

    }
}
