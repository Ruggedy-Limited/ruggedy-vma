<?php

use App\Utils\RawMigration;


class CreateForeignKeysForDoctrine extends RawMigration
{
    /**
     * @inheritdoc
     */
    public function getRawSqlMigration()
    {
        return <<<SQL
SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

ALTER TABLE `announcements` 
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NOT NULL COMMENT '' ;

ALTER TABLE `api_tokens` 
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NOT NULL COMMENT '' ;

ALTER TABLE `invitations` 
CHANGE COLUMN `team_id` `team_id` INT(10) UNSIGNED NOT NULL COMMENT '' ,
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT '' ;

ALTER TABLE `invoices` 
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT '' ,
CHANGE COLUMN `team_id` `team_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT '' ;

ALTER TABLE `notifications` 
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NOT NULL COMMENT '' ,
CHANGE COLUMN `created_by` `created_by` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT '' ;

ALTER TABLE `subscriptions` 
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NOT NULL COMMENT '' ;

ALTER TABLE `team_subscriptions` 
CHANGE COLUMN `team_id` `team_id` INT(10) UNSIGNED NOT NULL COMMENT '' ;

ALTER TABLE `team_users` 
CHANGE COLUMN `team_id` `team_id` INT(10) UNSIGNED NOT NULL COMMENT '' ,
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NOT NULL COMMENT '' ;

ALTER TABLE `teams` 
CHANGE COLUMN `owner_id` `owner_id` INT(10) UNSIGNED NOT NULL COMMENT '' ;

ALTER TABLE `announcements` 
ADD CONSTRAINT `announcements_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `api_tokens` 
ADD CONSTRAINT `api_tokens_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `invitations` 
ADD CONSTRAINT `invitations_fk_team`
  FOREIGN KEY (`team_id`)
  REFERENCES `teams` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `invitations_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `invoices` 
ADD CONSTRAINT `invoices_fk_team`
  FOREIGN KEY (`team_id`)
  REFERENCES `teams` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `invoices_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `notifications` 
ADD CONSTRAINT `notifications_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `notifications_fk_user_created`
  FOREIGN KEY (`created_by`)
  REFERENCES `users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `subscriptions` 
ADD CONSTRAINT `subscriptions_fk_user_id`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `team_subscriptions` 
ADD CONSTRAINT `team_subscriptions_fk_team`
  FOREIGN KEY (`team_id`)
  REFERENCES `teams` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `team_users` 
ADD CONSTRAINT `team_users_fk_team`
  FOREIGN KEY (`team_id`)
  REFERENCES `teams` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `team_users_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `teams` 
ADD CONSTRAINT `teams_fk_owner`
  FOREIGN KEY (`owner_id`)
  REFERENCES `users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;
    }
}
