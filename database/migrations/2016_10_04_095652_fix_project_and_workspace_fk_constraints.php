<?php

use App\Utils\RawMigration;

class FixProjectAndWorkspaceFkConstraints extends RawMigration
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

ALTER TABLE `projects`
DROP FOREIGN KEY `projects_fk_user`;

ALTER TABLE `workspaces`
DROP FOREIGN KEY `workspaces_fk_user`,
DROP FOREIGN KEY `workspaces_fk_project`;

ALTER TABLE `projects` 
ADD INDEX `projects_fk_user` (`user_id` ASC)  COMMENT '',
DROP INDEX `projects_fk_user` ;

ALTER TABLE `workspaces` 
ADD INDEX `workspaces_fk_user` (`user_id` ASC)  COMMENT '',
ADD INDEX `workspaces_fk_project` (`project_id` ASC)  COMMENT '',
DROP INDEX `workspaces_fk_project` ,
DROP INDEX `workspaces_fk_user` ;

ALTER TABLE `projects` 
ADD CONSTRAINT `projects_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

ALTER TABLE `workspaces` 
ADD CONSTRAINT `workspaces_fk_project`
  FOREIGN KEY (`project_id`)
  REFERENCES `projects` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION,
ADD CONSTRAINT `workspaces_fk_user`
  FOREIGN KEY (`user_id`)
  REFERENCES `users` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
SQL;
    }
}