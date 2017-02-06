<?php

use App\Utils\RawMigration;

class FixSmallintBooleanColumns extends RawMigration
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

ALTER TABLE `api_tokens`
  CHANGE `transient` `transient` TINYINT(1)  UNSIGNED  NOT NULL  DEFAULT '0';
  
ALTER TABLE `notifications`
  CHANGE `read` `read` TINYINT(1)  UNSIGNED  NOT NULL  DEFAULT '0';
  
ALTER TABLE `users`
  CHANGE `uses_two_factor_auth` `uses_two_factor_auth` TINYINT(1)  UNSIGNED  NOT NULL  DEFAULT '0';
  
ALTER TABLE `files`
  CHANGE `processed` `processed` TINYINT(1)  UNSIGNED  NOT NULL  DEFAULT '0',
  CHANGE `deleted` `deleted` TINYINT(1)  UNSIGNED  NOT NULL  DEFAULT '0';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;

    }
}
