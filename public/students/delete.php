<?php
require_once __DIR__ . '/../../src/db.php';
require_once __DIR__ . '/../../src/helpers.php';


// Видалення дозволене лише через POST-запит
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Перевірка CSRF-токена (якщо ви використовуєте цю функцію)
// csrf_check();

$id = (int)($_POST['id'] ?? 0);
$pdo = db();

try {
    $pdo->beginTransaction();

    // 1. Спочатку видаляємо пов'язані записи з таблиці Session (щоб уникнути помилки зовнішнього ключа)
    $stmt = $pdo->prepare('DELETE FROM Session WHERE student_id = ?');
    $stmt->execute([$id]);

    // 2. Потім видаляємо самого студента з таблиці students (з маленької літери)
    $stmt = $pdo->prepare('DELETE FROM students WHERE student_id = ?');
    $stmt->execute([$id]);

    $pdo->commit();
    
    flash('success', 'Студента успішно видалено.');
} catch (Throwable $e) {
    // Якщо сталася помилка — відкочуємо транзакцію назад
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash('error', 'Не вдалося видалити студента.');
}

redirect('index.php');