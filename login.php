<?php
require_once 'db.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];

        if ($user['role'] === 'admin') {
            header('Location: index.php?page=admin');
        } elseif ($user['role'] === 'shop') {
            header('Location: index.php?page=shop');
        } else {
            header('Location: index.php?page=user');
        }
        exit;
    } else {
        $error = 'بيانات الدخول غير صحيحة';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>تسجيل الدخول</title>
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
        max-width:400px;
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
    input {
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
    <h2>تسجيل الدخول للمنصة</h2>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post">
        <label>البريد الإلكتروني:</label>
        <input type="email" name="email" required>
        <label>كلمة المرور:</label>
        <input type="password" name="password" required>
        <button type="submit">دخول</button>
    </form>
    <div class="link">
        ليس لديك حساب؟ <a href="register.php">إنشاء حساب جديد</a>
    </div>
</div>
</body>
</html>
