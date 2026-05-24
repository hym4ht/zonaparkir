<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('session');
        $this->load->helper(['url', 'form']);
    }

    // -------------------------------------------------------
    // GET  /login  — tampilkan form login
    // -------------------------------------------------------
    public function index()
    {
        // Sudah login? langsung redirect ke admin
        if ($this->session->userdata('admin_logged_in')) {
            redirect('admin');
        }

        $data['error']           = $this->session->flashdata('login_error');
        $data['logout_success']  = $this->session->flashdata('logout_success');

        $this->load->view('login', $data);
    }

    // -------------------------------------------------------
    // POST /login/process — proses form login
    // -------------------------------------------------------
    public function process()
    {
        $username = $this->input->post('username', TRUE);
        $password = $this->input->post('password', TRUE);

        // Cari admin di database
        $admin = $this->db->get_where('admin', ['username' => $username])->row();

        if ($admin && password_verify($password, $admin->password)) {
            // Set session
            $this->session->set_userdata([
                'admin_logged_in' => TRUE,
                'admin_username'  => $admin->username,
                'admin_nama'      => $admin->nama_lengkap,
                'login_time'      => date('Y-m-d H:i:s'),
            ]);
            redirect('admin');
        } else {
            $this->session->set_flashdata('login_error', 'Username atau password salah. Silakan coba lagi.');
            redirect('login');
        }
    }

    // -------------------------------------------------------
    // GET /logout — hapus session & redirect ke login
    // -------------------------------------------------------
    public function logout()
    {
        $this->session->unset_userdata('admin_logged_in');
        $this->session->unset_userdata('admin_username');
        $this->session->unset_userdata('login_time');
        $this->session->set_flashdata('logout_success', 'Anda berhasil logout.');
        redirect('login');
    }
}
