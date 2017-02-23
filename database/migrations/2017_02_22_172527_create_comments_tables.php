<?php

use App\Utils\RawMigration;

class CreateCommentsTables extends RawMigration
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

CREATE TABLE IF NOT EXISTS `comments` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '',
  `content` TEXT NOT NULL COMMENT '',
  `status` TINYINT(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '',
  `user_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `file_id` INT(10) UNSIGNED NOT NULL COMMENT '',
  `vulnerability_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT '',
  `created_at` DATETIME NOT NULL COMMENT '',
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '',
  PRIMARY KEY (`id`)  COMMENT '',
  INDEX `comments_user_fk_idx` (`user_id` ASC)  COMMENT '',
  INDEX `comments_file_fk_idx` (`file_id` ASC)  COMMENT '',
  INDEX `comments_vulnerability_fk_idx` (`vulnerability_id` ASC)  COMMENT '',
  CONSTRAINT `comments_user_fk`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `comments_file_fk`
    FOREIGN KEY (`file_id`)
    REFERENCES `files` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `comments_vulnerability_fk`
    FOREIGN KEY (`vulnerability_id`)
    REFERENCES `vulnerabilities` (`id`)
    ON DELETE CASCADE
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
