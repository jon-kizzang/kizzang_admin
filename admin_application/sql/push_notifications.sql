Truncate notifications.queue;
ALTER TABLE `notifications`.`queue` 
CHANGE COLUMN `type` `type` ENUM('Day 2','Day 5','Multiplier','Pro Football','College Football','Top 5','Sweepstakes') NULL DEFAULT 'Day 2' ;
ALTER TABLE `notifications`.`queue` 
ADD COLUMN `notification_type` ENUM('individual','Batch') NULL DEFAULT 'Batch' AFTER `type`;