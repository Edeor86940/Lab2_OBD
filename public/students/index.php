<?php

require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/helpers.php';


$perPage = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$q    = trim((string)($_GET['q'] ?? ''));
 
$stmt = db()->prepare('SELECT COUNT(*) FROM students WHERE full_name LIKE :q');
$stmt->execute([':q' => "%$q%"]);
$total = (int)$stmt->fetchColumn();                         
$pages = max(1, (int)ceil($total / $perPage));
$page  = min($page, $pages);
 
$stmt = db()->prepare('SELECT * FROM students WHERE full_name LIKE :q LIMIT :limit OFFSET :offset');
$stmt->bindValue(':q', "%$q%");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT); 
$stmt->execute();
$students = $stmt->fetchAll();                              
$messages = take_flash();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Список студентів</title>
</head>
<body>
    <h1>Студенти</h1>

    <?php foreach ($messages as $msg): ?>
        <div style="color: <?= $msg['type'] === 'success' ? 'green' : 'red' ?>; margin-bottom: 10px;">
            <?= htmlspecialchars($msg['text']) ?>
        </div>
    <?php endforeach; ?>

    <p><a href="create.php">Додати нового студента</a></p>

   
    <form method="GET" action="">
        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Пошук за ім'ям...">
        <button type="submit">Шукати</button>
    </form>
    <br>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>ПІБ</th>
            <th>Дата народження</th>
            <th>Дії</th>
        </tr>
        <?php if (empty($students)): ?>
            <tr><td colspan="4">Нічого не знайдено</td></tr>
        <?php else: ?>
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?= $student['student_id'] ?></td>
                <td><?= htmlspecialchars($student['full_name']) ?></td>
                <td><?= htmlspecialchars($student['date_of_birth'] ?? '') ?></td>
                <td>
                    <a href="edit.php?id=<?= $student['student_id'] ?>">Редагувати</a> | 
                    <form action="delete.php" method="POST" onsubmit="return confirm('Видалити студента?');" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $student['student_id'] ?>">
                        <button type="submit" style="color: red; background: none; border: none; cursor: pointer; padding: 0;">Видалити</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </table>

   
    <div style="margin-top: 15px;">
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&q=<?= urlencode($q) ?>">&laquo; Попередня</a>
        <?php endif; ?>
        
        <span> Сторінка <?= $page ?> з <?= $pages ?> </span>

        <?php if ($page < $pages): ?>
            <a href="?page=<?= $page + 1 ?>&q=<?= urlencode($q) ?>">Наступна &raquo;</a>
        <?php endif; ?>
    </div>
</body>
</html>