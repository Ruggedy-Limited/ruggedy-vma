<?php

use App\Utils\RawMigration;


class ChangeKeysToInt10 extends RawMigration
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

ALTER TABLE `projects` 
DROP FOREIGN KEY `projects_fk_user`;

ALTER TABLE `workspaces` 
DROP FOREIGN KEY `workspaces_fk_user`,
DROP FOREIGN KEY `workspaces_fk_project`;

ALTER TABLE `announcements` 
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NOT NULL COMMENT '' ,
ADD INDEX `announcements_fk_user_idx` (`user_id` ASC)  COMMENT '';

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
CHANGE COLUMN `created_by` `created_by` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT '' ,
ADD INDEX `notifications_fk_user_created_idx` (`created_by` ASC)  COMMENT '';

ALTER TABLE `projects` 
ADD INDEX `projects_fk_user` (`user_id` ASC)  COMMENT '',
DROP INDEX `projects_fk_user` ;

ALTER TABLE `subscriptions` 
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NOT NULL COMMENT '' ,
ADD INDEX `subscriptions_fk_user_id_idx` (`user_id` ASC)  COMMENT '';

ALTER TABLE `team_subscriptions` 
CHANGE COLUMN `team_id` `team_id` INT(10) UNSIGNED NOT NULL COMMENT '' ,
ADD INDEX `team_subscriptions_fk_team_idx` (`team_id` ASC)  COMMENT '';

ALTER TABLE `teams` 
CHANGE COLUMN `owner_id` `owner_id` INT(10) UNSIGNED NOT NULL COMMENT '' ;

ALTER TABLE `workspaces` 
ADD INDEX `workspaces_fk_user` (`user_id` ASC)  COMMENT '',
ADD INDEX `workspaces_fk_project` (`project_id` ASC)  COMMENT '',
DROP INDEX `workspaces_fk_project` ,
DROP INDEX `workspaces_fk_user` ;

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

ALTER TABLE `projects` 
ADD CONSTRAINT `projects_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`);

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

ALTER TABLE `teams` 
ADD CONSTRAINT `teams_fk_owner`
  FOREIGN KEY (`owner_id`)
  REFERENCES `users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `workspaces` 
ADD CONSTRAINT `workspaces_fk_project`
  FOREIGN KEY (`project_id`)
  REFERENCES `projects` (`id`),
ADD CONSTRAINT `workspaces_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;
    }
}
