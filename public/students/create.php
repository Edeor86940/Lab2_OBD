<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/helpers.php'; // Додано підключення helpers для функції redirect()


$data = ['full_name' => '', 'date_of_birth' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['full_name']     = trim($_POST['full_name'] ?? '');
    $data['date_of_birth'] = trim($_POST['date_of_birth'] ?? '');
    if ($data['full_name'] === '' || mb_strlen($data['full_name']) > 150) {
        $errors['full_name'] = 'Вкажіть ПІБ (до 150 символів).';
    }
    if (empty($errors)) {
        try {
            $stmt = db()->prepare(
                'INSERT INTO students (full_name, date_of_birth) 
                 VALUES (:full_name, :date_of_birth)'
            );
            $stmt->execute([
                ':full_name'     => $data['full_name'],
                ':date_of_birth' => $data['date_of_birth'] !== '' ? $data['date_of_birth'] : null,
            ]);
            redirect('index.php');
            
        } catch (PDOException $e) {
            throw $e; 
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
        <label for="full_name">ПІБ студента</label><br>
        <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($data['full_name']) ?>" required><br>
        <?php if (isset($errors['full_name'])): ?>
            <div class="err-text"><?= htmlspecialchars($errors['full_name']) ?></div>
        <?php endif; ?>
        <br>

        <label for="date_of_birth">Дата народження</label><br>
        <input type="date" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars($data['date_of_birth']) ?>"><br><br>

        <button type="submit">Зберегти</button>
    </form>
    <br>
    <a href="index.php">Назад до списку</a>
</body>
</html>