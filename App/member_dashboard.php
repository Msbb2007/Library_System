<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

use App\Services\LibraryManager;

$library = new LibraryManager();
$currentUserId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_id'], $_POST['action'])) {
    $bookId = $_POST['book_id'];
    $action = $_POST['action'];

    if ($action === 'borrow') {
        $library->borrowBook($bookId, $currentUserId);
    } elseif ($action === 'return') {
        $book = $library->findBookById($bookId);
        if ($book && $book->getBorrowedBy() === $currentUserId) {
            $library->returnBook($bookId);
        }
    }
}

$search = trim($_GET['search'] ?? '');
$books = !empty($search) ? $library->searchBooks($search) : $library->getAllBooks();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل اعضا | سیستم مدیریت کتابخانه</title>
    <link rel="stylesheet" href="Styles/member.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="dashboard-page">

    <div class="dashboard-container">
        <!-- هدر اصلی -->
        <header class="dashboard-header">
            <div class="user-info">
                <div class="avatar-icon">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div>
                    <h1>خوش آمدید، <?= htmlspecialchars($_SESSION['user_name']) ?></h1>
                    <p>پنل مدیریت و امانت کتاب اعضا</p>
                </div>
            </div>
            <nav>
                <a href="logout.php" class="btn btn-logout">
                    <i class="fa-solid fa-right-from-bracket"></i> خروج
                </a>
            </nav>
        </header>

      <!-- بخش جستجو -->
<div class="search-section">
    <form method="GET" action="" class="search-form">
        <div class="search-input-wrapper">
            <i class="fa-solid fa-magnifying-glass search-icon"></i>
            <input type="text" name="search" placeholder="جست و جو براساس نام کتاب یا نویسنده..." value="<?= htmlspecialchars($search) ?>">
        </div>
        
        <!-- دکمه جستجو با آیکون -->
        <button type="submit" class="btn btn-primary btn-icon-only">
            <i class="fa-solid fa-magnifying-glass"></i>
        </button>

        <?php if (!empty($search)): ?>
            <!-- دکمه پاکسازی با آیکون -->
            <a href="member_dashboard.php" class="btn btn-secondary btn-icon-only">
                <i class="fa-solid fa-xmark"></i>
            </a>
        <?php endif; ?>
    </form>
</div>


        <!-- لیست کتاب‌ها -->
        <div class="card-table">
            <div class="card-header">
                <h2><i class="fa-solid fa-books"></i> لیست کتاب‌های سیستم</h2>
                <span class="count-badge"><?= count($books) ?> کتاب</span>
            </div>

            <div class="table-responsive">
                <table>
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
                                <td colspan="5" style="text-align: center; padding: 30px; color: #64748b;">
                                    <i class="fa-solid fa-folder-open" style="font-size: 2rem; margin-bottom: 10px; display: block;"></i>
                                    هیچ کتابی یافت نشد!
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($books as $book): ?>
                                <?php 
                                    $isBorrowedByMe = ($book->isBorrowed() && $book->getBorrowedBy() === $currentUserId);
                                ?>
                                <tr>
                                    <td><code class="code-badge"><?= htmlspecialchars($book->getId()) ?></code></td>
                                    <td><strong><?= htmlspecialchars($book->getTitle()) ?></strong></td>
                                    <td><?= htmlspecialchars($book->getAuthor()) ?></td>
                                    <td>
                                        <?php if ($isBorrowedByMe): ?>
                                            <span class="badge badge-my-borrow"><i class="fa-solid fa-user-check"></i> در امانت شما</span>
                                        <?php elseif ($book->isBorrowed()): ?>
                                            <span class="badge badge-borrowed"><i class="fa-solid fa-lock"></i> امانت داده شده</span>
                                        <?php else: ?>
                                            <span class="badge badge-available"><i class="fa-solid fa-circle-check"></i> موجود</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($isBorrowedByMe): ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="book_id" value="<?= htmlspecialchars($book->getId()) ?>">
                                                <input type="hidden" name="action" value="return">
                                                <button type="submit" class="btn btn-warning btn-sm">
                                                    <i class="fa-solid fa-rotate-left"></i> تحویل / بازگرداندن
                                                </button>
                                            </form>
                                        <?php elseif ($book->isBorrowed()): ?>
                                            <button class="btn btn-disabled btn-sm" disabled>
                                                <i class="fa-solid fa-ban"></i> غیرقابل دسترسی
                                            </button>
                                        <?php else: ?>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="book_id" value="<?= htmlspecialchars($book->getId()) ?>">
                                                <input type="hidden" name="action" value="borrow">
                                                <button type="submit" class="btn btn-primary btn-sm">
                                                    <i class="fa-solid fa-hand-holding"></i> امانت گرفتن
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>