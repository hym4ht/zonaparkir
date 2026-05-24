<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {

    public function index()
    {
        // ambil semua data slot
        $slot = $this->db->get('slot')->result();

        // hitung total slot
        $total_slot = $this->db->count_all('slot');

        // hitung slot kosong
        $slot_kosong = $this->db->where('status', 'kosong')->count_all_results('slot');

        // hitung slot terisi
        $slot_terisi = $this->db->where('status', 'terisi')->count_all_results('slot');

        $data = array(
            'total_slot' => $total_slot,
            'slot_kosong' => $slot_kosong,
            'slot_terisi' => $slot_terisi,
            'slot' => $slot
        );

        $this->load->view('user_dashboard', $data);
    }
}