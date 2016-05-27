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

ALTER TABLE `ruggedy`.`projects` 
DROP FOREIGN KEY `projects_user_id_foreign`;

ALTER TABLE `ruggedy`.`workspaces` 
DROP FOREIGN KEY `workspaces_project_id_foreign`,
DROP FOREIGN KEY `workspaces_user_id_foreign`;

ALTER TABLE `ruggedy`.`announcements` 
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NOT NULL COMMENT '' ,
ADD INDEX `announcements_fk_user_idx` (`user_id` ASC)  COMMENT '';

ALTER TABLE `ruggedy`.`api_tokens` 
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NOT NULL COMMENT '' ;

ALTER TABLE `ruggedy`.`invitations` 
CHANGE COLUMN `team_id` `team_id` INT(10) UNSIGNED NOT NULL COMMENT '' ,
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT '' ;

ALTER TABLE `ruggedy`.`invoices` 
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT '' ,
CHANGE COLUMN `team_id` `team_id` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT '' ;

ALTER TABLE `ruggedy`.`notifications` 
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NOT NULL COMMENT '' ,
CHANGE COLUMN `created_by` `created_by` INT(10) UNSIGNED NULL DEFAULT NULL COMMENT '' ,
ADD INDEX `notifications_fk_user_created_idx` (`created_by` ASC)  COMMENT '';

ALTER TABLE `ruggedy`.`projects` 
DROP INDEX `projects_user_id_foreign` ,
ADD INDEX `projects_fk_user` (`user_id` ASC)  COMMENT '';

ALTER TABLE `ruggedy`.`subscriptions` 
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NOT NULL COMMENT '' ,
ADD INDEX `subscriptions_fk_user_id_idx` (`user_id` ASC)  COMMENT '';

ALTER TABLE `ruggedy`.`team_subscriptions` 
CHANGE COLUMN `team_id` `team_id` INT(10) UNSIGNED NOT NULL COMMENT '' ,
ADD INDEX `team_subscriptions_fk_team_idx` (`team_id` ASC)  COMMENT '';

ALTER TABLE `ruggedy`.`team_users` 
CHANGE COLUMN `team_id` `team_id` INT(10) UNSIGNED NOT NULL COMMENT '' ,
CHANGE COLUMN `user_id` `user_id` INT(10) UNSIGNED NOT NULL COMMENT '' ,
ADD INDEX `fk_user_id_idx` (`user_id` ASC)  COMMENT '';

ALTER TABLE `ruggedy`.`teams` 
CHANGE COLUMN `owner_id` `owner_id` INT(10) UNSIGNED NOT NULL COMMENT '' ;

ALTER TABLE `ruggedy`.`workspaces` 
DROP INDEX `workspaces_user_id_foreign` ,
ADD INDEX `workspaces_fk_user` (`user_id` ASC)  COMMENT '',
DROP INDEX `workspaces_project_id_foreign` ,
ADD INDEX `workspaces_fk_project` (`project_id` ASC)  COMMENT '';

ALTER TABLE `ruggedy`.`announcements` 
ADD CONSTRAINT `announcements_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `ruggedy`.`users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `ruggedy`.`api_tokens` 
ADD CONSTRAINT `api_tokens_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `ruggedy`.`users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `ruggedy`.`invitations` 
ADD CONSTRAINT `invitations_fk_team`
  FOREIGN KEY (`team_id`)
  REFERENCES `ruggedy`.`teams` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `invitations_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `ruggedy`.`users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `ruggedy`.`invoices` 
ADD CONSTRAINT `invoices_fk_team`
  FOREIGN KEY (`team_id`)
  REFERENCES `ruggedy`.`teams` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `invoices_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `ruggedy`.`users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `ruggedy`.`notifications` 
ADD CONSTRAINT `notifications_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `ruggedy`.`users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `notifications_fk_user_created`
  FOREIGN KEY (`created_by`)
  REFERENCES `ruggedy`.`users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `ruggedy`.`projects` 
ADD CONSTRAINT `projects_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `ruggedy`.`users` (`id`);

ALTER TABLE `ruggedy`.`subscriptions` 
ADD CONSTRAINT `subscriptions_fk_user_id`
  FOREIGN KEY (`user_id`)
  REFERENCES `ruggedy`.`users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `ruggedy`.`team_subscriptions` 
ADD CONSTRAINT `team_subscriptions_fk_team`
  FOREIGN KEY (`team_id`)
  REFERENCES `ruggedy`.`teams` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `ruggedy`.`team_users` 
ADD CONSTRAINT `team_users_fk_team`
  FOREIGN KEY (`team_id`)
  REFERENCES `ruggedy`.`teams` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `team_users_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `ruggedy`.`users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `ruggedy`.`teams` 
ADD CONSTRAINT `teams_fk_owner`
  FOREIGN KEY (`owner_id`)
  REFERENCES `ruggedy`.`users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `ruggedy`.`workspaces` 
ADD CONSTRAINT `workspaces_fk_project`
  FOREIGN KEY (`project_id`)
  REFERENCES `ruggedy`.`projects` (`id`),
ADD CONSTRAINT `workspaces_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `ruggedy`.`users` (`id`);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;
    }
}
