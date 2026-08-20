<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use App\Services\LibraryManager;

$library = new LibraryManager();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $phone = trim($_POST['phone'] ?? ''); 
    $user = $library->findUserByPhone($phone);

    if ($user) {
        $_SESSION['user_id'] = $user->getPhone();
        $_SESSION['user_name'] = $user->getName();
        $_SESSION['user_role'] = $user->getRole();

        if ($user->getRole() === 'admin') {
            header('Location: admin_dashboard.php');
        } else {
            header('Location: member_dashboard.php');
        }
        exit;
    } else {
        $error = "کاربری با این شماره یافت نشد!";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = $_POST['role'] ?? 'member';

    if (!empty($name) && !empty($phone)) {
        if ($library->findUserByPhone($phone)) {
            $error = "این شماره تماس قبلاً ثبت شده است!";
        } else {
            $library->addUser($name, $phone, $role);
            $success = "ثبت‌نام با موفقیت انجام شد. اکنون وارد شوید.";
        }
    } else {
        $error = "لطفاً تمام فیلدها را پر کنید.";
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود و ثبت‌نام | سیستم مدیریت کتابخانه</title>
    <link rel="stylesheet" href="Styles/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-page">

    <div class="auth-card">
        <div class="auth-header">
            <div class="logo-icon">
                <i class="fa-solid fa-book-open"></i>
            </div>
            <h1>سامانه جامع کتابخانه</h1>
            <p>برای ادامه، وارد حساب خود شوید یا ثبت‌نام کنید</p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?= $error ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?= $success ?></div>
        <?php endif; ?>

        <input type="radio" id="tab-login" name="auth-tab" class="tab-toggle" checked>
        <input type="radio" id="tab-register" name="auth-tab" class="tab-toggle">

        <div class="tabs">
            <label for="tab-login" class="tab-btn">ورود به سیستم</label>
            <label for="tab-register" class="tab-btn">ثبت‌نام کاربر جدید</label>
        </div>

        <form method="POST" class="auth-form form-login">
            <div class="form-group">
                <label for="login-phone"><i class="fa-solid fa-phone"></i> شماره تماس:</label>
                <input type="text" id="login-phone" name="phone" placeholder="مثال: 09123456789" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary btn-block">ورود به حساب</button>
        </form>

        <form method="POST" class="auth-form form-register">
            <div class="form-group">
                <label for="reg-name"><i class="fa-solid fa-user"></i> نام و نام خانوادگی:</label>
                <input type="text" id="reg-name" name="name" placeholder="مثال:  محمدصدرا بابازاده">
            </div>
            <div class="form-group">
                <label for="reg-phone"><i class="fa-solid fa-phone"></i> شماره تماس:</label>
                <input type="text" id="reg-phone" name="phone" placeholder="مثال: 09123456789">
            </div>
            <button type="submit" name="register" class="btn btn-success btn-block">تکمیل ثبت‌نام</button>
        </form>
    </div>

</body>
</html>