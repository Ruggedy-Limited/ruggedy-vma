<?php

use App\Utils\RawMigration;

class RemoveFileFkFromComments extends RawMigration
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

ALTER TABLE `comments` 
DROP FOREIGN KEY `comments_file_fk`;

ALTER TABLE `comments` 
DROP COLUMN `file_id`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;

	}
}
