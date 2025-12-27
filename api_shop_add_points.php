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
$points   = (int)($data['points'] ?? 0);

if ($userCode === '' || $points <= 0) {
    echo json_encode(['success'=>false,'message'=>'بيانات غير صحيحة'], JSON_UNESCAPED_UNICODE);
    exit;
}

// إيجاد المستخدم بالكود
$stmtU = $pdo->prepare("SELECT id FROM users WHERE code = ? LIMIT 1");
$stmtU->execute([$userCode]);
$u = $stmtU->fetch();

if (!$u) {
    echo json_encode(['success'=>false,'message'=>'المستخدم غير موجود'], JSON_UNESCAPED_UNICODE);
    exit;
}

// تسجيل نشاط نقاط (نوع خاص مثلاً plastic لعدم التعقيد، أو تضيف نوع جديد)
$stmtA = $pdo->prepare("INSERT INTO activities (user_id,type,notes,image_path,points) VALUES (?,?,?,?,?)");
$stmtA->execute([$u['id'],'plastic','إضافة نقاط من السوبر ماركت',null,$points]);

echo json_encode(['success'=>true], JSON_UNESCAPED_UNICODE);
