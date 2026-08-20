<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

use App\Services\LibraryManager;

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = trim($_POST['id'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');

    if (!empty($title) && !empty($author) && !empty($id)) {
        $library = new LibraryManager();
        
        if ($library->findBookById($id)) {
            $message = "این شناسه قبلاً ثبت شده است!";
            $messageType = "danger";
        } else {
            $library->addBook($title, $author, $id);
            $message = "کتاب با موفقیت ثبت شد!";
            $messageType = "success";
        }
    } else {
        $message = "لطفاً تمام فیلدها را پر کنید.";
        $messageType = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>افزودن کتاب جدید</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Styles/admin.css">
</head>
<body class="admin-dashboard">

    <div class="container" style="max-width: 600px; margin-top: 50px;">
        <div class="card p-4">
            <div class="card-header text-center mb-4">
                <i class="fa-solid fa-plus-circle fa-3x" style="color: var(--primary);"></i>
                <h1 style="font-size: 1.5rem; margin-top: 15px;">افزودن کتاب جدید</h1>
            </div>

            <?php if (!empty($message)): ?>
                <div class="alert alert-<?= $messageType ?>">
                    <i class="fa-solid fa-circle-info"></i> <?= $message ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="addBook.php" class="modern-form">
                <div class="form-group">
                    <label><i class="fa-solid fa-hashtag"></i> شناسه کتاب</label>
                    <input type="text" name="id" placeholder="مثلاً BK-101" required>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-book"></i> عنوان کتاب</label>
                    <input type="text" name="title" placeholder="نام کتاب را وارد کنید" required>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-user-pen"></i> نویسنده</label>
                    <input type="text" name="author" placeholder="نام نویسنده" required>
                </div>
                
                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn-submit">ثبت کتاب</button>
                    <a href="admin_dashboard.php" class="btn-back">بازگشت</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
