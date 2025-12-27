<?php
require_once 'auth.php';
requireLogin(); // يمنع الدخول بدون تسجيل
$role = currentUserRole();          // user | shop | admin
$name = $_SESSION['user_name'] ?? '';
?>
<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>منصة إعادة التدوير العالمية</title>
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>
<body>

<header>
  <h1>منصة إعادة التدوير ECHO-SMART 🌱</h1>

  <div class="top-icons">
    <div class="icon" data-page="home" title="الصفحة الرئيسية">
      <i class="fa-solid fa-house"></i>
    </div>

    <?php if ($role === 'user' || $role === 'admin'): ?>
      <div class="icon" data-page="user" title="المستخدم">
        <i class="fa-solid fa-user"></i>
      </div>
    <?php endif; ?>

    <?php if ($role === 'shop' || $role === 'admin'): ?>
      <div class="icon" data-page="shop" title="السوبر ماركت">
        <i class="fa-solid fa-store"></i>
      </div>
    <?php endif; ?>

    <!-- أيقونة مسؤول محطة التجميع -->
<?php if ($role === 'collector' || $role === 'admin'): ?>
  <div class="icon" data-page="collector" title="محطة التجميع">
    <i class="fas fa-trash-alt"></i>
  </div>
<?php endif; ?>

<!-- أيقونة المندوب -->
<?php if ($role === 'delegate' || $role === 'admin'): ?>
  <div class="icon" data-page="delegate" title="صفحة المندوب">
    <i class="fas fa-truck"></i>
  </div>
<?php endif; ?>

    <?php if ($role === 'admin'): ?>
      <div class="icon" data-page="admin" title="الأدمن">
        <i class="fa-solid fa-shield-halved"></i>
      </div>

      <div class="icon" data-page="stats" title="الإحصائيات">
        <i class="fa-solid fa-chart-column"></i>
      </div>
    <?php endif; ?>

    <div class="icon" data-page="help" title="المساعدة">
      <i class="fa-solid fa-circle-question"></i>
    </div>

    <div class="icon" data-page="policy" title="القوانين والسياسات">
      <i class="fa-solid fa-scale-balanced"></i>
    </div>

    <div class="icon" data-page="rewards" title="الجوائز والكوبونات">
      <i class="fa-solid fa-gift"></i>
    </div>

    <?php if ($role === 'shop' || $role === 'admin'): ?>
      <div class="icon" data-page="news" title="الأخبار والإشعارات">
        <i class="fa-solid fa-newspaper"></i>
      </div>
    <?php endif; ?>

    <div class="icon" data-page="contact" title="تواصل معنا">
      <i class="fa-solid fa-envelope"></i>
    </div>
  </div>

  <div class="user-controls">
    <span id="userBadge">
      <?php echo htmlspecialchars($name).' ('.htmlspecialchars($role).')'; ?>
    </span>
    <button id="btn-logout" onclick="window.location.href='logout.php'">تسجيل خروج</button>
  </div>
</header>

<main>
  <!-- الصفحة الرئيسية -->
  <div class="page active" id="home">
    <h2>أهلاً بك في منصة إعادة التدوير ECHO-SMART 🌱</h2>
    <p>هذه المنصة تساعدك على إعادة تدوير البلاستيك، الزجاج، المعادن، والورق. تابع نقاطك وكوبوناتك، وأضف المواد لإعادة التدوير بسهولة.</p>
  </div>

  <?php if ($role === 'user' || $role === 'admin'): ?>
  <!-- صفحة المستخدم -->
  <div class="page" id="user">
    <h2>منصة المستخدم 🌱</h2>
    <div class="card user-card">
      <form id="depositForm">
        <select id="materialType">
          <option value="plastic">🟦 بلاستيك</option>
          <option value="glass">🟩 زجاج</option>
          <option value="metal">🟪 معادن</option>
          <option value="paper">📄 ورق</option>
        </select>
        <input type="text" id="itemNotes" placeholder="ملاحظات">
        <input type="file" id="itemImage">
        <button type="submit" class="btn-add">أضف مادة ♻️</button>
      </form>
      <div class="stats">
        <div>نقاطك: <span id="stat-points">0</span></div>
        <div>كوبوناتك: <span id="stat-coupons">0</span></div>
        <button id="btn-use-coupon" class="btn-coupon">صرف كوبون 🎁</button>
      </div>
      <table id="userActivities"><tbody></tbody></table>
    </div>
  </div>
  <?php endif; ?>

  <?php if ($role === 'shop' || $role === 'admin'): ?>
  <!-- صفحة السوبر ماركت -->
  <div class="page" id="shop">
    <h2>السوبر ماركت 🏪</h2>
    <div class="card shop-card">
      <input type="text" id="shopUserCode" placeholder="كود المستخدم">
      <input type="number" id="shopPoints" placeholder="عدد النقاط">
      <button id="shopAddPoints" class="btn-add">أضف نقاط ➕</button>
      <button id="shopUseCoupon" class="btn-coupon">خصم كوبون ➖</button>
      <h3>المستخدمين الجدد</h3>
      <div id="shopNewUsers"></div>
    </div>
  </div>
  <?php endif; ?>
  
