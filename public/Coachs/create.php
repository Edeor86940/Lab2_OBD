<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/helpers.php';


$data = ['full_name' => '', 'adrres' => '', 'specialization' => ''];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['full_name']      = trim($_POST['full_name'] ?? '');
    $data['adrres']         = trim($_POST['adrres'] ?? '');
    $data['specialization'] = trim($_POST['specialization'] ?? '');

    if ($data['full_name'] === '' || mb_strlen($data['full_name']) > 150) {
        $errors['full_name'] = 'Вкажіть коректне ПІБ (до 150 символів).';
    }

    if (empty($errors)) {
        $stmt = db()->prepare('INSERT INTO Coachs (full_name, adrres, specialization) VALUES (:full_name, :adrres, :spec)');
        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':adrres'    => $data['adrres'] !== '' ? $data['adrres'] : null,
            ':spec'      => $data['specialization'] !== '' ? $data['specialization'] : null,
        ]);
        flash('success', 'Тренера успішно додано.');
        redirect('index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Додати тренера</title>
</head>
<body>
    <h1>Додати нового тренера</h1>
    <form method="post" novalidate>
        <label>ПІБ:</label><br>
        <input type="text" name="full_name" value="<?= htmlspecialchars($data['full_name']) ?>" required><br>
        <?php if (isset($errors['full_name'])): ?><div style="color:red;"><?= $errors['full_name'] ?></div><?php endif; ?><br>

        <label>Адреса:</label><br>
        <input type="text" name="adrres" value="<?= htmlspecialchars($data['adrres']) ?>"><br><br>

        <label>Спеціалізація:</label><br>
        <input type="text" name="specialization" value="<?= htmlspecialchars($data['specialization']) ?>"><br><br>

        <button type="submit">Зберегти</button>
    </form>
    <br><a href="index.php">Назад</a>
</body>
</html>