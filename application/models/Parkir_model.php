<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Parkir_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // =============================================
    // SLOT
    // =============================================

    /**
     * Ambil semua data slot
     */
    public function get_all_slot()
    {
        return $this->db->order_by('kode_slot', 'ASC')->get('slot')->result();
    }

    /**
     * Update status slot berdasarkan kode_slot
     * @param string $kode_slot  contoh: 'A', 'B', 'C', 'D'
     * @param string $status     'kosong' atau 'terisi'
     */
    public function update_slot($kode_slot, $status)
    {
        $this->db->where('kode_slot', $kode_slot);
        $this->db->update('slot', [
            'status'     => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Hitung slot kosong
     */
    public function count_kosong()
    {
        return $this->db->where('status', 'kosong')->count_all_results('slot');
    }

    /**
     * Hitung slot terisi
     */
    public function count_terisi()
    {
        return $this->db->where('status', 'terisi')->count_all_results('slot');
    }

    /**
     * Total slot
     */
    public function count_total()
    {
        return $this->db->count_all('slot');
    }

    // =============================================
    // LOG AKSES (masuk / keluar)
    // =============================================

    /**
     * Simpan log akses kendaraan
     * @param string $jenis  'masuk' atau 'keluar'
     */
    public function insert_log($jenis)
    {
        $this->db->insert('log_akses', [
            'jenis' => $jenis,
            'waktu' => date('Y-m-d H:i:s')
        ]);
        return $this->db->insert_id();
    }

    /**
     * Ambil log akses terbaru
     * @param int $limit
     */
    public function get_log($limit = 20)
    {
        return $this->db
            ->order_by('waktu', 'DESC')
            ->limit($limit)
            ->get('log_akses')
            ->result();
    }

    /**
     * Hitung total kendaraan masuk hari ini
     */
    public function count_masuk_hari_ini()
    {
        $this->db->where('jenis', 'masuk');
        $this->db->where('DATE(waktu)', date('Y-m-d'));
        return $this->db->count_all_results('log_akses');
    }

    /**
     * Hitung total kendaraan keluar hari ini
     */
    public function count_keluar_hari_ini()
    {
        $this->db->where('jenis', 'keluar');
        $this->db->where('DATE(waktu)', date('Y-m-d'));
        return $this->db->count_all_results('log_akses');
    }

    // =============================================
    // PARKIR (tabel lama, tetap dipertahankan)
    // =============================================

    public function get_all_parkir()
    {
        return $this->db->order_by('id_parkir', 'DESC')->get('parkir')->result();
    }

    public function count_parkir()
    {
        return $this->db->count_all('parkir');
    }
}
