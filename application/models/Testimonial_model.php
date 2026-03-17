<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Testimonial_model extends CI_Model
{
    protected $table = 'testimonials';

    public function get_approved(): array
    {
        return $this->db->where('is_approved', 1)->order_by('created_at', 'DESC')->get($this->table)->result_array();
    }

    public function get_all(): array
    {
        return $this->db->order_by('created_at', 'DESC')->get($this->table)->result_array();
    }

    public function create(array $data): int
    {
        $this->db->insert($this->table, $data);
        return (int) $this->db->insert_id();
    }

    public function approve(int $id): bool
    {
        return $this->db->where('id_testimonial', $id)->update($this->table, ['is_approved' => 1]);
    }

    public function delete(int $id): bool
    {
        return $this->db->where('id_testimonial', $id)->delete($this->table);
    }
}
