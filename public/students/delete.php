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


    $stmt = $pdo->prepare('DELETE FROM Session WHERE student_id = ?');
    $stmt->execute([$id]);


    $stmt = $pdo->prepare('DELETE FROM students WHERE student_id = ?');
    $stmt->execute([$id]);

    $pdo->commit();
    
    flash('success', 'Студента успішно видалено.');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    flash('error', 'Не вдалося видалити студента.');
}

redirect('index.php');