<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('url');

        // =============================================
        // SESSION GUARD — wajib login untuk akses admin
        // =============================================
        if (!$this->session->userdata('admin_logged_in')) {
            redirect('login');
        }
    }

    public function index()
    {
        // =========================
        // TOTAL SLOT
        // =========================
        $total_slot = $this->db->count_all('slot');

        // =========================
        // SLOT KOSONG
        // =========================
        $slot_kosong = $this->db
            ->where('status', 'kosong')
            ->count_all_results('slot');

        // =========================
        // SLOT TERISI
        // =========================
        $slot_terisi = $this->db
            ->where('status', 'terisi')
            ->count_all_results('slot');

        // =========================
        // JUMLAH KENDARAAN
        // =========================
        $jumlah_kendaraan = $this->db->count_all('parkir');

        // =========================
        // DATA SLOT
        // =========================
        $slot = $this->db
            ->order_by('kode_slot', 'ASC')
            ->get('slot')
            ->result();

        // =========================
        // DATA PARKIR
        // SUDAH PAKAI kode_slot
        // TANPA JOIN
        // =========================
        $parkir = $this->db
            ->order_by('id_parkir', 'DESC')
            ->get('parkir')
            ->result();

        // =========================
        // KIRIM DATA KE VIEW
        // =========================
        $data = array(
            'total_slot'        => $total_slot,
            'slot_kosong'       => $slot_kosong,
            'slot_terisi'       => $slot_terisi,
            'jumlah_kendaraan'  => $jumlah_kendaraan,
            'slot'              => $slot,
            'parkir'            => $parkir
        );

        $this->load->view('admin_dashboard', $data);
    }

    // =============================================
    // Halaman Kelola Admin
    // =============================================
    public function manage_admin()
    {
        $data['admins'] = $this->db
            ->order_by('id_admin', 'ASC')
            ->get('admin')
            ->result();

        $this->load->view('manage_admin', $data);
    }

    // =============================================
    // Tambah Admin Baru
    // =============================================
    public function add_admin()
    {
        $username = trim($this->input->post('username', TRUE));
        $password = $this->input->post('password');
        $nama     = trim($this->input->post('nama_lengkap', TRUE));

        // Validasi
        if (empty($username) || empty($password)) {
            $this->session->set_flashdata('error', 'Username dan password wajib diisi.');
            redirect('admin/manage_admin');
        }

        // Cek duplikat username
        $exists = $this->db->get_where('admin', ['username' => $username])->row();
        if ($exists) {
            $this->session->set_flashdata('error', 'Username "' . $username . '" sudah digunakan.');
            redirect('admin/manage_admin');
        }

        $this->db->insert('admin', [
            'username'     => $username,
            'password'     => password_hash($password, PASSWORD_BCRYPT),
            'nama_lengkap' => $nama ?: null,
        ]);

        $this->session->set_flashdata('success', 'Admin "' . $username . '" berhasil ditambahkan!');
        redirect('admin/manage_admin');
    }

    // =============================================
    // Edit Admin
    // =============================================
    public function edit_admin()
    {
        $id       = (int) $this->input->post('id_admin');
        $username = trim($this->input->post('username', TRUE));
        $password = $this->input->post('password');
        $nama     = trim($this->input->post('nama_lengkap', TRUE));

        if (empty($id) || empty($username)) {
            $this->session->set_flashdata('error', 'Data tidak valid.');
            redirect('admin/manage_admin');
        }

        // Cek duplikat username (selain diri sendiri)
        $exists = $this->db
            ->where('username', $username)
            ->where('id_admin !=', $id)
            ->get('admin')->row();

        if ($exists) {
            $this->session->set_flashdata('error', 'Username "' . $username . '" sudah digunakan admin lain.');
            redirect('admin/manage_admin');
        }

        $update = [
            'username'     => $username,
            'nama_lengkap' => $nama ?: null,
        ];

        // Update password hanya jika diisi
        if (!empty($password)) {
            $update['password'] = password_hash($password, PASSWORD_BCRYPT);
        }

        $this->db->where('id_admin', $id)->update('admin', $update);

        $this->session->set_flashdata('success', 'Admin "' . $username . '" berhasil diupdate!');
        redirect('admin/manage_admin');
    }

    // =============================================
    // Hapus Admin
    // =============================================
    public function delete_admin($id = 0)
    {
        $id = (int) $id;

        // Proteksi: tidak boleh hapus admin utama (id=1)
        if ($id <= 1) {
            $this->session->set_flashdata('error', 'Admin utama tidak boleh dihapus.');
            redirect('admin/manage_admin');
        }

        $admin = $this->db->get_where('admin', ['id_admin' => $id])->row();
        if (!$admin) {
            $this->session->set_flashdata('error', 'Admin tidak ditemukan.');
            redirect('admin/manage_admin');
        }

        $this->db->where('id_admin', $id)->delete('admin');

        $this->session->set_flashdata('success', 'Admin "' . $admin->username . '" berhasil dihapus.');
        redirect('admin/manage_admin');
    }
}