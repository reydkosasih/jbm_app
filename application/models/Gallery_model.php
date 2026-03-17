<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gallery_model extends CI_Model
{
    protected $table = 'gallery';

    public function get_active(): array
    {
        return $this->db->where('is_active', 1)->order_by('sort_order', 'ASC')->get($this->table)->result_array();
    }

    public function get_all(): array
    {
        return $this->db->order_by('sort_order', 'ASC')->get($this->table)->result_array();
    }

    public function get_by_id(int $id)
    {
        return $this->db->where('id_gallery', $id)->get($this->table)->row();
    }

    public function create(array $data): int
    {
        $this->db->insert($this->table, $data);
        return (int) $this->db->insert_id();
    }

    public function update(int $id, array $data): bool
    {
        return $this->db->where('id_gallery', $id)->update($this->table, $data);
    }

    public function delete(int $id): bool
    {
        return $this->db->where('id_gallery', $id)->delete($this->table);
    }
}
