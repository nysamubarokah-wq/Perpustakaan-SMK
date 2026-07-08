<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['user' => null]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['user' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$user = $user ?? auth()->user();
$unreadCount = $unreadCount ?? 0;
?>

<style>
.notification-bell-wrapper {
    position: relative;
}

.notification-bell-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.2s;
}

.notification-bell-btn:hover {
    background: rgba(0,0,0,0.05);
}

body.dark-mode .notification-bell-btn:hover {
    background: rgba(255,255,255,0.1);
}

.notification-bell-btn i {
    font-size: 20px;
}

.notification-badge {
    position: absolute;
    top: 2px;
    right: 2px;
    background: #e74c3c;
    color: white;
    border-radius: 50%;
    min-width: 18px;
    height: 18px;
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 4px;
    animation: notifPulse 2s infinite;
}

@keyframes notifPulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

.notification-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 100%;
    width: 340px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    z-index: 9999;
    overflow: hidden;
    animation: dropdownFadeIn 0.2s ease;
}

body.dark-mode .notification-dropdown {
    background: #16213e;
    box-shadow: 0 10px 40px rgba(0,0,0,0.4);
}

@keyframes dropdownFadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.notification-dropdown-header {
    padding: 14px 16px;
    border-bottom: 1px solid #eee;
    font-weight: 700;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

body.dark-mode .notification-dropdown-header {
    border-color: #2a2a4a;
}

.notification-dropdown-header .notif-title {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #222;
}

body.dark-mode .notification-dropdown-header .notif-title {
    color: #e0e0e0;
}

.notification-dropdown-list {
    max-height: 350px;
    overflow-y: auto;
}

.notification-item {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: background 0.15s;
    display: block;
    text-decoration: none;
}

body.dark-mode .notification-item {
    border-color: #2a2a4a;
}

.notification-item:hover {
    background: #f8f9fa;
}

body.dark-mode .notification-item:hover {
    background: rgba(255,255,255,0.05);
}

.notification-item.unread {
    background: #f0fdf4;
}

body.dark-mode .notification-item.unread {
    background: rgba(39, 174, 96, 0.1);
}

.notification-item-icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-right: 12px;
}

.notification-item-icon i {
    font-size: 16px;
}

.notification-item-content {
    flex: 1;
    min-width: 0;
}

.notification-item-title {
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 2px;
    color: #222;
}

body.dark-mode .notification-item-title {
    color: #e0e0e0;
}

.notification-item-message {
    font-size: 12px;
    color: #666;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

body.dark-mode .notification-item-message {
    color: #a0a0b0;
}

.notification-item-time {
    font-size: 11px;
    color: #999;
    margin-top: 4px;
}

body.dark-mode .notification-item-time {
    color: #808090;
}

.notification-dropdown-footer {
    padding: 12px 16px;
    border-top: 1px solid #eee;
    text-align: center;
}

body.dark-mode .notification-dropdown-footer {
    border-color: #2a2a4a;
}

.notification-dropdown-footer a {
    color: #1a6e35;
    font-weight: 600;
    font-size: 13px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.notification-dropdown-footer a:hover {
    text-decoration: underline;
}

body.dark-mode .notification-dropdown-footer a {
    color: #81c784;
}

.notification-empty {
    padding: 30px 16px;
    text-align: center;
    color: #999;
    font-size: 13px;
}

.notification-empty i {
    font-size: 32px;
    display: block;
    margin-bottom: 8px;
    opacity: 0.5;
}

body.dark-mode .notification-empty {
    color: #808090;
}

.notification-mark-read-btn {
    font-size: 11px;
    color: #1a6e35;
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
}

.notification-mark-read-btn:hover {
    background: rgba(26, 110, 53, 0.1);
}

body.dark-mode .notification-mark-read-btn {
    color: #81c784;
}

body.dark-mode .notification-mark-read-btn:hover {
    background: rgba(129, 199, 132, 0.1);
}

@media (max-width: 576px) {
    .notification-dropdown {
        width: 290px;
        right: -60px;
    }
}
</style>

<div class="notification-bell-wrapper" id="notificationBellWrapper">
    <button class="notification-bell-btn" onclick="toggleNotificationDropdown(event)" title="Notifikasi" id="notificationBellBtn">
        <i class="bi bi-bell" id="notificationBellIcon"></i>
        <?php if($unreadCount > 0): ?>
            <span class="notification-badge" id="notificationBadge"><?php echo e($unreadCount > 99 ? '99+' : $unreadCount); ?></span>
        <?php endif; ?>
    </button>

    <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-dropdown-header">
            <span class="notif-title">
                <i class="bi bi-bell-fill" style="color: #1a6e35"></i>
                Notifikasi
            </span>
            <?php if($unreadCount > 0): ?>
                <button class="notification-mark-read-btn" onclick="markAllNotificationsRead(event)" id="markAllReadBtn">
                    <i class="bi bi-check-all"></i> Tandai semua dibaca
                </button>
            <?php endif; ?>
        </div>

        <div class="notification-dropdown-list" id="notificationList">
            <div class="notification-empty" id="notificationEmpty">
                <i class="bi bi-bell-slash"></i>
                Tidak ada notifikasi baru
            </div>
        </div>

        <div class="notification-dropdown-footer">
            <a href="<?php echo e(route('notifikasi.go')); ?>">
                <i class="bi bi-list-ul"></i>
                Lihat Semua Notifikasi
            </a>
        </div>
    </div>
</div>

<script>
let notificationDropdownOpen = false;
let lastNotificationUpdate = 0;

function toggleNotificationDropdown(event) {
    event.stopPropagation();
    const dropdown = document.getElementById('notificationDropdown');
    
    if (dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
        notificationDropdownOpen = false;
    } else {
        dropdown.style.display = 'block';
        notificationDropdownOpen = true;
        
        if (Date.now() - lastNotificationUpdate > 5000) {
            loadNotifications();
        }
    }
}

function loadNotifications() {
    fetch('<?php echo e(route("notifikasi.latest")); ?>', {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        renderNotifications(data.notifikasi);
        updateBadge(data.count);
        lastNotificationUpdate = Date.now();
    })
    .catch(error => console.error('Error loading notifications:', error));
}

function renderNotifications(notifications) {
    const container = document.getElementById('notificationList');
    const empty = document.getElementById('notificationEmpty');
    
    if (!notifications || notifications.length === 0) {
        container.innerHTML = '<div class="notification-empty" id="notificationEmpty"><i class="bi bi-bell-slash"></i>Tidak ada notifikasi baru</div>';
        return;
    }
    
    container.innerHTML = notifications.map(notif => {
        const iconBg = getIconBackground(notif.icon, notif.warna);
        const timeAgo = getTimeAgo(notif.created_at);
        const unreadClass = notif.is_read ? '' : 'unread';
        
        return `
            <a href="${notif.link ? notif.link : '#'}" 
               class="notification-item ${unreadClass}"
               onclick="markNotificationRead(${notif.id}, event)"
               data-notif-id="${notif.id}">
                <div style="display:flex;align-items:flex-start">
                    <div class="notification-item-icon" style="background:${iconBg}">
                        <i class="bi bi-${getIconName(notif.icon)}" style="color:white"></i>
                    </div>
                    <div class="notification-item-content">
                        <div class="notification-item-title">${escapeHtml(notif.judul)}</div>
                        <div class="notification-item-message">${escapeHtml(notif.pesan)}</div>
                        <div class="notification-item-time">${timeAgo}</div>
                    </div>
                </div>
            </a>
        `;
    }).join('');
}

function markNotificationRead(notifId, event) {
    event.preventDefault();
    
    fetch(`/notifikasi/${notifId}/baca`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const item = document.querySelector(`[data-notif-id="${notifId}"]`);
            if (item) item.classList.remove('unread');
            updateBadge(data.new_count ?? (parseInt(document.getElementById('notificationBadge')?.textContent || 0) - 1));
        }
        
        if (event.target.closest('a')?.href) {
            window.location.href = event.target.closest('a').href;
        }
    })
    .catch(error => {
        console.error('Error marking notification as read:', error);
        if (event.target.closest('a')?.href) {
            window.location.href = event.target.closest('a').href;
        }
    });
}

