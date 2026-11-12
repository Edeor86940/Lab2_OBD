<?php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/helpers.php';

?>

    <h1>Система управління спортивною організацією</h1>
    <p>Виберіть потрібний розділ для керування даними:</p>

    <ul>
        <li><a href="/students/index.php"><strong>Студенти</strong></a> — управління списком студентів.</li>
        <li><a href="/Coachs/index.php"><strong>Тренери</strong></a> — управління списком тренерів.</li>
        <li><a href="/Session/index.php"><strong>Сесії </strong></a> — розклад занять та зв'язки між студентами й тренерами.</li>
    </ul>

</body>
</html>