<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/helpers.php';



$id = (int)($_GET['id'] ?? 0);


$stmt = db()->prepare('SELECT * FROM students WHERE student_id = ?');
$stmt->execute([$id]);
$student = $stmt->fetch();


if (!$student) {
    http_response_code(404);
    exit('Студента не знайдено.');
}

$data = [
    'full_name'     => $student['full_name'],
    'date_of_birth' => $student['date_of_birth'] ?? ''
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['full_name']     = trim($_POST['full_name'] ?? '');
    $data['date_of_birth'] = trim($_POST['date_of_birth'] ?? '');

    if ($data['full_name'] === '' || mb_strlen($data['full_name']) > 150) {
        $errors['full_name'] = 'Вкажіть ПІБ (до 150 символів).';
    }

    if (empty($errors)) {
        $stmt = db()->prepare(
            'UPDATE students SET full_name = :full_name, date_of_birth = :date_of_birth 
             WHERE student_id = :id'
        );
        $stmt->execute([
            ':full_name'     => $data['full_name'],
            ':date_of_birth' => $data['date_of_birth'] !== '' ? $data['date_of_birth'] : null,
            ':id'            => $id
        ]);

        flash('success', 'Зміни успішно збережено.');
        redirect('index.php');
    }
}
?>

<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагувати студента</title>
    <style>
        .err-text { color: red; font-size: 0.9em; margin-top: 3px; }
        .form-group { margin-bottom: 15px; }
    </style>
</head>
<body>
    <h1>Редагувати студента</h1>

    <form method="post" novalidate>
        <div class="form-group">
            <label for="full_name">ПІБ студента</label><br>
            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($data['full_name']) ?>" required>
            <?php if (isset($errors['full_name'])): ?>
                <div class="err-text"><?= htmlspecialchars($errors['full_name']) ?></div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="date_of_birth">Дата народження</label><br>
            <input type="date" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($data['date_of_birth']) ?>">
        </div>

        <button type="submit">Зберегти зміни</button>
    </form>
    <br>
    <a href="index.php">Назад до списку</a>
</body>
</html>