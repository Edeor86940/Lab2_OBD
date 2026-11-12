<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/helpers.php';


$perPage = 10;
$page = max(1, (int)($_GET['page'] ?? 1));
$q    = trim((string)($_GET['q'] ?? ''));
 
$stmt = db()->prepare('SELECT COUNT(*) FROM Coachs WHERE full_name LIKE :q');
$stmt->execute([':q' => "%$q%"]);
$total = (int)$stmt->fetchColumn();
$pages = max(1, (int)ceil($total / $perPage));
$page  = min($page, $pages);
 
$stmt = db()->prepare('SELECT * FROM Coachs WHERE full_name LIKE :q LIMIT :limit OFFSET :offset');
$stmt->bindValue(':q', "%$q%");
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', ($page - 1) * $perPage, PDO::PARAM_INT); // Виправлено розрив рядка
$stmt->execute();
$coaches = $stmt->fetchAll();
$messages = take_flash();
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Список тренерів</title>
</head>
<body>
    <h1>Тренери</h1>

    <?php foreach ($messages as $msg): ?>
        <div style="color: <?= $msg['type'] === 'success' ? 'green' : 'red' ?>; margin-bottom: 10px;">
            <?= htmlspecialchars($msg['text']) ?>
        </div>
    <?php endforeach; ?>

    <p><a href="create.php">Додати нового тренера</a></p>

    <form method="GET" action="">
        <input type="text" name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Пошук тренера...">
        <button type="submit">Шукати</button>
    </form>
    <br>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>ПІБ</th>
            <th>Спеціалізація</th>
            <th>Дії</th>
        </tr>
        <?php if (empty($coaches)): ?>
            <tr><td colspan="4">Тренерів не знайдено</td></tr>
        <?php else: ?>
            <?php foreach ($coaches as $coach): ?>
            <tr>
                <td><?= $coach['coach_id'] ?></td>
                <td><?= htmlspecialchars($coach['full_name']) ?></td>
                <td><?= htmlspecialchars($coach['specialization'] ?? '') ?></td>
                <td>
                    <a href="edit.php?id=<?= $coach['coach_id'] ?>">Редагувати</a> | 
                    <form action="delete.php" method="POST" onsubmit="return confirm('Видалити цього тренера?');" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $coach['coach_id'] ?>">
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