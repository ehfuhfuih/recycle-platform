<?php
require_once 'auth.php';
requireLogin();
header('Content-Type: application/json; charset=utf-8');

if (currentUserRole() !== 'shop' && currentUserRole() !== 'admin') {
    echo json_encode(['success'=>false,'message'=>'صلاحيات غير كافية'], JSON_UNESCAPED_UNICODE);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$userCode = $data['user_code'] ?? '';

if ($userCode === '') {
    echo json_encode(['success'=>false,'message'=>'كود المستخدم مطلوب'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmtU = $pdo->prepare("SELECT id FROM users WHERE code = ? LIMIT 1");
$stmtU->execute([$userCode]);
$u = $stmtU->fetch();

if (!$u) {
    echo json_encode(['success'=>false,'message'=>'المستخدم غير موجود'], JSON_UNESCAPED_UNICODE);
    exit;
}

// إيجاد أول كوبون غير مستخدم لذلك المستخدم
$stmt = $pdo->prepare("SELECT id,code FROM coupons WHERE user_id = ? AND used = 0 ORDER BY created_at ASC LIMIT 1");
$stmt->execute([$u['id']]);
$coupon = $stmt->fetch();

if (!$coupon) {
    echo json_encode(['success'=>false,'message'=>'لا يوجد كوبونات متاحة للمستخدم'], JSON_UNESCAPED_UNICODE);
    exit;
}

$stmtU2 = $pdo->prepare("UPDATE coupons SET used = 1 WHERE id = ?");
$stmtU2->execute([$coupon['id']]);

echo json_encode(['success'=>true,'coupon_code'=>$coupon['code']], JSON_UNESCAPED_UNICODE);
