<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

use App\Services\LibraryManager;

$library = new LibraryManager();

$searchUser = trim($_GET['search_user'] ?? '');
$users = !empty($searchUser) ? $library->searchUsers($searchUser) : $library->getAllUsers();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مدیریت کاربران</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="Styles/admin.css">
</head>
<body class="admin-dashboard">
    <div class="container">
        <header class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1 style="margin: 0;"><i class="fa-solid fa-users-gear"></i> مدیریت کاربران</h1>
                <p style="color: var(--text-muted); margin: 5px 0 0 0;">مشاهده و مدیریت دسترسی‌های اعضای سیستم</p>
            </div>
            <nav style="display: flex; gap: 10px; align-items: center;">
                <a href="admin_dashboard.php" class="btn-back"><i class="fa-solid fa-arrow-right"></i> داشبورد</a>
            </nav>
        </header>

        <div class="card" style="margin-bottom: 30px; padding: 20px;">
            <form method="GET" action="" class="search-bar-container">
                <div class="search-input-wrapper" style="position: relative; flex: 1;">
                    <i class="fa-solid fa-magnifying-glass" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); color: var(--text-muted);"></i>
                    <input type="text" name="search_user" 
                           placeholder="جست‌وجو بر اساس نام، شماره یا نقش..." 
                           value="<?= htmlspecialchars($searchUser) ?>" 
                           style="padding-right: 45px; width: 100%; height: 45px; border-radius: 10px; border: 1px solid #e2e8f0;">
                </div>
                <button type="submit" class="btn-submit" style="height: 45px; flex: 0 0 auto; padding: 0 60px;">
                    <i class="fa-solid fa-search"></i> جست‌وجو
                </button>
                <?php if (!empty($searchUser)): ?>
                    <a href="manageUsers.php" class="btn-back" style="height: 45px; display: flex; align-items: center; text-decoration: none; padding: 0 15px;">
                        پاک‌سازی
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="padding: 20px; border-bottom: 1px solid #eee;">
                <h2 style="margin: 0; font-size: 1.2rem;"><i class="fa-solid fa-list"></i> لیست کاربران سیستم</h2>
            </div>
            <table class="modern-table" style="margin: 0; width: 100%;">
                <thead>
                    <tr>
                        <th><i class="fa-solid fa-user"></i> نام و نام خانوادگی</th>
                        <th><i class="fa-solid fa-phone"></i> شماره تماس</th>
                        <th><i class="fa-solid fa-shield-halved"></i> نقش کاربری</th>
                        <th><i class="fa-solid fa-key"></i> دسترسی‌ها</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: #888;">
                                <i class="fa-solid fa-user-slash fa-3x" style="display: block; margin-bottom: 10px; opacity: 0.3;"></i>
                                هیچ کاربری با این مشخصات یافت نشد.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td style="font-weight: 500;"><?= htmlspecialchars($user->getName()) ?></td>
                                <td><?= htmlspecialchars($user->getPhone()) ?></td>
                                <td>
                                    <?php if ($user->getRole() === 'admin'): ?>
                                        <span class="badge badge-admin">مدیر سیستم</span>
                                    <?php else: ?>
                                        <span class="badge badge-user">عضو عادی</span>
                                    <?php endif; ?>
                                </td>
                                <td style="font-size: 0.85em; color: #64748b;">
                                    <span class="permission-tag"><?= implode(' · ', $user->getPermissions()) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
