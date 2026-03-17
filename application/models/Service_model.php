<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Service_model extends CI_Model
{
    protected $table = 'services';

    public function get_active(): array
    {
        return $this->db->where('is_active', 1)->get($this->table)->result_array();
    }

    public function get_by_id(int $id): ?array
    {
        $row = $this->db->where('id_service', $id)->get($this->table)->row_array();
        return $row ?: null;
    }

    public function get_all(): array
    {
        return $this->db->get($this->table)->result_array();
    }

    public function create(array $data): int
    {
        $this->db->insert($this->table, $data);
        return (int) $this->db->insert_id();
    }

    public function update(int $id, array $data): bool
    {
        return $this->db->where('id_service', $id)->update($this->table, $data);
    }

    public function delete(int $id): bool
    {
        return $this->db->where('id_service', $id)->update($this->table, ['is_active' => 0]);
    }
}
