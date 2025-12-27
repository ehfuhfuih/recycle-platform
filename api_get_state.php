<?php
require_once 'auth.php';
requireLogin();

header('Content-Type: application/json; charset=utf-8');

$userId = currentUserId();
$role   = currentUserRole();

// بيانات المستخدم الحالي
$stmt = $pdo->prepare("SELECT id,name,role,
   (SELECT COALESCE(SUM(points),0) FROM activities WHERE user_id = users.id) AS points,
   (SELECT COUNT(*) FROM coupons WHERE user_id = users.id AND used = 0) AS coupons
   FROM users WHERE id = ?");
$stmt->execute([$userId]);
$me = $stmt->fetch();

// الأنشطة (للمستخدم: أنشطته فقط، للأدمن: كل الأنشطة، للسوبر ماركت يمكن أن تضبطها لاحقًا)
if ($role === 'admin') {
    $qAct = "SELECT a.id,a.type,a.notes,a.points,a.created_at,u.name AS user_name 
             FROM activities a JOIN users u ON a.user_id=u.id
             ORDER BY a.created_at DESC LIMIT 100";
    $stmtAct = $pdo->query($qAct);
} else {
    $qAct = "SELECT id,type,notes,points,created_at 
             FROM activities WHERE user_id = ? ORDER BY created_at DESC LIMIT 50";
    $stmtAct = $pdo->prepare($qAct);
    $stmtAct->execute([$userId]);
}
$activities = $stmtAct->fetchAll();

// الكوبونات (للأدمن يشوف الكل)
if ($role === 'admin') {
    $qCp = "SELECT c.id,c.code,c.value,c.used,c.created_at,u.name AS user_name
            FROM coupons c JOIN users u ON c.user_id=u.id
            ORDER BY c.created_at DESC LIMIT 100";
    $stmtCp = $pdo->query($qCp);
} else {
    $qCp = "SELECT id,code,value,used,created_at 
            FROM coupons WHERE user_id = ? ORDER BY created_at DESC";
    $stmtCp = $pdo->prepare($qCp);
    $stmtCp->execute([$userId]);
}
$coupons = $stmtCp->fetchAll();

// المستخدمون للأدمن فقط
$users = [];
if ($role === 'admin') {
    $stmtU = $pdo->query("SELECT id,name,role FROM users ORDER BY id DESC");
    $users = $stmtU->fetchAll();
}

// الإشعارات (اختيارية: جدول مستقل أو يمكن تركه فارغًا الآن)
$alerts = [];

echo json_encode([
    'me'         => $me,
    'activities' => $activities,
    'coupons'    => $coupons,
    'alerts'     => $alerts,
    'users'      => $users
], JSON_UNESCAPED_UNICODE);
