<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/helpers.php';


$perPage = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$q    = trim((string)($_GET['q'] ?? ''));
 
// Підрахунок загальної кількості (пошук за датою сесії або ID тренера/студента)
$stmt = db()->prepare('SELECT COUNT(*) FROM Session WHERE date_of_session LIKE :q');
$stmt->execute([':q' => "%$q%"]);
$total = (int)$stmt->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));
$page  = min($page, $pages);
 
// Вибірка сесій разом із ім'ям тренера та студента (якщо потрібно)
$stmt = db()->prepare(
    'SELECT s.session_id, s.date_of_session, c.full_name AS coach_name, st.full_name AS student_name 
     FROM Session s 
     LEFT JOIN Coachs c ON c.coach_id = s.coach_id 
     LEFT JOIN students st ON st.student_id = s.student_id 
     WHERE s.date_of_session LIKE :q 
     LIMIT :limit OFFSET :offset'
);
$stmt->bindValue(':q', "%$q%");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT); // Виправлено розрив рядка
$stmt->execute();
$sessions = $stmt->fetchAll();

$messages = take_flash();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Список тренувань</title>
</head>
<body>
    <h1>Тренування та сесії</h1>

    <?php foreach ($messages as $msg): ?>
        <div style="color: <?= $msg['type'] === 'success' ? 'green' : 'red' ?>; margin-bottom: 10px;">
            <?= htmlspecialchars($msg['text']) ?>
        </div>
    <?php endforeach; ?>

    <p><a href="create.php">Створити нове тренування</a></p>

    <form method="GET" action="">
        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Пошук за датою (YYYY-MM-DD)...">
        <button type="submit">Шукати</button>
    </form>
    <br>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Студент</th>
            <th>Тренер</th>
            <th>Дата сесії</th>
            <th>Дії</th>
        </tr>
        <?php if (empty($sessions)): ?>
            <tr><td colspan="5">Тренувань не знайдено</td></tr>
        <?php else: ?>
            <?php foreach ($sessions as $session): ?>
            <tr>
                <td><?= $session['session_id'] ?></td>
                <td><?= htmlspecialchars($session['student_name'] ?? 'Не вказано') ?></td>
                <td><?= htmlspecialchars($session['coach_name'] ?? 'Не вказано') ?></td>
                <td><?= htmlspecialchars($session['date_of_session'] ?? '') ?></td>
                <td>
                    <a href="edit.php?id=<?= $session['session_id'] ?>">Редагувати</a> | 
                    <form action="delete.php" method="POST" onsubmit="return confirm('Видалити це тренування?');" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $session['session_id'] ?>">
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
    <br>
</body>
</html>