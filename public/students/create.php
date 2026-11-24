<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/helpers.php';

$data = ['full_name' => '', 'date_of_birth' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf_or_419();

    $data['full_name']     = trim((string)($_POST['full_name'] ?? ''));
    $data['date_of_birth'] = trim((string)($_POST['date_of_birth'] ?? ''));

    if ($data['full_name'] === '' || mb_strlen($data['full_name']) > 150) {
        $errors['full_name'] = 'Вкажіть ПІБ (до 150 символів).';
    }

    $birthDateToSave = null;
    if ($data['date_of_birth'] !== '') {
        $dt = DateTimeImmutable::createFromFormat('Y-m-d', $data['date_of_birth']);
        if (!$dt || $dt->format('Y-m-d') !== $data['date_of_birth']) {
            $errors['date_of_birth'] = 'Некоректний формат дати народження.';
        } else {
            $birthDateToSave = $data['date_of_birth'];
        }
    }

    if (empty($errors)) {
        try {
            $stmt = db()->prepare(
                'INSERT INTO students (full_name, date_of_birth)
                 VALUES (:full_name, :date_of_birth)'
            );
            $stmt->execute([
                ':full_name'     => $data['full_name'],
                ':date_of_birth' => $birthDateToSave,
            ]);

            flash('success', 'Студента успішно додано.');
            redirect('index.php');
        } catch (PDOException $e) {
            $driverCode = $e->errorInfo[1] ?? null;
            if ((int)$driverCode === 1062 || $e->getCode() === '23000') {
                $errors['full_name'] = 'Запис із такими унікальними даними вже існує.';
            } else {
                error_log('Create student error: ' . $e->getMessage());
                $errors['full_name'] = 'Помилка бази даних під час збереження.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Додати студента</title>
    <style>
        .err-text { color: red; font-size: 0.9em; margin-top: 3px; }
    </style>
</head>
<body>
    <h1>Додати нового студента</h1>

    <form method="post" novalidate>
        <?= csrf_field() ?>

        <label for="full_name">ПІБ студента</label><br>
        <input type="text" id="full_name" name="full_name" value="<?= h($data['full_name']) ?>" required><br>
        <?php if (isset($errors['full_name'])): ?>
            <div class="err-text"><?= h($errors['full_name']) ?></div>
        <?php endif; ?>
        <br>

        <label for="date_of_birth">Дата народження</label><br>
        <input type="date" id="date_of_birth" name="date_of_birth" value="<?= h($data['date_of_birth']) ?>"><br>
        <?php if (isset($errors['date_of_birth'])): ?>
            <div class="err-text"><?= h($errors['date_of_birth']) ?></div>
        <?php endif; ?>
        <br>

        <button type="submit">Зберегти</button>
    </form>
    <br>
    <a href="index.php">Назад до списку</a>
</body>
</html>