// script.js (نسخة مكيّفة مع PHP + MySQL، الواجهة فقط من هنا)

const DOM = {
  userBadge: document.getElementById('userBadge'),
  depositForm: document.getElementById('depositForm'),
  userActivities: document.querySelector('#userActivities tbody'),
  adminAlerts: document.getElementById('adminAlerts'),
  couponsTable: document.querySelector('#couponsTable tbody'),
  usersTable: document.querySelector('#usersTable tbody'),
  btnUseCoupon: document.getElementById('btn-use-coupon'),
  shopUserCode: document.getElementById('shopUserCode'),
  shopPoints: document.getElementById('shopPoints'),
  shopAddPoints: document.getElementById('shopAddPoints'),
  shopUseCoupon: document.getElementById('shopUseCoupon'),
  shopNewUsers: document.getElementById('shopNewUsers')
};

const materialPoints = { plastic: 5, glass: 8, metal: 12, paper: 3 };

// الحالة العامة تأتي من السيرفر
let STATE = {
  me: null,          // {id,name,role,points,coupons}
  activities: [],    // أنشطة المستخدم الحالي أو الكل حسب الدور
  coupons: [],       // كوبونات
  alerts: [],        // إشعارات للأدمن
  users: []          // للمشاهدة في لوحة الأدمن
};

// تحميل الحالة من السيرفر
async function loadState() {
  try {
    const res = await fetch('api_get_state.php');
    if (!res.ok) throw new Error('خطأ في تحميل البيانات');
    const data = await res.json();
    STATE = data;
    refreshUI();
  } catch (e) {
    console.error(e);
    alert('حدث خطأ أثناء تحميل البيانات من السيرفر');
  }
}

// إرسال نشاط (إضافة مادة)
async function apiAddActivity(formData) {
  const res = await fetch('api_add_activity.php', {
    method: 'POST',
    body: formData
  });
  if (!res.ok) throw new Error('فشل حفظ النشاط');
  return await res.json();
}

// صرف كوبون
async function apiUseCoupon() {
  const res = await fetch('api_use_coupon.php', {
    method: 'POST'
  });
  if (!res.ok) throw new Error('فشل صرف الكوبون');
  return await res.json();
}

// السوبر ماركت: إضافة نقاط
async function apiShopAddPoints(userCode, pts) {
  const res = await fetch('api_shop_add_points.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ user_code: userCode, points: pts })
  });
  if (!res.ok) throw new Error('فشل إضافة النقاط');
  return await res.json();
}

// السوبر ماركت: خصم كوبون
async function apiShopUseCoupon(userCode) {
  const res = await fetch('api_shop_use_coupon.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ user_code: userCode })
  });
  if (!res.ok) throw new Error('فشل خصم الكوبون');
  return await res.json();
}

// معالجة فورم إضافة المواد
if (DOM.depositForm) {
  DOM.depositForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    const type = document.getElementById('materialType').value;
    const notes = document.getElementById('itemNotes').value.trim();
    const file = document.getElementById('itemImage').files[0] || null;

    const formData = new FormData();
    formData.append('type', type);
    formData.append('notes', notes);
    if (file) formData.append('image', file);

    try {
      const result = await apiAddActivity(formData);
      if (result.success) {
        await loadState();
        alert('تم تسجيل المادة وكسبت نقاط');
      } else {
        alert(result.message || 'فشل في حفظ البيانات');
      }
    } catch (err) {
      console.error(err);
      alert('حدث خطأ أثناء حفظ البيانات');
    }
  });
}

// زر صرف كوبون (للمستخدم)
if (DOM.btnUseCoupon) {
  DOM.btnUseCoupon.addEventListener('click', async () => {
    try {
      const result = await apiUseCoupon();
      if (result.success) {
        await loadState();
        alert('تم صرف الكوبون بنجاح: ' + (result.coupon_code || ''));
      } else {
        alert(result.message || 'لا يوجد كوبونات متاحة');
      }
    } catch (err) {
      console.error(err);
      alert('حدث خطأ أثناء صرف الكوبون');
    }
  });
}