<!-- صفحة مسؤول محطة التجميع -->
<?php if ($role === 'collector' || $role === 'admin'): ?>
<div class="page" id="collector">
  <div class="card">
    <h2 style="text-align:center; font-size:28px; color:#2e7d32;">محطة تجميع النفايات ECHO_SMART</h2>
    <p style="text-align:center; font-size:20px; color:#1b5e20;">أدخل كود المستخدم لتسجيل عملية التسليم</p>
    
    <div style="text-align:center; margin:30px 0;">
      <input type="text" id="collectorUserCode" placeholder="كود المستخدم" style="width:80%; max-width:400px; padding:15px; font-size:18px; text-align:center;">
      <br><br>
      <button id="collectorEnter" style="padding:15px 30px; font-size:20px; background:#2e7d32;">ادخال</button>
      <button id="collectorReceived" style="padding:15px 30px; font-size:20px; background:#1b5e20; margin-left:15px;">تم الاستلام</button>
    </div>

    <div id="collectorMessage" style="text-align:center; font-size:24px; font-weight:bold; margin-top:20px; color:green;"></div>
  </div>
</div>
<?php endif; ?>

<!-- صفحة المندوب -->
<?php if ($role === 'delegate' || $role === 'admin'): ?>
<div class="page" id="delegate">
  <div class="card">
    <h2 style="text-align:center; font-size:28px; color:#1976d2;">صفحة المندوب - ECHO_SMART</h2>
    
    <div style="text-align:center; margin:30px 0;">
      <input type="text" id="delegateCode" placeholder="أدخل كودك الشخصي" style="width:80%; max-width:400px; padding:15px; font-size:18px; text-align:center;">
      <br><br>
      <button id="delegateLogin" style="padding:15px 40px; font-size:20px; background:#1976d2;">ادخال</button>
    </div>

    <div id="delegateMessage" style="text-align:center; font-size:24px; font-weight:bold; color:green; margin:20px 0;"></div>

    <div style="text-align:center; background:#f5f5f5; padding:20px; border-radius:15px; margin:20px 0;">
      <h3 style="font-size:22px; color:#d32f2f;">العمليات الحاصلة عليها</h3>
      <p id="delegateOperations" style="font-size:20px; color:#555;">ليس هناك أي عمليات حتى الآن</p>
    </div>

    <div style="text-align:center; background:#e8f5e9; padding:20px; border-radius:15px;">
      <h3 style="font-size:22px; color:#2e7d32;">المبالغ المحصلة</h3>
      <p id="delegateEarnings" style="font-size:20px; color:#1b5e20; font-weight:bold;">ليس هناك مبالغ حتى الآن</p>
    </div>
  </div>
