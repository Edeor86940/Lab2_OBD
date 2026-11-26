<?php
declare(strict_types=1);

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

    $stmt = $pdo->prepare('DELETE FROM Session WHERE session_id = ?');
    $stmt->execute([$id]);

    $pdo->commit();

    if ($stmt->rowCount() > 0) {
        flash('success', 'Тренування успішно видалено.');
    } else {
        flash('error', 'Запис не знайдено або вже видалено.');
    }
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    error_log('Delete session error: ' . $e->getMessage());
    flash('error', 'Не вдалося видалити тренування: ' . $e->getMessage());
}

redirect('index.php');
