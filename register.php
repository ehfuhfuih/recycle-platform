<?php
require_once 'db.php';
session_start();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $role  = $_POST['role'] ?? 'user';

    if ($name === '' || $email === '' || $pass === '') {
        $error = 'كل الحقول مطلوبة';
    } else {
        $hash = password_hash($pass, PASSWORD_BCRYPT);
        $code = 'U'.strtoupper(substr(md5(uniqid()),0,6));
        try {
            $stmt = $pdo->prepare("INSERT INTO users (name,email,password,role,code) VALUES (?,?,?,?,?)");
            $stmt->execute([$name,$email,$hash,$role,$code]);
            $success = 'تم إنشاء الحساب بنجاح. كودك: '.$code;
        } catch (PDOException $e) {
            $error = 'البريد مستخدم من قبل';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>إنشاء حساب</title>
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Cairo', sans-serif;
        background: #e6f2f0;
        margin: 0;
        padding: 0;
        display:flex;
        justify-content:center;
        align-items:center;
        min-height:100vh;
    }
    .auth-box {
        background:#fff;
        padding:25px 30px;
        border-radius:18px;
        box-shadow:0 6px 20px rgba(0,0,0,0.15);
        max-width:450px;
        width:100%;
        text-align:right;
    }
    h2 {
        margin-top:0;
        margin-bottom:15px;
        text-align:center;
        color:#2e7d32;
    }
    label {
        display:block;
        margin-top:10px;
        font-weight:bold;
    }
    input, select {
        width:100%;
        padding:10px;
        margin-top:5px;
        border-radius:8px;
        border:1px solid #ccc;
        font-size:15px;
        box-sizing:border-box;
    }
    button {
        margin-top:20px;
        width:100%;
        padding:10px;
        border:none;
        border-radius:10px;
        background:#2e7d32;
        color:#fff;
        font-weight:bold;
        font-size:16px;
        cursor:pointer;
        transition:0.2s;
    }
    button:hover {
        background:#1b5e20;
        transform:translateY(-1px);
    }
    .error {
        color:#c62828;
        background:#ffebee;
        padding:8px 10px;
        border-radius:8px;
        font-size:14px;
    }
    .success {
        color:#2e7d32;
        background:#e8f5e9;
        padding:8px 10px;
        border-radius:8px;
        font-size:14px;
    }
    .link {
        margin-top:15px;
        text-align:center;
        font-size:14px;
    }
    .link a {
        color:#1e88e5;
        text-decoration:none;
        font-weight:bold;
    }
</style>
</head>
<body>
<div class="auth-box">
    <h2>إنشاء حساب جديد</h2>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif; ?>
    <form method="post">
        <label>الاسم:</label>
        <input type="text" name="name" required>

        <label>البريد الإلكتروني:</label>
        <input type="email" name="email" required>

        <label>كلمة المرور:</label>
        <input type="password" name="password" required>

       <label>نوع الحساب (Role):</label>
< name="role" required>
    <option value="user">مستخدم عادي</option>
    <option value="shop">سوبر ماركت</option>
    <option value="collector">مسؤول محطة تجميع نفايات ECHO_SMART</option>
    <option value="delegate">مندوب التجميع</option>
    <option value="admin">أدمن (مدير النظام)</option>
        </select>

        <button type="submit">تسجيل</button>
    </form>
    <div class="link">
        لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a>
    </div>
</div>
</body>
</html>
