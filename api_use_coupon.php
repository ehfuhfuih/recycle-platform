<?php
require_once 'auth.php';
requireLogin();
header('Content-Type: application/json; charset=utf-8');

$userId = currentUserId();

// إيجاد أول كوبون غير مستخدم
$stmt = $pdo->prepare("SELECT id,code FROM coupons WHERE user_id = ? AND used = 0 ORDER BY created_at ASC LIMIT 1");
$stmt->execute([$userId]);
$coupon = $stmt->fetch();

if (!$coupon) {
    echo json_encode(['success'=>false,'message'=>'لا يوجد كوبونات متاحة'], JSON_UNESCAPED_UNICODE);
    exit;
}

// تعليمه كمستخدم
$stmtU = $pdo->prepare("UPDATE coupons SET used = 1 WHERE id = ?");
$stmtU->execute([$coupon['id']]); 

echo json_encode(['success'=>true,'coupon_code'=>$coupon['code']], JSON_UNESCAPED_UNICODE);