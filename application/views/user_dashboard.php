<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Monitoring ketersediaan slot parkir secara realtime dengan sensor IoT ESP32">
    <title>Info Parkir — Monitoring Realtime</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        :root {
            --bg-dark:      #0a0e1a;
            --bg-card:      #111827;
            --bg-card2:     #1a2235;
            --accent-blue:  #3b82f6;
            --accent-green: #22c55e;
            --accent-red:   #ef4444;
            --accent-amber: #f59e0b;
            --text-primary: #f1f5f9;
            --text-muted:   #64748b;
            --border:       rgba(255,255,255,0.07);
            --glow-green:   0 0 20px rgba(34,197,94,0.35);
            --glow-red:     0 0 20px rgba(239,68,68,0.35);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ======= ANIMATED BG ======= */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(ellipse at 20% 20%, rgba(59,130,246,0.08) 0%, transparent 60%),
                        radial-gradient(ellipse at 80% 80%, rgba(34,197,94,0.06) 0%, transparent 60%);
            pointer-events: none;
            z-index: 0;
        }

        .wrapper {
            position: relative;
            z-index: 1;
            padding: 2rem 1rem 4rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* ======= HEADER ======= */
        .hero {
            text-align: center;
            padding: 3rem 1rem 2rem;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(34,197,94,0.15);
            border: 1px solid rgba(34,197,94,0.3);
            color: var(--accent-green);
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            padding: 0.3rem 0.8rem;
            border-radius: 999px;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
        }

        .live-dot {
            width: 7px;
            height: 7px;
            background: var(--accent-green);
            border-radius: 50%;
            animation: pulse-dot 1.5s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: 0.4; transform: scale(0.7); }
        }

        .hero h1 {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 900;
            background: linear-gradient(135deg, #fff 0%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.1;
            margin-bottom: 0.75rem;
        }

        .hero p {
            color: var(--text-muted);
            font-size: 1.05rem;
            max-width: 420px;
            margin: 0 auto;
        }

        /* ======= CLOCK ======= */
        .clock-wrap {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .clock-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.5rem 1.1rem;
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        #jam-besar {
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--accent-blue);
            font-variant-numeric: tabular-nums;
        }

        /* ======= STAT CARDS ======= */
        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.25rem;
            margin: 2.5rem 0;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .stat-card:hover { transform: translateY(-3px); }

        .stat-card.blue  { border-color: rgba(59,130,246,0.3); }
        .stat-card.green { border-color: rgba(34,197,94,0.3); }
        .stat-card.red   { border-color: rgba(239,68,68,0.3); }

        .stat-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .stat-card.blue  .stat-icon { background: rgba(59,130,246,0.15); color: var(--accent-blue); }
        .stat-card.green .stat-icon { background: rgba(34,197,94,0.15);  color: var(--accent-green); }
        .stat-card.red   .stat-icon { background: rgba(239,68,68,0.15);  color: var(--accent-red); }

        .stat-label {
            font-size: 0.78rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.25rem;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1;
            font-variant-numeric: tabular-nums;
        }

        .stat-card.blue  .stat-value { color: var(--accent-blue); }
        .stat-card.green .stat-value { color: var(--accent-green); }
        .stat-card.red   .stat-value { color: var(--accent-red); }

        /* ======= SECTION TITLE ======= */
        .section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .section-sub {
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .last-update {
            font-size: 0.75rem;
            color: var(--text-muted);
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        /* ======= SLOT GRID ======= */
        .slot-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .slot-card {
            background: var(--bg-card);
            border: 2px solid var(--border);
            border-radius: 22px;
            padding: 2rem 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: default;
        }

        .slot-card::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.35s ease;
        }

        .slot-card.kosong {
            border-color: rgba(34,197,94,0.5);
            box-shadow: var(--glow-green);
        }

        .slot-card.kosong::before {
            background: radial-gradient(ellipse at center, rgba(34,197,94,0.12) 0%, transparent 70%);
            opacity: 1;
        }

        .slot-card.terisi {
            border-color: rgba(239,68,68,0.5);
            box-shadow: var(--glow-red);
        }

        .slot-card.terisi::before {
            background: radial-gradient(ellipse at center, rgba(239,68,68,0.12) 0%, transparent 70%);
            opacity: 1;
        }

        .slot-card:hover { transform: translateY(-4px) scale(1.02); }

        .slot-icon-wrap {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            transition: transform 0.3s ease;
        }

        .slot-card:hover .slot-icon-wrap { transform: scale(1.1); }

        .slot-kode {
            font-size: 2.8rem;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .slot-card.kosong .slot-kode { color: var(--accent-green); }
        .slot-card.terisi .slot-kode { color: var(--accent-red); }

        .slot-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 0.3rem 0.8rem;
            border-radius: 999px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .slot-card.kosong .slot-badge {
            background: rgba(34,197,94,0.15);
            color: var(--accent-green);
            border: 1px solid rgba(34,197,94,0.3);
        }

        .slot-card.terisi .slot-badge {
            background: rgba(239,68,68,0.15);
            color: var(--accent-red);
            border: 1px solid rgba(239,68,68,0.3);
        }

        /* ======= STATUS BAR ======= */
        .status-bar {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .progress-wrap {
            flex: 1;
            min-width: 180px;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
        }

        .progress {
            height: 8px;
            background: rgba(255,255,255,0.07);
            border-radius: 999px;
        }

        .progress-bar-custom {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--accent-green), #16a34a);
            transition: width 0.6s ease;
        }

        .progress-bar-custom.danger {
            background: linear-gradient(90deg, var(--accent-amber), var(--accent-red));
        }

        .connection-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.82rem;
            font-weight: 500;
        }

        .conn-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent-green);
            animation: pulse-dot 1.5s infinite;
        }

        .conn-dot.offline { background: var(--accent-red); animation: none; }

        @media (max-width: 576px) {
            .status-bar {
                flex-direction: column;
                align-items: stretch !important;
                gap: 1rem !important;
                padding: 1.25rem !important;
            }

            .connection-status {
                justify-content: center;
                border-top: 1px solid var(--border);
                padding-top: 0.75rem;
                margin-top: 0.25rem;
            }
        }

        /* ======= FOOTER ======= */
        .footer {
            text-align: center;
            padding: 2rem 0;
            border-top: 1px solid var(--border);
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-top: 2rem;
        }

        /* ======= SKELETON LOADING ======= */
        .skeleton {
            background: linear-gradient(90deg, var(--bg-card2) 25%, rgba(255,255,255,0.04) 50%, var(--bg-card2) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 8px;
        }

        @keyframes shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* ======= TOAST NOTIF ======= */
        .toast-container {
            position: fixed;
            bottom: 1.5rem;
            right: 1.5rem;
            z-index: 9999;
        }

        .toast-msg {
            background: var(--bg-card2);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 0.75rem 1.2rem;
            font-size: 0.82rem;
            color: var(--text-primary);
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            animation: slide-in 0.3s ease;
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        }

        @keyframes slide-in {
            from { transform: translateX(100%); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>

<div class="wrapper">

    <!-- ===== HERO ===== -->
    <div class="hero">
        <div class="hero-badge">
            <span class="live-dot"></span>
            Live Monitoring IoT
        </div>

        <h1>Sistem Informasi Parkir</h1>

        <p>Monitoring ketersediaan slot parkir secara realtime dengan sensor ESP32</p>

        <div class="clock-wrap">
            <div class="clock-chip">
                <i class="bi bi-calendar3"></i>
                <span id="tanggal">—</span>
            </div>
            <div class="clock-chip">
                <i class="bi bi-clock"></i>
                <span id="jam-besar">—</span>
            </div>
        </div>
    </div>

    <!-- ===== STAT CARDS ===== -->
    <div class="stats-row">

        <div class="stat-card blue">
            <div class="stat-icon"><i class="bi bi-grid-1x2-fill"></i></div>
            <div>
                <div class="stat-label">Total Slot</div>
                <div class="stat-value" id="stat-total"><?= $total_slot ?></div>
            </div>
        </div>

        <div class="stat-card green">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div>
                <div class="stat-label">Slot Tersedia</div>
                <div class="stat-value" id="stat-kosong"><?= $slot_kosong ?></div>
            </div>
        </div>

        <div class="stat-card red">
            <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
            <div>
                <div class="stat-label">Slot Terisi</div>
                <div class="stat-value" id="stat-terisi"><?= $slot_terisi ?></div>
            </div>
        </div>

    </div>

    <!-- ===== PROGRESS BAR STATUS ===== -->
    <div class="status-bar">
        <div class="progress-wrap">
            <div class="progress-label">
                <span>Kapasitas Terpakai</span>
                <span id="pct-label"><?= $total_slot > 0 ? round(($slot_terisi/$total_slot)*100) : 0 ?>%</span>
            </div>
            <div class="progress">
                <div class="progress-bar-custom <?= ($slot_terisi >= $total_slot) ? 'danger' : '' ?>"
                     id="progress-bar"
                     style="width: <?= $total_slot > 0 ? round(($slot_terisi/$total_slot)*100) : 0 ?>%">
                </div>
            </div>
        </div>
        <div class="connection-status">
            <div class="conn-dot" id="conn-dot"></div>
            <span id="conn-label">Terhubung</span>
            &nbsp;·&nbsp;
            <span id="last-update-time">—</span>
        </div>
    </div>

    <!-- ===== SLOT GRID ===== -->
    <div class="section-head">
        <div>
            <div class="section-title"><i class="bi bi-grid-fill"></i> Ketersediaan Slot Parkir</div>
            <div class="section-sub">Hijau = Kosong &nbsp;•&nbsp; Merah = Terisi</div>
        </div>
    </div>

    <div class="slot-grid" id="slot-grid">

        <?php foreach ($slot as $s): ?>
        <?php $kosong = ($s->status == 'kosong'); ?>

        <div class="slot-card <?= $s->status ?>" id="slot-<?= strtolower($s->kode_slot) ?>">
            <div class="slot-icon-wrap">
                <?= $kosong ? '🟢' : '🔴' ?>
            </div>
            <div class="slot-kode"><?= $s->kode_slot ?></div>
            <div class="slot-badge">
                <i class="bi bi-<?= $kosong ? 'check-circle-fill' : 'x-circle-fill' ?>"></i>
                <?= $kosong ? 'Kosong' : 'Terisi' ?>
            </div>
        </div>

        <?php endforeach; ?>

    </div>

</div><!-- /wrapper -->

<!-- ===== FOOTER ===== -->
<footer class="footer">
    <p>© 2025 Sistem Informasi Parkir &nbsp;•&nbsp; Powered by ESP32 IoT &nbsp;•&nbsp; Developed by Zona Parkir</p>
</footer>

<!-- Toast Container -->
<div class="toast-container" id="toast-container"></div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// =================================================================
// BASE URL — diambil dari CI3
// =================================================================
const BASE_URL = '<?= base_url() ?>';

// =================================================================
// REALTIME CLOCK
// =================================================================
function updateClock() {
    const now = new Date();
    const hari   = ["Minggu","Senin","Selasa","Rabu","Kamis","Jumat","Sabtu"];
    const bulan  = ["Januari","Februari","Maret","April","Mei","Juni",
                    "Juli","Agustus","September","Oktober","November","Desember"];

    document.getElementById('tanggal').textContent =
        `${hari[now.getDay()]}, ${now.getDate()} ${bulan[now.getMonth()]} ${now.getFullYear()}`;

    const jam   = String(now.getHours()).padStart(2,'0');
    const menit = String(now.getMinutes()).padStart(2,'0');
    const detik = String(now.getSeconds()).padStart(2,'0');
    document.getElementById('jam-besar').textContent = `${jam}:${menit}:${detik}`;
}
setInterval(updateClock, 1000);
updateClock();

// =================================================================
// TOAST NOTIFICATION
// =================================================================
function showToast(msg, icon = 'ℹ️') {
    const c  = document.getElementById('toast-container');
    const el = document.createElement('div');
    el.className = 'toast-msg';
    el.innerHTML = `<span>${icon}</span> ${msg}`;
    c.appendChild(el);
    setTimeout(() => el.remove(), 3500);
}

// =================================================================
// POLLING REALTIME — ambil data slot setiap 2 detik via AJAX
// =================================================================
let prevSlotState = {};
let isOnline = true;

function fetchSlotData() {
    fetch(BASE_URL + 'api/slot_json')
        .then(res => {
            if (!res.ok) throw new Error('Server error');
            return res.json();
        })
        .then(data => {
            if (!data.success) return;

            setOnline(true);

            // Update stat numbers dengan animasi
            animateNumber('stat-total',  parseInt(document.getElementById('stat-total').textContent),  data.total_slot);
            animateNumber('stat-kosong', parseInt(document.getElementById('stat-kosong').textContent), data.kosong);
            animateNumber('stat-terisi', parseInt(document.getElementById('stat-terisi').textContent), data.terisi);

            // Update progress bar
            const pct = data.total_slot > 0 ? Math.round((data.terisi / data.total_slot) * 100) : 0;
            const bar = document.getElementById('progress-bar');
            bar.style.width = pct + '%';
            bar.className   = 'progress-bar-custom' + (pct >= 100 ? ' danger' : '');
            document.getElementById('pct-label').textContent = pct + '%';

            // Update last-update time
            document.getElementById('last-update-time').textContent = 'Update: ' + data.updated_at;

            // Update setiap slot card
            data.slot.forEach(s => {
                const id   = 'slot-' + s.kode.toLowerCase();
                const card = document.getElementById(id);
                if (!card) return;

                const wasKosong = prevSlotState[s.kode];
                const nowKosong = (s.status === 'kosong');

                // Deteksi perubahan → tampilkan toast
                if (wasKosong !== undefined && wasKosong !== nowKosong) {
                    if (nowKosong) {
                        showToast(`Slot <b>${s.kode}</b> sekarang <b>kosong</b>`, '🟢');
                    } else {
                        showToast(`Slot <b>${s.kode}</b> sekarang <b>terisi</b>`, '🔴');
                    }
                }
                prevSlotState[s.kode] = nowKosong;

                // Update class & konten card
                card.className = 'slot-card ' + s.status;
                card.querySelector('.slot-icon-wrap').textContent = nowKosong ? '🟢' : '🔴';
                card.querySelector('.slot-badge').innerHTML =
                    `<i class="bi bi-${nowKosong ? 'check' : 'x'}-circle-fill"></i> ${nowKosong ? 'Kosong' : 'Terisi'}`;
            });
        })
        .catch(() => {
            setOnline(false);
        });
}

function setOnline(online) {
    const dot   = document.getElementById('conn-dot');
    const label = document.getElementById('conn-label');
    if (online) {
        dot.classList.remove('offline');
        label.textContent = 'Terhubung';
        isOnline = true;
    } else {
        dot.classList.add('offline');
        label.textContent = 'Offline';
        if (isOnline) showToast('Koneksi ke server terputus!', '⚠️');
        isOnline = false;
    }
}

// Animasi angka
function animateNumber(id, from, to) {
    const el = document.getElementById(id);
    if (from === to) return;
    el.textContent = to;
    el.style.transform = 'scale(1.2)';
    el.style.transition = 'transform 0.2s ease';
    setTimeout(() => { el.style.transform = 'scale(1)'; }, 200);
}

// Init prevSlotState dari data PHP awal
<?php foreach ($slot as $s): ?>
prevSlotState['<?= $s->kode_slot ?>'] = <?= ($s->status === 'kosong') ? 'true' : 'false' ?>;
<?php endforeach; ?>

// Mulai polling setiap 2 detik
setInterval(fetchSlotData, 2000);
</script>

</body>
</html>