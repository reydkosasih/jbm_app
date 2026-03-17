<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Vehicle_model extends CI_Model
{

    protected $table = 'vehicles';

    public function get_by_user($user_id)
    {
        return $this->db->where('user_id', $user_id)
            ->where('is_deleted', 0)
            ->order_by('created_at', 'DESC')
            ->get($this->table)->result();
    }

    public function get_by_id($id, $user_id = null)
    {
        $this->db->where('id', $id)->where('is_deleted', 0);
        if ($user_id) $this->db->where('user_id', $user_id);
        return $this->db->get($this->table)->row();
    }

    public function create($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function update($id, $user_id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->where('user_id', $user_id)
            ->update($this->table, $data);
    }

    public function delete($id, $user_id)
    {
        // Soft-delete: check no active bookings
        $active = $this->db->where('vehicle_id', $id)
            ->where_in('status', ['pending', 'confirmed', 'in_progress'])
            ->count_all_results('bookings');
        if ($active > 0) return false;
        return $this->db->where('id', $id)->where('user_id', $user_id)
            ->update($this->table, ['is_deleted' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    public function get_all($filters = [])
    {
        $this->db->select('v.*, u.name AS owner_name, u.email AS owner_email')
            ->from($this->table . ' v')
            ->join('users u', 'u.id_user = v.user_id')
            ->where('v.is_deleted', 0);
        if (!empty($filters['user_id'])) $this->db->where('v.user_id', $filters['user_id']);
        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('v.plate_number', $filters['search'])
                ->or_like('v.brand', $filters['search'])
                ->or_like('v.model', $filters['search'])
                ->or_like('u.name', $filters['search'])
                ->group_end();
        }
        return $this->db->order_by('v.created_at', 'DESC')->get()->result();
    }
}