// أزرار السوبر ماركت
if (DOM.shopAddPoints) {
  DOM.shopAddPoints.addEventListener('click', async () => {
    const userCode = DOM.shopUserCode.value.trim();
    const pts = parseInt(DOM.shopPoints.value) || 0;
    if (!userCode || !pts) return alert('أدخل كود المستخدم وعدد النقاط');
    try {
      const result = await apiShopAddPoints(userCode, pts);
      if (result.success) {
        await loadState();
        alert('تم إضافة النقاط بنجاح');
      } else {
        alert(result.message || 'فشل في إضافة النقاط');
      }
    } catch (err) {
      console.error(err);
      alert('حدث خطأ أثناء إضافة النقاط');
    }
  });
}

if (DOM.shopUseCoupon) {
  DOM.shopUseCoupon.addEventListener('click', async () => {
    const userCode = DOM.shopUserCode.value.trim();
    if (!userCode) return alert('أدخل كود المستخدم');
    try {
      const result = await apiShopUseCoupon(userCode);
      if (result.success) {
        await loadState();
        alert('تم خصم الكوبون بنجاح');
      } else {
        alert(result.message || 'لا يوجد كوبونات متاحة للمستخدم');
      }
    } catch (err) {
      console.error(err);
      alert('حدث خطأ أثناء خصم الكوبون');
    }
  });
}

// تحديث الواجهة من STATE
function refreshUI() {
  // بيانات المستخدم الحالي
  if (STATE.me) {
    const pts = STATE.me.points || 0;
    const couponsCount = STATE.me.coupons || 0;
    const elPts = document.getElementById('stat-points');
    const elCps = document.getElementById('stat-coupons');
    if (elPts) elPts.innerText = pts + ' نقاط';
    if (elCps) elCps.innerText = couponsCount + ' كوبون';
    if (DOM.btnUseCoupon) DOM.btnUseCoupon.disabled = couponsCount === 0;
  }

  // جدول الأنشطة
  if (DOM.userActivities) {
    DOM.userActivities.innerHTML = '';
    (STATE.activities || []).forEach(a => {
      const tr = document.createElement('tr');
      const imgHtml = a.image_url ? `<img class="img-thumb" src="${a.image_url}">` : '—';
      tr.innerHTML = `
        <td>${imgHtml}</td>
        <td>${a.type} ${a.notes ? '- ' + a.notes : ''}</td>
        <td>${a.points}</td>
        <td>${a.created_at}</td>
      `;
      DOM.userActivities.appendChild(tr);
    });
  }

  // جدول المستخدمين (أدمن)
  if (DOM.usersTable && Array.isArray(STATE.users)) {
    DOM.usersTable.innerHTML = '';
    STATE.users.forEach(u => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${u.id}</td>
        <td>${u.name}</td>
        <td>${u.role}</td>
        <td>${u.points || 0}</td>
      `;
      DOM.usersTable.appendChild(tr);
    });
    const cnt = document.getElementById('adminUserCount');
    if (cnt) cnt.innerText = STATE.users.length;
  }

  // جدول الكوبونات
  if (DOM.couponsTable && Array.isArray(STATE.coupons)) {
    DOM.couponsTable.innerHTML = '';
    STATE.coupons.forEach(c => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${c.code}</td>
        <td>${c.value}</td>
        <td>${c.user_name || c.user_id}</td>
        <td>${c.used ? 'مستخدم' : 'متاح'}</td>
      `;
      DOM.couponsTable.appendChild(tr);
    });
  }

  // الإشعارات (أدمن)
  if (DOM.adminAlerts && Array.isArray(STATE.alerts)) {
    DOM.adminAlerts.innerHTML = '';
    STATE.alerts.forEach(a => {
      const li = document.createElement('li');
      li.innerText = a.text + ' — ' + a.created_at;
      DOM.adminAlerts.appendChild(li);
    });
  }
}

// تحميل الحالة عند فتح الصفحة
loadState();
