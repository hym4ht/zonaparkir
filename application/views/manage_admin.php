<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kelola Admin — Info Parkir</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root {
            --bg:#f0f4f8;--sidebar:#0f172a;--card:#ffffff;
            --blue:#3b82f6;--green:#22c55e;--red:#ef4444;
            --amber:#f59e0b;--indigo:#6366f1;--txt:#1e293b;
            --muted:#64748b;--border:#e2e8f0;
        }
        *{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--txt)}

        .admin-nav{background:var(--sidebar);padding:.85rem 1.5rem;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;box-shadow:0 2px 12px rgba(0,0,0,.3)}
        .nav-brand{font-size:1.1rem;font-weight:700;color:#fff;display:flex;align-items:center;gap:.5rem}
        .nav-brand i{color:var(--blue)}
        .nav-right{display:flex;align-items:center;gap:1rem;font-size:.82rem;color:#94a3b8}
        .nav-link-item{color:#94a3b8;text-decoration:none;font-size:.82rem;font-weight:500;padding:.3rem .65rem;border-radius:8px;transition:all .2s}
        .nav-link-item:hover,.nav-link-item.active{color:#fff;background:rgba(255,255,255,.1)}

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

            .nav-right .nav-link-item, 
            .nav-right span {
                width: 100%;
                justify-content: flex-start !important;
                text-align: left;
            }

            .nav-right .nav-link-item {
                padding: 0.6rem 1rem !important;
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 10px;
                display: flex !important;
                align-items: center;
                gap: 0.5rem;
            }

            .nav-right .nav-link-item:hover {
                background: rgba(255, 255, 255, 0.08) !important;
            }
        }

        .page{padding:1.5rem;max-width:1000px;margin:0 auto}
        .page-title{font-size:1.6rem;font-weight:800;margin-bottom:.25rem}
        .page-sub{font-size:.85rem;color:var(--muted);margin-bottom:1.5rem}

        .panel{background:var(--card);border-radius:16px;box-shadow:0 1px 4px rgba(0,0,0,.06);margin-bottom:1.5rem;overflow:hidden}
        .panel-header{padding:1rem 1.25rem;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:.5rem}
        .panel-title{font-weight:700;font-size:.95rem;display:flex;align-items:center;gap:.5rem}
        .panel-body{padding:1.25rem}

        .log-table{width:100%;border-collapse:collapse;font-size:.85rem}
        .log-table th{background:#f8fafc;color:var(--muted);font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;padding:.6rem 1rem;text-align:left;border-bottom:1px solid var(--border)}
        .log-table td{padding:.65rem 1rem;border-bottom:1px solid var(--border);vertical-align:middle}
        .log-table tr:last-child td{border-bottom:none}
        .log-table tr:hover td{background:#f8fafc}

        .btn-action{border:none;padding:.35rem .65rem;border-radius:8px;font-size:.78rem;font-weight:600;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:.3rem}
        .btn-edit{background:rgba(59,130,246,.1);color:var(--blue);border:1px solid rgba(59,130,246,.2)}
        .btn-edit:hover{background:rgba(59,130,246,.2)}
        .btn-delete{background:rgba(239,68,68,.1);color:var(--red);border:1px solid rgba(239,68,68,.2)}
        .btn-delete:hover{background:rgba(239,68,68,.2)}
        .btn-add{background:linear-gradient(135deg,var(--indigo),var(--blue));color:#fff;border:none;padding:.55rem 1.1rem;border-radius:10px;font-size:.82rem;font-weight:600;cursor:pointer;transition:all .2s;display:inline-flex;align-items:center;gap:.4rem;box-shadow:0 2px 10px rgba(99,102,241,.3)}
        .btn-add:hover{transform:translateY(-1px);box-shadow:0 4px 16px rgba(99,102,241,.4)}

        .badge-role{font-size:.72rem;font-weight:600;padding:.2rem .55rem;border-radius:999px;background:rgba(99,102,241,.1);color:var(--indigo);border:1px solid rgba(99,102,241,.2)}

        /* Modal */
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:999;display:none;align-items:center;justify-content:center;backdrop-filter:blur(4px)}
        .modal-overlay.show{display:flex}
        .modal-box{background:#fff;border-radius:20px;padding:2rem;width:90%;max-width:440px;box-shadow:0 20px 50px rgba(0,0,0,.2);animation:slideUp .3s ease}
        @keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}
        .modal-title{font-size:1.1rem;font-weight:700;margin-bottom:1.25rem;display:flex;align-items:center;gap:.5rem}
        .modal-close{position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.2rem;color:var(--muted);cursor:pointer}
        .form-group{margin-bottom:1rem}
        .form-group label{display:block;font-size:.78rem;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:.4rem}
        .form-group input{width:100%;padding:.7rem .9rem;border:1.5px solid var(--border);border-radius:10px;font-size:.88rem;font-family:'Inter',sans-serif;transition:all .2s;outline:none}
        .form-group input:focus{border-color:var(--indigo);box-shadow:0 0 0 3px rgba(99,102,241,.15)}
        .btn-submit{width:100%;padding:.75rem;background:linear-gradient(135deg,var(--indigo),var(--blue));color:#fff;border:none;border-radius:10px;font-size:.9rem;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;transition:all .2s;margin-top:.5rem}
        .btn-submit:hover{transform:translateY(-1px);box-shadow:0 4px 16px rgba(99,102,241,.3)}

        .alert-msg{padding:.75rem 1rem;border-radius:10px;font-size:.83rem;font-weight:500;margin-bottom:1rem;display:flex;align-items:center;gap:.5rem;animation:alertIn .3s ease}
        @keyframes alertIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
        .alert-success{background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.3);color:#16a34a}
        .alert-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#dc2626}

        .empty-state{text-align:center;padding:3rem 1rem;color:var(--muted)}
        .empty-state i{font-size:2.5rem;margin-bottom:.75rem;opacity:.4}
        .empty-state p{font-size:.88rem}
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
        <a href="<?= site_url('admin') ?>" class="nav-link-item"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="<?= site_url('admin/manage_admin') ?>" class="nav-link-item active"><i class="bi bi-people-fill"></i> Kelola Admin</a>
        <span style="display:flex;align-items:center;gap:.4rem;color:#cbd5e1">
            <i class="bi bi-person-circle" style="color:#6366f1"></i>
            <?= htmlspecialchars($this->session->userdata('admin_username') ?? 'admin') ?>
        </span>
        <a href="<?= site_url('logout') ?>" title="Logout" onclick="return confirm('Yakin ingin logout?')"
           style="display:inline-flex;align-items:center;gap:.35rem;background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3);color:#fca5a5;font-size:.78rem;font-weight:600;padding:.3rem .75rem;border-radius:999px;text-decoration:none;transition:all .2s"
           onmouseover="this.style.background='rgba(239,68,68,.3)'" onmouseout="this.style.background='rgba(239,68,68,.15)'">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</nav>

<div class="page">
    <div class="page-title">Kelola Admin</div>
    <div class="page-sub">Tambah, edit, atau hapus akun admin sistem parkir</div>

    <?php if ($this->session->flashdata('success')): ?>
        <div class="alert-msg alert-success">
            <i class="bi bi-check-circle-fill"></i>
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>
    <?php if ($this->session->flashdata('error')): ?>
        <div class="alert-msg alert-error">
            <i class="bi bi-exclamation-circle-fill"></i>
            <?= $this->session->flashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="panel">
        <div class="panel-header">
            <div class="panel-title">
                <i class="bi bi-people-fill" style="color:var(--indigo)"></i>
                Daftar Admin (<?= count($admins) ?>)
            </div>
            <button class="btn-add" onclick="openModal('add')">
                <i class="bi bi-plus-lg"></i> Tambah Admin
            </button>
        </div>
        <div class="panel-body" style="padding:0">
            <?php if (empty($admins)): ?>
                <div class="empty-state">
                    <i class="bi bi-person-x"></i>
                    <p>Belum ada data admin</p>
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="log-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Dibuat</th>
                            <th style="text-align:center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $no=1; foreach($admins as $a): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <span style="font-weight:600"><?= htmlspecialchars($a->username) ?></span>
                                <span class="badge-role">Admin</span>
                            </td>
                            <td><?= htmlspecialchars($a->nama_lengkap ?: '-') ?></td>
                            <td style="color:var(--muted);font-size:.8rem"><?= $a->created_at ?></td>
                            <td style="text-align:center">
                                <button class="btn-action btn-edit" onclick="openModal('edit', <?= $a->id_admin ?>, '<?= htmlspecialchars($a->username, ENT_QUOTES) ?>', '<?= htmlspecialchars($a->nama_lengkap, ENT_QUOTES) ?>')">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <?php if ($a->id_admin != 1): ?>
                                <a href="<?= site_url('admin/delete_admin/'.$a->id_admin) ?>" class="btn-action btn-delete" onclick="return confirm('Yakin hapus admin \'<?= htmlspecialchars($a->username, ENT_QUOTES) ?>\'?')">
                                    <i class="bi bi-trash3"></i> Hapus
                                </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MODAL ADD/EDIT -->
<div class="modal-overlay" id="adminModal">
    <div class="modal-box" style="position:relative">
        <button class="modal-close" onclick="closeModal()"><i class="bi bi-x-lg"></i></button>
        <div class="modal-title" id="modalTitle">
            <i class="bi bi-person-plus-fill" style="color:var(--indigo)"></i>
            Tambah Admin Baru
        </div>
        <form id="adminForm" method="post">
            <input type="hidden" name="id_admin" id="f_id">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" id="f_username" placeholder="Masukkan username" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" id="f_nama" placeholder="Masukkan nama lengkap">
            </div>
            <div class="form-group">
                <label id="lbl_password">Password</label>
                <input type="password" name="password" id="f_password" placeholder="Masukkan password" autocomplete="new-password">
            </div>
            <button type="submit" class="btn-submit" id="btnSubmit">
                <i class="bi bi-check-lg"></i> Simpan Admin
            </button>
        </form>
    </div>
</div>

<script>
const BASE = '<?= site_url() ?>';

function openModal(mode, id, username, nama) {
    const modal = document.getElementById('adminModal');
    const form  = document.getElementById('adminForm');
    const title = document.getElementById('modalTitle');
    const pwd   = document.getElementById('f_password');
    const lbl   = document.getElementById('lbl_password');
    const btn   = document.getElementById('btnSubmit');

    if (mode === 'add') {
        title.innerHTML = '<i class="bi bi-person-plus-fill" style="color:var(--indigo)"></i> Tambah Admin Baru';
        form.action = BASE + 'admin/add_admin';
        document.getElementById('f_id').value = '';
        document.getElementById('f_username').value = '';
        document.getElementById('f_nama').value = '';
        pwd.value = '';
        pwd.required = true;
        lbl.textContent = 'Password';
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan Admin';
    } else {
        title.innerHTML = '<i class="bi bi-pencil-square" style="color:var(--blue)"></i> Edit Admin';
        form.action = BASE + 'admin/edit_admin';
        document.getElementById('f_id').value = id;
        document.getElementById('f_username').value = username;
        document.getElementById('f_nama').value = nama;
        pwd.value = '';
        pwd.required = false;
        lbl.textContent = 'Password Baru (kosongkan jika tidak diubah)';
        btn.innerHTML = '<i class="bi bi-check-lg"></i> Update Admin';
    }
    modal.classList.add('show');
}

function closeModal() {
    document.getElementById('adminModal').classList.remove('show');
}

document.getElementById('adminModal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});

// Toggle menu responsif
document.getElementById('nav-toggle').addEventListener('click', function() {
    document.querySelector('.nav-right').classList.toggle('show');
});
</script>

</body>
</html>
