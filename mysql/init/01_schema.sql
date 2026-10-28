CREATE TABLE `Coachs` (
  `coach_id` integer PRIMARY KEY,
  `full_name` varchar(255) NOT NULL,
  `specialization` varchar(255),
  `adrres` varchar(255)
);

CREATE TABLE `Students` (
  `student_id` integer PRIMARY KEY,
  `full_name` varchar(255),
  `date_of_birth` date
);

CREATE TABLE `Session` (
  `session_id` integer PRIMARY KEY,
  `student_id` integer NOT NULL,
  `coach_id` integer NOT NULL,
  `date_of_session` date
);

ALTER TABLE `Session` ADD FOREIGN KEY (`student_id`) REFERENCES `Students` (`student_id`);

ALTER TABLE `Session` ADD FOREIGN KEY (`coach_id`) REFERENCES `Coachs` (`coach_id`);

CREATE INDEX `idx_session_student` ON `Session` (`student_id`);
CREATE INDEX `idx_session_coach` ON `Session` (`coach_id`);

-- Disable foreign key checks for INSERT
SET FOREIGN_KEY_CHECKS = 0;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;