<?php
require_once 'auth.php';
requireLogin();
header('Content-Type: application/json; charset=utf-8');

$userId = currentUserId();

$type  = $_POST['type']  ?? '';
$notes = $_POST['notes'] ?? '';
$allowed = ['plastic','glass','metal','paper'];

if (!in_array($type, $allowed, true)) {
    echo json_encode(['success'=>false,'message'=>'نوع المادة غير صحيح'], JSON_UNESCAPED_UNICODE);
    exit;
}

// حفظ الصورة (اختياري)
$imagePath = null;
if (!empty($_FILES['image']['name'])) {
    $uploadDir  = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
    $fileName   = time().'_'.basename($_FILES['image']['name']);
    $targetPath = $uploadDir.$fileName;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
        $imagePath = $targetPath;
    }
}

// حساب النقاط
$materialPoints = ['plastic'=>5,'glass'=>8,'metal'=>12,'paper'=>3];
$points = $materialPoints[$type] ?? 1;

// إدخال النشاط
$stmt = $pdo->prepare("INSERT INTO activities (user_id,type,notes,image_path,points) VALUES (?,?,?,?,?)");
$stmt->execute([$userId,$type,$notes,$imagePath,$points]);

// توليد كوبونات حسب النقاط المتراكمة (مثال بسيط: كل 30 نقطة كوبون)
$stmtSum = $pdo->prepare("SELECT COALESCE(SUM(points),0) AS total FROM activities WHERE user_id = ?");
$stmtSum->execute([$userId]);
$totalPoints = (int)$stmtSum->fetch()['total'];

$couponValue = 20;
while ($totalPoints >= 30) {
    $code = 'C'.strtoupper(substr(md5(uniqid()),0,5));
    $stmtC = $pdo->prepare("INSERT INTO coupons (code,user_id,value,used) VALUES (?,?,?,0)");
    $stmtC->execute([$code,$userId,$couponValue]);
    $totalPoints -= 30;
}

echo json_encode(['success'=>true], JSON_UNESCAPED_UNICODE);
