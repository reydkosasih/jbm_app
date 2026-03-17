<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Spare_part_model extends CI_Model
{

    protected $table     = 'spare_parts';
    protected $mutations = 'stock_mutations';

    public function get_all($filters = [])
    {
        $this->db->from($this->table);
        if (!empty($filters['search'])) {
            $this->db->group_start()
                ->like('name', $filters['search'])
                ->or_like('sku', $filters['search'])
                ->group_end();
        }
        if (isset($filters['is_active'])) $this->db->where('is_active', $filters['is_active']);
        return $this->db->order_by('name', 'ASC')->get()->result();
    }

    public function get_active()
    {
        return $this->db->where('is_active', 1)->where('stock >', 0)->order_by('name', 'ASC')->get($this->table)->result();
    }

    public function get_by_id($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }

    public function get_low_stock()
    {
        return $this->db->where('stock <= min_stock')->where('is_active', 1)->order_by('stock', 'ASC')->get($this->table)->result();
    }

    public function create($data)
    {
        $stock = (int)($data['stock'] ?? 0);
        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->insert($this->table, $data);
        $id = $this->db->insert_id();

        if ($stock > 0) {
            $this->_mutate($id, 'in', $stock, 0, $stock, 'purchase', 'Stok awal');
        }
        return $id;
    }

    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->where('id', $id)->update($this->table, $data);
    }

    public function add_stock($id, $qty, $reason = 'purchase', $note = '')
    {
        $part = $this->get_by_id($id);
        if (!$part) return false;
        $new_stock = $part->stock + $qty;
        $this->db->where('id', $id)->update($this->table, ['stock' => $new_stock, 'updated_at' => date('Y-m-d H:i:s')]);
        $this->_mutate($id, 'in', $qty, $part->stock, $new_stock, $reason, $note);
        return $new_stock;
    }

    public function deduct_stock($id, $qty, $booking_id = null, $note = '')
    {
        $part = $this->get_by_id($id);
        if (!$part || $part->stock < $qty) return false;
        $new_stock = $part->stock - $qty;
        $this->db->where('id', $id)->update($this->table, ['stock' => $new_stock, 'updated_at' => date('Y-m-d H:i:s')]);
        $this->_mutate($id, 'out', $qty, $part->stock, $new_stock, 'usage', $note ?: ('Digunakan untuk booking #' . $booking_id));
        return $new_stock;
    }

    public function get_mutations($id, $limit = 50)
    {
        return $this->db->where('spare_part_id', $id)->order_by('created_at', 'DESC')->limit($limit)->get($this->mutations)->result();
    }

    private function _mutate($part_id, $type, $qty, $before, $after, $reason, $note)
    {
        $this->db->insert($this->mutations, [
            'spare_part_id' => $part_id,
            'type'          => $type,
            'quantity'      => $qty,
            'stock_before'  => $before,
            'stock_after'   => $after,
            'reason'        => $reason,
            'note'          => $note,
            'created_by'    => $this->session->userdata('user_id') ?? null,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
