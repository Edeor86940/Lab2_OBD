-- 1) Усі поля з таблиці Students
SELECT * FROM Students;

-- 2) Лише потрібні стовпці (student_id, full_name)
SELECT student_id, full_name FROM Students;

-- 3) Фільтр за умовою (студенти з ID > 10)
SELECT student_id, full_name 
FROM Students
WHERE student_id > 10;

-- 4) Комбінована умова + сортування за датою народження (від найстарших)
SELECT student_id, full_name, date_of_birth
FROM Students
WHERE student_id >= 1 AND student_id <= 10
ORDER BY date_of_birth ASC;

-- 5) Обмеження кількості рядків (останні 3 додані студенти)
SELECT * FROM Students
ORDER BY student_id DESC 
LIMIT 3;

-- 6) Студент + сесія + тренер (з'єднання 3-х таблиць)
SELECT s.full_name AS student_name,
       c.full_name AS coach_name,
       c.specialization,
       sess.date_of_session
FROM Session sess
JOIN Students s ON s.student_id = sess.student_id
JOIN Coachs c   ON c.coach_id = sess.coach_id
ORDER BY sess.date_of_session DESC;

-- 7) Тренери, у яких є сесії зі студентами, народженими після 2010 року (підзапит IN)
SELECT DISTINCT c.coach_id, c.full_name, c.specialization
FROM Coachs c
WHERE c.coach_id IN (
  SELECT sess.coach_id 
  FROM Session sess
  JOIN Students s ON s.student_id = sess.student_id
  WHERE s.date_of_birth >= '2010-01-01'
);

-- 8) Студенти, які записані set хоча б на одну сесію (підзапит EXISTS)
SELECT s.student_id, s.full_name
FROM Students s
WHERE EXISTS (
  SELECT 1 FROM Session sess
  WHERE sess.student_id = s.student_id
);

-- 9) Тренери, у яких є заповнені сесії в базі (підзапит з агрегатною функцією COUNT)
SELECT c.coach_id, c.full_name, c.specialization
FROM Coachs c
WHERE (
  SELECT COUNT(*) 
  FROM Session sess
  WHERE sess.coach_id = c.coach_id
) >= 1;

-- 10) Тренери, у яких ВСІ проведені сесії були виключно зі повнолітніми студентами 
--     (тобто відсутні сесії зі студентами, молодше 18 років на момент проведення сесії, через NOT EXISTS)
SELECT c.coach_id, c.full_name
FROM Coachs c
WHERE EXISTS (SELECT 1 FROM Session sess WHERE sess.coach_id = c.coach_id)
  AND NOT EXISTS (
    SELECT 1 
    FROM Session sess
    JOIN Students s ON s.student_id = sess.student_id
    WHERE sess.coach_id = c.coach_id
      AND s.date_of_birth > DATE_SUB(sess.date_of_session, INTERVAL 18 YEAR)
);