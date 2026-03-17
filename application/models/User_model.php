<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    protected $table = 'users';

    public function get_by_email(string $email): ?array
    {
        $row = $this->db->where('email', $email)->where('is_active', 1)->get($this->table)->row_array();
        return $row ?: null;
    }

    public function get_by_id(int $id): ?array
    {
        $row = $this->db->where('id_user', $id)->get($this->table)->row_array();
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        return (int) $this->db->insert_id();
    }

    public function update(int $id, array $data): bool
    {
        return $this->db->where('id_user', $id)->update($this->table, $data);
    }

    public function get_all(array $filters = []): array
    {
        if (!empty($filters['role'])) {
            $this->db->where('role', $filters['role']);
        }
        if (!empty($filters['search'])) {
            $search = $this->db->escape_like_str($filters['search']);
            $this->db->group_start()
                ->like('name', $search)
                ->or_like('email', $search)
                ->group_end();
        }
        return $this->db->order_by('created_at', 'DESC')->get($this->table)->result_array();
    }

    public function set_reset_token(int $id, string $token, string $expiry): bool
    {
        return $this->db->where('id_user', $id)->update($this->table, [
            'reset_token'  => $token,
            'reset_expiry' => $expiry,
        ]);
    }

    public function get_by_reset_token(string $token): ?array
    {
        $row = $this->db
            ->where('reset_token', $token)
            ->where('reset_expiry >=', date('Y-m-d H:i:s'))
            ->where('is_active', 1)
            ->get($this->table)
            ->row_array();
        return $row ?: null;
    }

    public function update_password(int $id, string $hashed_password): bool
    {
        return $this->db->where('id_user', $id)->update($this->table, [
            'password'    => $hashed_password,
            'reset_token' => null,
            'reset_expiry' => null,
        ]);
    }

    public function clear_reset_token(int $id): bool
    {
        return $this->db->where('id_user', $id)->update($this->table, [
            'reset_token'  => null,
            'reset_expiry' => null,
        ]);
    }

    public function email_exists(string $email): bool
    {
        return $this->db->where('email', $email)->count_all_results($this->table) > 0;
    }

    public function count_by_role(string $role): int
    {
        return (int) $this->db->where('role', $role)->count_all_results($this->table);
    }
}