</div>
<?php endif; ?>

  <?php if ($role === 'admin'): ?>
  <!-- صفحة الأدمن -->
  <div class="page" id="admin">
    <h2>لوحة الأدمن 🛡️</h2>
    <div class="card admin-card">
      <h3>الإشعارات الأخيرة</h3>
      <ul id="adminAlerts"></ul>
      <h3>قائمة المستخدمين</h3>
      <table id="usersTable"><tbody></tbody></table>
      <h3>الكوبونات</h3>
      <table id="couponsTable"><tbody></tbody></table>
      <div>عدد المستخدمين: <span id="adminUserCount">0</span></div>
    </div>
  </div>

  <!-- صفحة الإحصائيات للأدمن فقط -->
  <div class="page" id="stats">
    <h2>الإحصائيات الشهرية 📊</h2>
    <p>تعرض الإحصائيات الخاصة بالمواد المعاد تدويرها وعدد المستخدمين النشطين. يمكن مراجعة التقدم والنجاحات من خلال الرسوم البيانية.</p>
    <img src=".//download (1).jpg" alt="">
  </div>
  <?php endif; ?>

  <!-- صفحة المساعدة -->
  <div class="page" id="help">
    <h2>مركز المساعدة ❓</h2>
    <p>تجد هنا إرشادات حول كيفية استخدام المنصة وإعادة التدوير بشكل صحيح. اقرأ التعليمات أو تواصل مع الدعم عند الحاجة.</p>
    <img src=".//ب.png" alt="">
  </div>

  <!-- صفحة القوانين والسياسات -->
  <div class="page" id="policy">
    <h2>الشروط والأحكام ⚖️</h2>
    <p>توضح هذه الصفحة حقوق وواجبات المستخدمين وسياسات الخصوصية. تصفح الشروط قبل استخدام المنصة لضمان الالتزام بالقوانين.</p>
    <p>الشروط والأحكام – منصة إعادة التدوير العالمية 🌱
    <!-- النص الطويل كما هو -->
    المنصة تقدم خدمات توعوية وتشجيعية على إعادة التدوير.
    المنصة غير مسؤولة عن أي أضرار مباشرة أو غير مباشرة قد تنتج عن استخدامك لها.</p>
  </div>

  <!-- صفحة الجوائز والكوبونات -->
  <div class="page" id="rewards">
    <h2>الجوائز والكوبونات 🎁</h2>
    <p>تعرض جميع المكافآت والكوبونات المتاحة للمستخدمين، مع طريقة الحصول عليها. اضغط على "صرف كوبون" عند استيفاء الشروط.</p>
    <img src=".//download.jpg" alt="">
  </div>

  <?php if ($role === 'shop' || $role === 'admin'): ?>
  <!-- صفحة الأخبار والإشعارات -->
  <div class="page" id="news">
    <h2>الأخبار والإشعارات 📰</h2>
    <p>عرض التحديثات حول المنصة، فعاليات إعادة التدوير، والمبادرات الجديدة. تابع الأخبار والإشعارات بشكل دوري.</p>
    <img src=".//download (2).jpg" alt="">
  </div>
  <?php endif; ?>

  <!-- صفحة التواصل معنا -->
  <div class="page" id="contact">
    <h2>تواصل معنا ✉️</h2>
    <p>أرسل اقتراحات أو استفسارات عبر هذه الصفحة وسيتم الرد عليك من قبل فريق الدعم.</p>
    <img src=".//download (3).jpg" alt="">
    <p>للتواصل موبايل01226700389</p>
    <p>الايميل : hossamhamdy201206@gmail.com</p>
  </div>
</main>


<script src="script.js"></script>

  <script>
// صفحة مسؤول المحطة
document.getElementById('collectorEnter')?.addEventListener('click', function() {
  const code = document.getElementById('collectorUserCode').value.trim();
  if (!code) {
    document.getElementById('collectorMessage').innerHTML = '<span style="color:red;">الرجاء إدخال كود المستخدم</span>';
    return;
  }
  document.getElementById('collectorMessage').innerHTML = '<span style="color:green;">تم الإدخال بنجاح</span>';
  document.getElementById('collectorUserCode').value = '';
});

document.getElementById('collectorReceived')?.addEventListener('click', function() {
  const code = document.getElementById('collectorUserCode').value.trim();
  if (!code) {
    document.getElementById('collectorMessage').innerHTML = '<span style="color:red;">الرجاء إدخال كود المستخدم أولاً</span>';
    return;
  }
  document.getElementById('collectorMessage').innerHTML = '<span style="color:green;">تم الاستلام بنجاح</span>';
});

// صفحة المندوب
document.getElementById('delegateLogin')?.addEventListener('click', function() {
  const code = document.getElementById('delegateCode').value.trim();
  if (!code) {
    document.getElementById('delegateMessage').innerHTML = '<span style="color:red;">أدخل كودك أولاً</span>';
    return;
  }
  document.getElementById('delegateMessage').innerHTML = '<span style="color:green;">تم الإدخال بنجاح</span>';
  
  // هنا ممكن تظهر عمليات وهمية أو تجيب من السيرفر بعدين
  document.getElementById('delegateOperations').innerText = "تم جمع 45 كجم بلاستيك من المستخدم UABC123";
  document.getElementById('delegateEarnings').innerText = "إجمالي المبالغ المحصلة: 850 جنيه";
});
</scrip>
document.querySelectorAll('.top-icons .icon').forEach(icon=>{
  icon.addEventListener('click', ()=>{
    const page = icon.dataset.page;
    document.querySelectorAll('.page').forEach(p=>p.classList.remove('active'));
    document.getElementById(page).classList.add('active');
  });
});
</script>

</body>
</html>
