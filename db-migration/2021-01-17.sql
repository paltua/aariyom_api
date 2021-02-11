ALTER TABLE `functional_units` 
ADD COLUMN `fu_title_url` VARCHAR(255) NOT NULL AFTER `fu_short_desc`;

UPDATE `functional_units` 
SET 
    `fu_title_url` = LOWER(REPLACE(`fu_title`, ' ', '-'));

ALTER TABLE `functional_units` 
ADD UNIQUE INDEX `fu_title_url_UNIQUE` (`fu_title_url` ASC);
