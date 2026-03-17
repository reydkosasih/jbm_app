<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Landing extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Service_model', 'Testimonial_model', 'Gallery_model']);
    }

    public function index()
    {
        $data = [
            'page_title' => 'Beranda',
            'services'   => $this->Service_model->get_active(),
            'testimonials' => $this->Testimonial_model->get_approved(),
            'gallery'    => $this->Gallery_model->get_active(),
            'stats'      => $this->_get_stats(),
        ];
        $this->render('landing/index', $data, 'public');
    }

    public function about()
    {
        $data = ['page_title' => 'Tentang Kami'];
        $this->render('landing/about', $data, 'public');
    }

    public function services()
    {
        $data = [
            'page_title' => 'Layanan Kami',
            'services'   => $this->Service_model->get_active(),
        ];
        $this->render('landing/services', $data, 'public');
    }

    // -------------------------------------------------------------------------

    private function _get_stats(): array
    {
        return [
            'total_customers' => $this->db->where('role', 'customer')->count_all_results('users'),
            'years_experience' => 10,
            'certified_mechanics' => $this->db->where('role', 'mekanik')->count_all_results('users'),
            'star_rating' => 4.9,
        ];
    }
}