function markAllNotificationsRead(event) {
    event.stopPropagation();
    
    fetch('<?php echo e(route("notifikasi.bacaSemua")); ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelectorAll('.notification-item.unread').forEach(el => {
                el.classList.remove('unread');
            });
            updateBadge(0);
            const btn = document.getElementById('markAllReadBtn');
            if (btn) btn.style.display = 'none';
        }
    })
    .catch(error => console.error('Error marking all as read:', error));
}

function updateBadge(count) {
    const badge = document.getElementById('notificationBadge');
    const icon = document.getElementById('notificationBellIcon');
    
    if (count > 0) {
        if (!badge) {
            const newBadge = document.createElement('span');
            newBadge.className = 'notification-badge';
            newBadge.id = 'notificationBadge';
            newBadge.textContent = count > 99 ? '99+' : count;
            document.getElementById('notificationBellBtn').appendChild(newBadge);
        } else {
            badge.textContent = count > 99 ? '99+' : count;
            badge.style.display = 'flex';
        }
        if (icon) icon.className = 'bi bi-bell-fill';
    } else {
        if (badge) badge.style.display = 'none';
        if (icon) icon.className = 'bi bi-bell';
    }
}

function getIconBackground(icon, warna) {
    const colors = {
        'book': '#27ae60',
        'arrow-repeat': '#3498db',
        'clock': '#f39c12',
        'exclamation-triangle': '#e74c3c',
        'coin': '#f1c40f',
        'star': '#f1c40f',
        'check-circle': '#27ae60',
        'x-circle': '#e74c3c',
        'bell': '#6c757d',
    };
    return colors[icon] || warna || '#6c757d';
}

function getIconName(icon) {
    return icon || 'bell';
}

function getTimeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffSec = Math.floor(diffMs / 1000);
    const diffMin = Math.floor(diffSec / 60);
    const diffHour = Math.floor(diffMin / 60);
    const diffDay = Math.floor(diffHour / 24);
    
    if (diffSec < 60) return 'Baru saja';
    if (diffMin < 60) return `${diffMin} menit lalu`;
    if (diffHour < 24) return `${diffHour} jam lalu`;
    if (diffDay < 7) return `${diffDay} hari lalu`;
    
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const wrapper = document.getElementById('notificationBellWrapper');
    const dropdown = document.getElementById('notificationDropdown');
    
    if (wrapper && dropdown && notificationDropdownOpen) {
        if (!wrapper.contains(event.target)) {
            dropdown.style.display = 'none';
            notificationDropdownOpen = false;
        }
    }
});

// Poll for new notifications every 30 seconds
setInterval(function() {
    if (document.getElementById('notificationBellWrapper')) {
        loadNotifications();
    }
}, 30000);

// Initial load
if (document.getElementById('notificationBellWrapper')) {
    loadNotifications();
}
</script>
<?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views\components\notification-bell.blade.php ENDPATH**/ ?>