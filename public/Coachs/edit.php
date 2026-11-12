<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/helpers.php';


$id = (int)($_GET['id'] ?? 0);

$stmt = db()->prepare('SELECT * FROM Coachs WHERE coach_id = ?');
$stmt->execute([$id]);
$coach = $stmt->fetch();

if (!$coach) {
    http_response_code(404);
    exit('Тренера не знайдено.');
}

$data = [
    'full_name'      => $coach['full_name'],
    'adrres'         => $coach['adrres'] ?? '', 
    'specialization' => $coach['specialization'] ?? ''
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data['full_name']      = trim($_POST['full_name'] ?? '');
    $data['adrres']         = trim($_POST['adrres'] ?? '');
    $data['specialization'] = trim($_POST['specialization'] ?? '');

    if ($data['full_name'] === '' || mb_strlen($data['full_name']) > 150) {
        $errors['full_name'] = 'Вкажіть коректне ПІБ (до 150 символів).';
    }

    if (empty($errors)) {
        $stmt = db()->prepare(
            'UPDATE Coachs SET full_name = :full_name, adrres = :adrres, specialization = :spec 
             WHERE coach_id = :id'
        );
        $stmt->execute([
            ':full_name' => $data['full_name'],
            ':adrres'    => $data['adrres'] !== '' ? $data['adrres'] : null,
            ':spec'      => $data['specialization'] !== '' ? $data['specialization'] : null,
            ':id'        => $id
        ]);

        flash('success', 'Дані тренера успішно оновлено.');
        redirect('index.php');
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Редагувати тренера</title>
</head>
<body>
    <h1>Редагувати тренера</h1>
    <form method="post" novalidate>
        <label>ПІБ:</label><br>
        <input type="text" name="full_name" value="<?= htmlspecialchars($data['full_name']) ?>" required><br>
        <?php if (isset($errors['full_name'])): ?><div style="color:red;"><?= $errors['full_name'] ?></div><?php endif; ?><br>

        <label>Адреса:</label><br>
        <input type="text" name="adrres" value="<?= htmlspecialchars($data['adrres']) ?>"><br><br>

        <label>Спеціалізація:</label><br>
        <input type="text" name="specialization" value="<?= htmlspecialchars($data['specialization']) ?>"><br><br>

        <button type="submit">Зберегти зміни</button>
    </form>
    <br><a href="index.php">Назад до списку</a>
</body>
</html>