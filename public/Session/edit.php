<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/helpers.php';


$id = (int)($_GET['id'] ?? 0);

$stmt = db()->prepare('SELECT * FROM Session WHERE session_id = ?');
$stmt->execute([$id]);
$session = $stmt->fetch();

if (!$session) {
    http_response_code(404);
    exit('Тренування не знайдено.');
}

$coachesStmt = db()->query("SELECT coach_id, full_name FROM Coachs");
$coaches = $coachesStmt->fetchAll();

$studentsStmt = db()->query("SELECT student_id, full_name FROM students");
$students = $studentsStmt->fetchAll();

$data = [
    'student_id'      => (string)$session['student_id'],
    'coach_id'        => (string)$session['coach_id'],
    'date_of_session' => $session['date_of_session'] ?? ''
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['student_id']      = trim($_POST['student_id'] ?? '');
    $data['coach_id']        = trim($_POST['coach_id'] ?? '');
    $data['date_of_session'] = trim($_POST['date_of_session'] ?? '');

    if ($data['student_id'] === '') {
        $errors['student_id'] = 'Оберіть студента.';
    }
    if ($data['coach_id'] === '') {
        $errors['coach_id'] = 'Оберіть тренера.';
    }

    if (empty($errors)) {
        $stmt = db()->prepare(
            'UPDATE Session SET student_id = :student_id, coach_id = :coach_id, date_of_session = :s_date 
             WHERE session_id = :id'
        );
        $stmt->execute([
            ':student_id' => (int)$data['student_id'],
            ':coach_id'   => (int)$data['coach_id'],
            ':s_date'     => $data['date_of_session'] !== '' ? $data['date_of_session'] : null,
            ':id'         => $id
        ]);

        flash('success', 'Тренування успішно оновлено.');
        redirect('index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head><meta charset="UTF-8"><title>Редагувати тренування</title></head>
<body>
    <h1>Редагувати тренування</h1>
    <form method="post" novalidate>
        <!-- Вибір студента -->
        <label>Студент:</label><br>
        <select name="student_id" required>
            <option value="">— оберіть студента —</option>
            <?php foreach ($students as $student): ?>
                <option value="<?= (int)$student['student_id'] ?>" 
                    <?= (string)$student['student_id'] === (string)$data['student_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($student['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['student_id'])): ?><div style="color:red;"><?= $errors['student_id'] ?></div><?php endif; ?><br><br>

        <!-- Вибір тренера -->
        <label>Тренер:</label><br>
        <select name="coach_id" required>
            <option value="">— оберіть тренера —</option>
            <?php foreach ($coaches as $coach): ?>
                <option value="<?= (int)$coach['coach_id'] ?>" 
                    <?= (string)$coach['coach_id'] === (string)$data['coach_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($coach['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (isset($errors['coach_id'])): ?><div style="color:red;"><?= $errors['coach_id'] ?></div><?php endif; ?><br><br>

        <label>Дата і час:</label><br>
        <input type="date" name="date_of_session" value="<?= htmlspecialchars($data['date_of_session']) ?>"><br><br>

        <button type="submit">Зберегти зміни</button>
    </form>
    <br><a href="index.php">Назад до списку</a>
</body>
</html>