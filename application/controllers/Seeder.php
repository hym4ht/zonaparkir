<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Seeder extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        // Cek apakah diakses melalui CLI (Command Line)
        if (!is_cli()) {
            echo "Seeder hanya dapat dijalankan melalui CLI/Terminal.";
            exit;
        }
        $this->load->database();
    }

    public function user()
    {
        // Pastikan tabel admin (sebagai tabel user) tersedia
        if (!$this->db->table_exists('admin')) {
            echo "Tabel admin tidak ditemukan!\n";
            return;
        }

        $data = array(
            array(
                'username'     => 'admin2',
                'password'     => password_hash('admin123', PASSWORD_BCRYPT),
                'nama_lengkap' => 'Administrator Dua',
                'created_at'   => date('Y-m-d H:i:s')
            ),
            array(
                'username'     => 'petugas1',
                'password'     => password_hash('petugas123', PASSWORD_BCRYPT),
                'nama_lengkap' => 'Petugas Parkir Pagi',
                'created_at'   => date('Y-m-d H:i:s')
            ),
            array(
                'username'     => 'petugas2',
                'password'     => password_hash('petugas123', PASSWORD_BCRYPT),
                'nama_lengkap' => 'Petugas Parkir Malam',
                'created_at'   => date('Y-m-d H:i:s')
            )
        );

        foreach ($data as $user) {
            // Cek apakah username sudah ada
            $this->db->where('username', $user['username']);
            $query = $this->db->get('admin');

            if ($query->num_rows() == 0) {
                $this->db->insert('admin', $user);
                echo "User '" . $user['username'] . "' berhasil ditambahkan.\n";
            } else {
                echo "User '" . $user['username'] . "' sudah ada.\n";
            }
        }

        echo "Seeder User Selesai.\n";
    }
}
