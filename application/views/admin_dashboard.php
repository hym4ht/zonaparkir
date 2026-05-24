<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Dashboard admin monitoring sistem parkir IoT ESP32">
    <title>Dashboard Admin — Info Parkir</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        :root {
            --bg:       #f0f4f8;
            --sidebar:  #0f172a;
            --card:     #ffffff;
            --blue:     #3b82f6;
            --green:    #22c55e;
            --red:      #ef4444;
            --amber:    #f59e0b;
            --indigo:   #6366f1;
            --txt:      #1e293b;
            --muted:    #64748b;
            --border:   #e2e8f0;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--txt);
        }

        /* ===== NAVBAR ===== */
        .admin-nav {
            background: var(--sidebar);
            padding: 0.85rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }

        .nav-brand {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-brand i { color: var(--blue); }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-size: 0.82rem;
            color: #94a3b8;
        }

        .live-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            background: rgba(34,197,94,0.15);
            border: 1px solid rgba(34,197,94,0.3);
            color: var(--green);
            font-size: 0.72rem;
            font-weight: 600;
            padding: 0.25rem 0.7rem;
            border-radius: 999px;
            text-transform: uppercase;
        }

        .live-dot {
            width: 6px; height: 6px;
            background: var(--green);
            border-radius: 50%;
            animation: blink 1.5s infinite;
        }

        @keyframes blink {
            0%,100% { opacity:1; } 50% { opacity:0.3; }
        }

        /* Navigasi Responsif Admin */
        .nav-toggle {
            display: none;
            background: none;
            border: none;
            color: #fff;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
            transition: background 0.2s;
        }

        .nav-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        @media (max-width: 991px) {
            .admin-nav {
                flex-wrap: wrap;
                padding: 0.85rem 1.5rem;
            }

            .nav-toggle {
                display: block;
            }

            .nav-right {
                display: none;
                width: 100%;
                flex-direction: column;
                align-items: stretch !important;
                gap: 0.85rem !important;
                padding-top: 1rem;
                padding-bottom: 0.5rem;
                border-top: 1px solid rgba(255, 255, 255, 0.08);
                margin-top: 0.85rem;
            }

            .nav-right.show {
                display: flex;
            }

            .nav-right a, 
            .nav-right .live-pill, 
            .nav-right span {
                width: 100%;
                justify-content: flex-start !important;
                text-align: left;
            }

            .nav-right a {
                padding: 0.6rem 1rem !important;
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 10px;
                display: flex !important;
                align-items: center;
                gap: 0.5rem;
            }

            .nav-right a:hover {
                background: rgba(255, 255, 255, 0.08) !important;
            }

            .nav-right .live-pill {
                padding: 0.5rem 1rem;
                border-radius: 10px;
            }

            .nav-right #nav-time {
                padding: 0.25rem 0.5rem;
                color: var(--muted);
            }
        }

        /* ===== LAYOUT ===== */
        .page { padding: 1.5rem; max-width: 1400px; margin: 0 auto; }

        .page-title {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--txt);
            margin-bottom: 0.25rem;
        }

        .page-sub {
            font-size: 0.85rem;
            color: var(--muted);
            margin-bottom: 1.5rem;
        }

        /* ===== STAT CARDS ===== */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .kpi-card {
            background: var(--card);
            border-radius: 16px;
            padding: 1.25rem 1.25rem 1rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            border-top: 4px solid transparent;
            transition: transform 0.2s ease;
        }

        .kpi-card:hover { transform: translateY(-2px); }

        .kpi-card.c-blue   { border-color: var(--blue); }
        .kpi-card.c-green  { border-color: var(--green); }
        .kpi-card.c-red    { border-color: var(--red); }
        .kpi-card.c-amber  { border-color: var(--amber); }
        .kpi-card.c-indigo { border-color: var(--indigo); }

        .kpi-label {
            font-size: 0.72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--muted);
            margin-bottom: 0.5rem;
        }

        .kpi-value {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1;
            font-variant-numeric: tabular-nums;
            transition: transform 0.2s ease;
        }

        .kpi-card.c-blue   .kpi-value { color: var(--blue); }
        .kpi-card.c-green  .kpi-value { color: var(--green); }
        .kpi-card.c-red    .kpi-value { color: var(--red); }
        .kpi-card.c-amber  .kpi-value { color: var(--amber); }
        .kpi-card.c-indigo .kpi-value { color: var(--indigo); }

        .kpi-icon {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }

        .kpi-card.c-blue   .kpi-icon { color: var(--blue); }
        .kpi-card.c-green  .kpi-icon { color: var(--green); }
        .kpi-card.c-red    .kpi-icon { color: var(--red); }
        .kpi-card.c-amber  .kpi-icon { color: var(--amber); }
        .kpi-card.c-indigo .kpi-icon { color: var(--indigo); }

        /* ===== SECTION PANEL ===== */
        .panel {
            background: var(--card);
            border-radius: 16px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }

        .panel-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .panel-title {
            font-weight: 700;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .panel-body { padding: 1.25rem; }

        /* ===== SLOT MONITOR ===== */
        .slot-monitor {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .slot-box {
            width: 90px;
            height: 90px;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.8rem;
            gap: 0.25rem;
            transition: all 0.3s ease;
            position: relative;
        }

        .slot-box .slot-label {
            font-size: 0.68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .slot-box.kosong {
            background: rgba(34,197,94,0.12);
            border: 2px solid rgba(34,197,94,0.4);
            color: #16a34a;
            box-shadow: 0 0 14px rgba(34,197,94,0.2);
        }

        .slot-box.terisi {
            background: rgba(239,68,68,0.1);
            border: 2px solid rgba(239,68,68,0.4);
            color: #dc2626;
            box-shadow: 0 0 14px rgba(239,68,68,0.2);
        }

        /* ===== LOG TABLE ===== */
        .log-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .log-table th {
            background: #f8fafc;
            color: var(--muted);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 0.6rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
        }

        .log-table td {
            padding: 0.65rem 1rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
        }

        .log-table tr:last-child td { border-bottom: none; }
        .log-table tr:hover td { background: #f8fafc; }

        .badge-masuk  { background: rgba(34,197,94,0.1);  color: #16a34a; border: 1px solid rgba(34,197,94,0.3); }
        .badge-keluar { background: rgba(239,68,68,0.1);  color: #dc2626; border: 1px solid rgba(239,68,68,0.3); }

        .badge-custom {
            display: inline-flex; align-items: center; gap: 0.3rem;
            font-size: 0.75rem; font-weight: 600;
            padding: 0.25rem 0.65rem; border-radius: 999px;
        }

        /* ===== OLD PARKIR TABLE ===== */
        .table-hover tbody tr:hover { background-color: #f8fafc; }

        /* ===== UPDATE INDICATOR ===== */
        .update-info {
            font-size: 0.72rem;
            color: var(--muted);
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .update-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--green);
            animation: blink 2s infinite;
        }
    </style>
</head>
<body>

<nav class="admin-nav">
    <div class="nav-brand">
        <i class="bi bi-car-front-fill"></i>
        Admin — Info Parkir
    </div>
    <button class="nav-toggle" id="nav-toggle" aria-label="Toggle Menu">
        <i class="bi bi-list"></i>
    </button>
    <div class="nav-right">
        <a href="<?= site_url('admin') ?>" style="color:#fff;text-decoration:none;font-size:.82rem;font-weight:500;padding:.3rem .65rem;border-radius:8px;background:rgba(255,255,255,.1)"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="<?= site_url('admin/manage_admin') ?>" style="color:#94a3b8;text-decoration:none;font-size:.82rem;font-weight:500;padding:.3rem .65rem;border-radius:8px;transition:all .2s" onmouseover="this.style.color='#fff';this.style.background='rgba(255,255,255,.1)'" onmouseout="this.style.color='#94a3b8';this.style.background='none'"><i class="bi bi-people-fill"></i> Kelola Admin</a>
        <div class="live-pill">
            <div class="live-dot"></div>
            Realtime
        </div>
        <span id="nav-time"><?= date('d M Y · H:i') ?></span>
        <!-- Info user login -->
        <span style="display:flex;align-items:center;gap:0.4rem;color:#cbd5e1;font-size:0.82rem;">
            <i class="bi bi-person-circle" style="color:#6366f1"></i>
            <?= htmlspecialchars($this->session->userdata('admin_username') ?? 'admin') ?>
        </span>
        <!-- Tombol Logout -->
        <a href="<?= site_url('logout') ?>" 
           title="Logout"
           onclick="return confirm('Yakin ingin logout?')"
           style="
               display:inline-flex;align-items:center;gap:0.35rem;
               background:rgba(239,68,68,0.15);border:1px solid rgba(239,68,68,0.3);
               color:#fca5a5;font-size:0.78rem;font-weight:600;
               padding:0.3rem 0.75rem;border-radius:999px;
               text-decoration:none;transition:all 0.2s;
           "
           onmouseover="this.style.background='rgba(239,68,68,0.3)'"
           onmouseout="this.style.background='rgba(239,68,68,0.15)'">
            <i class="bi bi-box-arrow-right"></i>
            Logout
        </a>
    </div>
</nav>

<div class="page">

    <!-- PAGE TITLE -->
    <div class="page-title">Dashboard Admin</div>
    <div class="page-sub">Monitoring parkir realtime — data diperbarui otomatis dari ESP32</div>

    <!-- KPI CARDS -->
    <div class="kpi-grid">

        <div class="kpi-card c-blue">
            <div class="kpi-icon"><i class="bi bi-grid-1x2-fill"></i></div>
            <div class="kpi-label">Total Slot</div>
            <div class="kpi-value" id="kpi-total"><?= $total_slot ?></div>
        </div>

        <div class="kpi-card c-green">
            <div class="kpi-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="kpi-label">Slot Kosong</div>
            <div class="kpi-value" id="kpi-kosong"><?= $slot_kosong ?></div>
        </div>

        <div class="kpi-card c-red">
            <div class="kpi-icon"><i class="bi bi-x-circle-fill"></i></div>
            <div class="kpi-label">Slot Terisi</div>
            <div class="kpi-value" id="kpi-terisi"><?= $slot_terisi ?></div>
        </div>

        <div class="kpi-card c-indigo">
            <div class="kpi-icon"><i class="bi bi-arrow-down-circle-fill"></i></div>
            <div class="kpi-label">Masuk Hari Ini</div>
            <div class="kpi-value" id="kpi-masuk">—</div>
        </div>

    </div>

    <!-- SLOT MONITOR -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">
                <i class="bi bi-radar" style="color:var(--blue)"></i>
                Monitoring Slot Parkir
            </div>
            <div class="update-info">
                <div class="update-dot"></div>
                <span id="slot-updated">Update: —</span>
            </div>
        </div>
        <div class="panel-body">
            <div class="slot-monitor" id="slot-monitor">

                <?php foreach($slot as $s): ?>
                <div class="slot-box <?= $s->status ?>" id="admin-slot-<?= strtolower($s->kode_slot) ?>">
                    <?= $s->kode_slot ?>
                    <span class="slot-label"><?= $s->status ?></span>
                </div>
                <?php endforeach; ?>

            </div>
            <div class="mt-3 d-flex gap-2 align-items-center">
                <span class="badge-custom badge-masuk"><i class="bi bi-check-circle-fill"></i> Kosong</span>
                <span class="badge-custom badge-keluar"><i class="bi bi-x-circle-fill"></i> Terisi</span>
            </div>
        </div>
    </div>

    <!-- LOG AKSES (dari ESP32) -->
    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">
                <i class="bi bi-activity" style="color:var(--indigo)"></i>
                Riwayat Kendaraan
            </div>
            <div class="update-info">
                <div class="update-dot"></div>
                <span id="log-updated">Update: —</span>
            </div>
        </div>
        <div class="panel-body" style="padding:0">
            <div class="table-responsive">
                <table class="log-table" id="log-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Status</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody id="log-tbody">
                        <tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:1.5rem">Memuat data...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>


</div><!-- /page -->

<script>
const BASE_URL = '<?= base_url() ?>';

// ===== Realtime clock navbar =====
setInterval(() => {
    const n = new Date();
    const bulan = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];
    const t = n.getDate() + ' ' + bulan[n.getMonth()] + ' ' + n.getFullYear();
    const j = String(n.getHours()).padStart(2,'0') + ':' + String(n.getMinutes()).padStart(2,'0');
    document.getElementById('nav-time').textContent = t + ' · ' + j;
}, 1000);

// ===== Polling slot setiap 2 detik =====
function fetchSlot() {
    fetch(BASE_URL + 'api/slot_json')
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;

            updateKPI('kpi-total',  d.total_slot);
            updateKPI('kpi-kosong', d.kosong);
            updateKPI('kpi-terisi', d.terisi);
            document.getElementById('slot-updated').textContent = 'Update: ' + d.updated_at;

            d.slot.forEach(s => {
                const box = document.getElementById('admin-slot-' + s.kode.toLowerCase());
                if (!box) return;
                box.className = 'slot-box ' + s.status;
                box.childNodes[0].textContent = s.kode;
                box.querySelector('.slot-label').textContent = s.status;
            });
        })
        .catch(() => {});
}

// ===== Polling log akses setiap 3 detik =====
function fetchLog() {
    fetch(BASE_URL + 'api/log_json')
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;

            updateKPI('kpi-masuk', d.masuk_hari);
            document.getElementById('log-updated').textContent = 'Update: ' + d.updated_at;

            const tbody = document.getElementById('log-tbody');
            if (d.log.length === 0) {
                tbody.innerHTML = '<tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:1.5rem">Belum ada log hari ini</td></tr>';
                return;
            }

            let html = '';
            d.log.forEach((l, i) => {
                const isMasuk = l.jenis === 'masuk';
                html += `
                <tr>
                    <td>${i + 1}</td>
                    <td>
                        <span class="badge-custom ${isMasuk ? 'badge-masuk' : 'badge-keluar'}">
                            <i class="bi bi-arrow-${isMasuk ? 'down' : 'up'}-circle-fill"></i>
                            ${l.jenis.charAt(0).toUpperCase() + l.jenis.slice(1)}
                        </span>
                    </td>
                    <td>${l.waktu}</td>
                </tr>`;
            });
            tbody.innerHTML = html;
        })
        .catch(() => {});
}

function updateKPI(id, val) {
    const el = document.getElementById(id);
    if (!el) return;
    const old = parseInt(el.textContent) || 0;
    if (old !== val) {
        el.textContent = val;
        el.style.transform = 'scale(1.15)';
        el.style.transition = 'transform 0.2s ease';
        setTimeout(() => { el.style.transform = 'scale(1)'; }, 250);
    }
}

// Toggle menu responsif
document.getElementById('nav-toggle').addEventListener('click', function() {
    document.querySelector('.nav-right').classList.toggle('show');
});

// Mulai polling
setInterval(fetchSlot, 2000);
setInterval(fetchLog,  3000);

// Panggil langsung saat load
fetchSlot();
fetchLog();
</script>

</body>
</html>