CREATE TABLE `Coachs` (
  `coach_id` integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `full_name` varchar(255) NOT NULL,
  `specialization` varchar(255),
  `adrres` varchar(255)
);

CREATE TABLE `students` (
  `student_id` integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `full_name` varchar(255),
  `date_of_birth` date
);

CREATE TABLE `Session` (
  `session_id` integer NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `student_id` integer NOT NULL,
  `coach_id` integer NOT NULL,
  `date_of_session` date
);

ALTER TABLE `Session` ADD FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`);
ALTER TABLE `Session` ADD FOREIGN KEY (`coach_id`) REFERENCES `Coachs` (`coach_id`);

CREATE INDEX `idx_session_student` ON `Session` (`student_id`);
CREATE INDEX `idx_session_coach` ON `Session` (`coach_id`);


START TRANSACTION;
UPDATE Coachs 
SET specialization = 'Реабілітація та ЛФК' 
WHERE coach_id = 1;
UPDATE Coachs 
SET adrres = 'вул. Спортивна, 10' 
WHERE coach_id = 1;
COMMIT


-- Disable foreign key checks for INSERT
SET FOREIGN_KEY_CHECKS = 0;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;
