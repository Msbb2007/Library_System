<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

use App\Services\LibraryManager;

$library = new LibraryManager();
$search = trim($_GET['search'] ?? '');
$books = !empty($search) ? $library->searchBooks($search) : $library->getAllBooks();

$totalBooks = count($books);
$borrowedBooks = count(array_filter($books, function($b) { return $b->isBorrowed(); }));
$availableBooks = $totalBooks - $borrowedBooks;
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل مدیریت کتابخانه | مدیریت سیستم</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" type="text/css" />
    <!-- آیکون‌های Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Styles/admin.css">
</head>
<body class="admin-dashboard">
    <header class="main-header">
        <div class="header-content">
            <div class="logo">
                <i class="fa-solid fa-book-atlas"></i>
                <span>مدیریت کتابخانه</span>
            </div>
            <nav class="header-nav">
                <a href="manageUsers.php" class="nav-link"><i class="fa-solid fa-users"></i> کاربران</a>
                <a href="addBook.php" class="nav-link btn-add"><i class="fa-solid fa-plus"></i> افزودن کتاب</a>
                <a href="logout.php" class="nav-link logout"><i class="fa-solid fa-right-from-bracket"></i> خروج</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="welcome-section">
            <h1>خوش آمدید، <?= htmlspecialchars($_SESSION['user_name']) ?> 👋</h1>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fa-solid fa-book"></i></div>
                    <div class="stat-info">
                        <span class="stat-value"><?= $totalBooks ?></span>
                        <span class="stat-label">کل کتاب‌ها</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa-solid fa-check-circle"></i></div>
                    <div class="stat-info">
                        <span class="stat-value"><?= $availableBooks ?></span>
                        <span class="stat-label">موجود</span>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fa-solid fa-hand-holding"></i></div>
                    <div class="stat-info">
                        <span class="stat-value"><?= $borrowedBooks ?></span>
                        <span class="stat-label">امانت داده شده</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- بخش جست‌وجو -->
        <section class="search-section">
            <form method="GET" action="" class="search-form">
                <div class="search-input-wrapper">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="search" placeholder="جست‌وجو بر اساس شناسه، عنوان یا نویسنده..." value="<?= htmlspecialchars($search) ?>">
                </div>
                <button type="submit" class="btn btn-icon-only btn-primary">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <?php if (!empty($search)): ?>
                    <a href="admin_dashboard.php" class="btn btn-icon-only btn-secondary">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                <?php endif; ?>
            </form>
        </section>

        <!-- لیست کتاب‌ها -->
        <section class="table-section">
            <div class="table-header">
                <h2><i class="fa-solid fa-list"></i> لیست کتاب‌ها</h2>
            </div>
            <div class="table-wrapper">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>عنوان کتاب</th>
                            <th>نویسنده</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($books)): ?>
                            <tr>
                                <td colspan="5" cla="empty-state">
                                    <i class="fa-solid fa-face-frown"></i>
                                    <p>هیچ کتابی با این مشخصات یافت نشد.</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><span class="id-badge">#<?= htmlspecialchars($book->getId()) ?></span></td>
                                    <td class="book-title"><?= htmlspecialchars($book->getTitle()) ?></td>
                                    <td><?= htmlspecialchars($book->getAuthor()) ?></td>
                                    <td>
                                        <?php if ($book->isBorrowed()): ?>
                                            <span class="badge badge-borrowed"><i class="fa-solid fa-clock"></i> امانت داده شده</span>
                                        <?php else: ?>
                                            <span class="badge badge-available"><i class="fa-solid fa-check"></i> موجود</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="editBook.php?id=<?= urlencode($book->getId()) ?>" class="btn-edit">
                                            <i class="fa-solid fa-pen-to-square"></i> ویرایش
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

</body>
</html>
