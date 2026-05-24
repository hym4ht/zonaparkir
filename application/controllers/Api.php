<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * API Controller — Sistem Informasi Parkir
 * Dikonsumsi oleh ESP32 via HTTP POST + AJAX dashboard
 */
class Api extends CI_Controller {

    private $api_key = 'parkir_secret_2025';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        // Pastikan response selalu JSON
        header('Content-Type: application/json');
        // Pastikan timezone WIB (pengaman jika index.php tidak terbaca)
        date_default_timezone_set('Asia/Jakarta');
    }

    // =========================================================
    // CEK API KEY
    // =========================================================
    private function cek_api_key()
    {
        // Coba ambil dari berbagai cara (ESP32 kadang beda format header)
        $key = '';
        if (isset($_SERVER['HTTP_X_API_KEY'])) {
            $key = $_SERVER['HTTP_X_API_KEY'];
        } elseif (isset($_SERVER['HTTP_X_API_Key'])) {
            $key = $_SERVER['HTTP_X_API_Key'];
        } else {
            $key = $this->input->get_request_header('X-API-Key', TRUE);
        }

        if ($key !== $this->api_key) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
    }

    // =========================================================
    // POST /api/update_slot
    // ESP32 kirim: {"slot1":1,"slot2":0,"slot3":1,"slot4":0}
    // =========================================================
    public function update_slot()
    {
        $this->cek_api_key();

        // Baca body JSON
        $raw  = file_get_contents('php://input');
        $data = json_decode($raw, true);

        // Jika JSON gagal, coba dari POST biasa
        if (empty($data)) {
            $data = [
                'slot1' => $this->input->post('slot1'),
                'slot2' => $this->input->post('slot2'),
                'slot3' => $this->input->post('slot3'),
                'slot4' => $this->input->post('slot4'),
            ];
        }

        // Mapping: slot1→A, slot2→B, slot3→C, slot4→D
        $map = ['slot1' => 'A', 'slot2' => 'B', 'slot3' => 'C', 'slot4' => 'D'];

        foreach ($map as $key => $kode) {
            $status = (!empty($data[$key]) && $data[$key] == 1) ? 'terisi' : 'kosong';

            // Update langsung tanpa model — lebih aman saat debug
            $this->db->where('kode_slot', $kode);
            $exists = $this->db->count_all_results('slot');

            if ($exists > 0) {
                // Update jika baris sudah ada
                $this->db->where('kode_slot', $kode);
                $this->db->update('slot', ['status' => $status]);
            } else {
                // Insert jika baris belum ada (pertama kali)
                $this->db->insert('slot', ['kode_slot' => $kode, 'status' => $status]);
            }
        }

        log_message('info', '[API] update_slot: ' . $raw);

        echo json_encode([
            'success'   => true,
            'message'   => 'Slot diupdate',
            'timestamp' => date('Y-m-d H:i:s') . ' WIB',
            'received'  => $data
        ]);
    }

    // =========================================================
    // POST /api/gate_log
    // ESP32 kirim: {"jenis":"masuk"} atau {"jenis":"keluar"}
    // =========================================================
    public function gate_log()
    {
        $this->cek_api_key();

        $raw  = file_get_contents('php://input');
        $data = json_decode($raw, true);

        $jenis = isset($data['jenis']) ? $data['jenis'] : $this->input->post('jenis');

        if (!in_array($jenis, ['masuk', 'keluar'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Jenis tidak valid']);
            return;
        }

        // Cek dulu apakah tabel log_akses ada
        if ($this->db->table_exists('log_akses')) {
            $this->db->insert('log_akses', [
                'jenis' => $jenis,
                'waktu' => date('Y-m-d H:i:s')
            ]);
            $id = $this->db->insert_id();
        } else {
            // Tabel belum ada — buat otomatis
            $this->db->query("
                CREATE TABLE IF NOT EXISTS log_akses (
                    id    INT AUTO_INCREMENT PRIMARY KEY,
                    jenis ENUM('masuk','keluar') NOT NULL,
                    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8
            ");
            $this->db->insert('log_akses', [
                'jenis' => $jenis,
                'waktu' => date('Y-m-d H:i:s')
            ]);
            $id = $this->db->insert_id();
        }

        log_message('info', '[API] gate_log: ' . $jenis);

        echo json_encode([
            'success' => true,
            'message' => 'Log disimpan',
            'id'      => $id,
            'jenis'   => $jenis,
            'waktu'   => date('Y-m-d H:i:s') . ' WIB'
        ]);
    }

    // =========================================================
    // GET /api/slot_json
    // AJAX polling dashboard — tidak butuh API key
    // =========================================================
    public function slot_json()
    {
        $slot = $this->db->order_by('kode_slot', 'ASC')->get('slot')->result();

        $total  = count($slot);
        $terisi = 0;
        $arr    = [];

        foreach ($slot as $s) {
            if ($s->status === 'terisi') $terisi++;
            $arr[] = ['kode' => $s->kode_slot, 'status' => $s->status];
        }

        echo json_encode([
            'success'    => true,
            'total_slot' => $total,
            'terisi'     => $terisi,
            'kosong'     => $total - $terisi,
            'slot'       => $arr,
            'updated_at' => date('H:i:s') . ' WIB'
        ]);
    }

    // =========================================================
    // GET /api/log_json
    // AJAX polling log akses di admin dashboard
    // =========================================================
    public function log_json()
    {
        $arr = [];
        $masuk_hari  = 0;
        $keluar_hari = 0;

        if ($this->db->table_exists('log_akses')) {
            $log = $this->db->order_by('waktu', 'DESC')->limit(15)->get('log_akses')->result();
            foreach ($log as $l) {
                $arr[] = ['id' => $l->id, 'jenis' => $l->jenis, 'waktu' => $l->waktu];
            }

            $this->db->where('jenis', 'masuk')->where('DATE(waktu)', date('Y-m-d'));
            $masuk_hari = $this->db->count_all_results('log_akses');

            $this->db->where('jenis', 'keluar')->where('DATE(waktu)', date('Y-m-d'));
            $keluar_hari = $this->db->count_all_results('log_akses');
        }

        echo json_encode([
            'success'     => true,
            'log'         => $arr,
            'masuk_hari'  => (int)$masuk_hari,
            'keluar_hari' => (int)$keluar_hari,
            'updated_at'  => date('H:i:s') . ' WIB'
        ]);
    }

    // =========================================================
    // GET /api/status
    // Cek koneksi + status sistem (butuh API key)
    // =========================================================
    public function status()
    {
        $this->cek_api_key();

        $slot   = $this->db->order_by('kode_slot', 'ASC')->get('slot')->result();
        $arr    = [];
        $terisi = 0;

        foreach ($slot as $s) {
            if ($s->status === 'terisi') $terisi++;
            $arr[] = ['kode' => $s->kode_slot, 'status' => $s->status];
        }

        echo json_encode([
            'success'     => true,
            'server_time' => date('Y-m-d H:i:s'),
            'total_slot'  => count($slot),
            'terisi'      => $terisi,
            'kosong'      => count($slot) - $terisi,
            'slot'        => $arr
        ]);
    }

    // =========================================================
    // GET /api/init_db
    // Buat tabel otomatis jika belum ada
    // Akses sekali dari browser: /infoparkir/api/init_db
    // =========================================================
    public function init_db()
    {
        $hasil = [];

        // Buat tabel slot jika belum ada
        $this->db->query("
            CREATE TABLE IF NOT EXISTS slot (
                id_slot   INT AUTO_INCREMENT PRIMARY KEY,
                kode_slot VARCHAR(5) NOT NULL UNIQUE,
                status    ENUM('kosong','terisi') DEFAULT 'kosong'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");
        $hasil[] = 'Tabel slot: OK';

        // Isi data awal A,B,C,D jika belum ada
        foreach (['A','B','C','D'] as $kode) {
            $this->db->where('kode_slot', $kode);
            if ($this->db->count_all_results('slot') == 0) {
                $this->db->insert('slot', ['kode_slot' => $kode, 'status' => 'kosong']);
                $hasil[] = "Insert slot $kode: OK";
            } else {
                $hasil[] = "Slot $kode: sudah ada";
            }
        }

        // Buat tabel log_akses jika belum ada
        $this->db->query("
            CREATE TABLE IF NOT EXISTS log_akses (
                id    INT AUTO_INCREMENT PRIMARY KEY,
                jenis ENUM('masuk','keluar') NOT NULL,
                waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");
        $hasil[] = 'Tabel log_akses: OK';

        // Buat tabel admin jika belum ada
        $this->db->query("
            CREATE TABLE IF NOT EXISTS admin (
                id_admin     INT AUTO_INCREMENT PRIMARY KEY,
                username     VARCHAR(50) NOT NULL UNIQUE,
                password     VARCHAR(255) NOT NULL,
                nama_lengkap VARCHAR(100),
                created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");
        $hasil[] = 'Tabel admin: OK';

        // Isi default admin jika kosong
        $this->db->where('username', 'admin');
        if ($this->db->count_all_results('admin') == 0) {
            $this->db->insert('admin', [
                'username'     => 'admin',
                'password'     => password_hash('admin123', PASSWORD_BCRYPT),
                'nama_lengkap' => 'Administrator InfoParkir'
            ]);
            $hasil[] = 'Insert default admin: OK';
        } else {
            $hasil[] = 'Admin default: sudah ada';
        }

        echo json_encode([
            'success' => true,
            'message' => 'Database siap!',
            'detail'  => $hasil
        ]);
    }
}
