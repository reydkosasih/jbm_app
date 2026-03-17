<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Errors — custom error page controller for 404/403/500
 */
class Errors extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Load settings for layout
        try {
            $rows = $this->db->get('settings')->result_array();
            $settings = [];
            foreach ($rows as $r) {
                $settings[$r['key']] = $r['value'];
            }
        } catch (Exception $e) {
            $settings = [];
        }
        $this->load->vars(['settings' => $settings]);
    }

    public function error_404()
    {
        $this->output->set_status_header(404);
        $data['page_title'] = 'Halaman Tidak Ditemukan';
        $this->load->view('errors/custom_404', $data);
    }

    public function error_403()
    {
        $this->output->set_status_header(403);
        $data['page_title'] = 'Akses Ditolak';
        $this->load->view('errors/custom_403', $data);
    }

    public function error_500()
    {
        $this->output->set_status_header(500);
        $data['page_title'] = 'Kesalahan Server';
        $this->load->view('errors/custom_500', $data);
    }
}
