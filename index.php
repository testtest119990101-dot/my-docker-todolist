<?php
// قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "password";
$dbname = "todolist";

// محاولة الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    echo "تحذير: قاعدة البيانات غير متصلة (هذا للاختبار بدون MySQL)<br>";
    $use_db = false;
} else {
    $use_db = true;
    
    // إنشاء الجدول
    $conn->query("CREATE TABLE IF NOT EXISTS tasks (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        is_done TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
}

// إضافة مهمة
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_task']) && $use_db) {
    $title = $conn->real_escape_string($_POST['title']);
    if (!empty($title)) {
        $conn->query("INSERT INTO tasks (title) VALUES ('$title')");
    }
    header('Location: index.php');
    exit;
}

// تحديث المهمة
if (isset($_GET['complete']) && $use_db) {
    $id = intval($_GET['complete']);
    $conn->query("UPDATE tasks SET is_done = 1 WHERE id = $id");
    header('Location: index.php');
    exit;
}

// حذف المهمة
if (isset($_GET['delete']) && $use_db) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM tasks WHERE id = $id");
    header('Location: index.php');
    exit;
}

// الحصول على المهام
$result = null;
if ($use_db) {
    $result = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>📝 قائمة المهام 🐳 Docker</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 قائمة المهام 🐳</h1>
            <p class="subtitle">تطبيق Docker للمهام اليومية</p>
        </div>
        
        <form method="POST" class="form">
            <input 
                type="text" 
                name="title" 
                placeholder="أضف مهمة جديدة..." 
                required
                maxlength="255"
            >
            <button type="submit" name="add_task">➕ إضافة</button>
        </form>
        
        <div class="tasks">
            <?php if ($use_db && $result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="task <?php echo $row['is_done'] ? 'done' : ''; ?>">
                        <div class="task-content">
                            <span class="task-title"><?php echo htmlspecialchars($row['title']); ?></span>
                            <span class="task-date"><?php echo $row['created_at']; ?></span>
                        </div>
                        <div class="buttons">
                            <?php if (!$row['is_done']): ?>
                                <a href="?complete=<?php echo $row['id']; ?>" class="btn-complete" title="تم">✓</a>
                            <?php endif; ?>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('حذف المهمة؟')" title="حذف">✕</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php elseif ($use_db): ?>
                <p class="empty">✨ لا توجد مهام حالياً - ابدأ بإضافة مهمة جديدة!</p>
            <?php else: ?>
                <div class="info">
                    <p>⚠️ التطبيق يعمل بدون قاعدة بيانات</p>
                    <p style="font-size: 0.9em; margin-top: 10px;">للحصول على كامل الميزات، استخدم:</p>
                    <code>docker-compose up --build</code>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
