<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

use App\Services\LibraryManager;

$library = new LibraryManager();
$message = '';
$messageType = '';

$bookId = $_GET['id'] ?? $_POST['id'] ?? '';

if (isset($_POST['action']) && $_POST['action'] === 'delete' && !empty($bookId)) {
    if ($library->deleteBook($bookId)) {
    
        header('Location: admin_dashboard.php?msg=deleted');
        exit;
    } else {
    
        $message = "خطا در حذف کتاب!";
        $messageType = "danger";
    }
}

$book = $library->findBookById($bookId);

if (!$book) {
    header('Location: admin_dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['action']) || $_POST['action'] !== 'delete')) {
    $title = trim($_POST['title'] ?? '');
    $author = trim($_POST['author'] ?? '');
    $isBorrowed = ($_POST['is_borrowed'] ?? '0') === '1'; 

    if (!empty($title) && !empty($author)) {
        if ($library->updateBook($bookId, $title, $author, $isBorrowed)) {
            $message = "اطلاعات و وضعیت کتاب با موفقیت به‌روزرسانی شد!";
            $messageType = "success";
            $book = $library->findBookById($bookId); 
        } else {
            $message = "خطا در ویرایش اطلاعات!";
            $messageType = "danger";
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
    <title>ویرایش کتاب</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" />
    <link rel="stylesheet" href="Styles/admin.css">
    <style>
        /* استایل‌های مخصوص این صفحه */
        .edit-book-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow);
        }
        .edit-book-container h1 {
            font-size: 1.75rem;
            margin-bottom: 25px;
            color: var(--primary);
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 500;
            color: var(--text-muted);
            font-size: 0.95rem;
        }
        .form-group input[type="text"], 
        .form-group select {
            width: 100%;
            height: 50px;
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-sizing: border-box;
            font-family: inherit;
            font-size: 1rem;
        }
        .form-group input[type="text"]:focus, 
        .form-group select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            outline: none;
        }
        .form-group input[disabled] {
            background-color: #f8f9fa;
            cursor: not-allowed;
            color: #6c757d;
        }
        .alert-success { background: #dcfce7; color: #15803d; }
        .alert-danger { background: #fee2e2; color: #b91c1c; }
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .btn-save {
            background-color: var(--primary);
            color: white;
            border: none;
            height: 50px;
            font-weight: bold;
            border-radius: 12px;
            cursor: pointer;
            flex: 1;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            height: 50px;
            font-weight: bold;
            border-radius: 12px;
            cursor: pointer;
            flex: 1;
        }
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }
        hr {
            margin: 40px 0;
            border: 0;
            border-top: 1px solid #e2e8f0;
        }
        header.page-header {
            text-align: center;
            margin-bottom: 30px;
        }
        header.page-header h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
        }
        header.page-header nav a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 1rem;
            transition: color 0.3s;
        }
        header.page-header nav a:hover {
            color: var(--primary);
        }
        .form-group .icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }
    </style>
</head>
<body class="admin-dashboard">

    <div class="edit-book-container">
        <header class="page-header">
            <h1><i class="fa-solid fa-pen-to-square"></i> ویرایش کتاب</h1>
            <nav>
                <a href="admin_dashboard.php"><i class="fa-solid fa-arrow-left-long"></i> بازگشت به داشبورد</a>
            </nav>
        </header>

        <?php if (!empty($message)): ?>
            <div class="alert alert-<?= $messageType ?>">
                <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- فرم ویرایش اطلاعات -->
        <form method="POST" action="editBook.php" class="modern-form">
            <input type="hidden" name="id" value="<?= htmlspecialchars($bookId) ?>">

            <div class="form-group">
                <label><i class="fa-solid fa-hashtag icon"></i> شناسه کتاب (غیرقابل تغییر)</label>
                <input type="text" value="<?= htmlspecialchars($book->getId()) ?>" disabled>
            </div>
            <div class="form-group">
                <label for="title"><i class="fa-solid fa-book icon"></i> عنوان کتاب</label>
                <input type="text" id="title" name="title" value="<?= htmlspecialchars($book->getTitle()) ?>" required>
            </div>
            <div class="form-group">
                <label for="author"><i class="fa-solid fa-user-pen icon"></i> نویسنده</label>
                <input type="text" id="author" name="author" value="<?= htmlspecialchars($book->getAuthor()) ?>" required>
            </div>
            <div class="form-group">
                <label for="is_borrowed"><i class="fa-solid fa-arrow-right-arrow-left icon"></i> وضعیت امانت</label>
                <select id="is_borrowed" name="is_borrowed">
                    <option value="0" <?= !$book->isBorrowed() ? 'selected' : '' ?>>موجود در کتابخانه</option>
                    <option value="1" <?= $book->isBorrowed() ? 'selected' : '' ?>>امانت داده شده</option>
                </select>
            </div>
            
            <div class="button-group">
                 <!-- دکمه ذخیره تغییرات -->
                <button type="submit" name="action" value="update" class="btn-save">
                   <i class="fa-solid fa-save"></i> ذخیره تغییرات
                </button>
            </div>
        </form>

        <hr>

        <form method="POST" action="editBook.php" onsubmit="return confirm('آیا از حذف این کتاب اطمینان دارید؟ این عملیات غیرقابل بازگشت است!');">
            <input type="hidden" name="id" value="<?= htmlspecialchars($bookId) ?>">
            <input type="hidden" name="action" value="delete">
            <button type="submit" class="btn-delete">
                <i class="fa-solid fa-trash-can"></i> حذف این کتاب
            </button>
        </form>
    </div>
</body>
</html>
