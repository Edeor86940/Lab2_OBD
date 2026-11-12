<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/helpers.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

$id = (int)($_POST['id'] ?? 0);
$pdo = db();

try {
    $pdo->beginTransaction();

    // Якщо у вас є пов'язана таблиця записів студентів на тренування, 
    // спочатку видаляємо зв'язки звідти, якщо це необхідно:
    // $stmt = $pdo->prepare('DELETE FROM session_students WHERE session_id = ?');
    // $stmt->execute([$id]);

    // Видаляємо саму сесію (змінено Sessions на Session відповідно до бази даних)
    $stmt = $pdo->prepare('DELETE FROM Session WHERE session_id = ?');
    $stmt->execute([$id]);

    $pdo->commit();
    flash('success', 'Тренування успішно видалено.');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash('error', 'Не вдалося видалити тренування.');
}

redirect('index.php');