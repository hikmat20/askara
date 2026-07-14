<?php defined('BASEPATH') or exit('No direct script access allowed');

class Signature_model extends CI_Model
{

    public function getByToken($token)
    {
        $this->db->select('signature_documents.*, view_users.full_name as sign_by_name, positions.name as position_name');
        $this->db->from('signature_documents');
        $this->db->join('view_users', 'view_users.id_user = signature_documents.sign_by', 'left');
        $this->db->join('positions', 'positions.id = signature_documents.position_id', 'left');
        $this->db->where('signature_documents.token', $token);
        return $this->db->get()->row();
    }

    public function getDocument($id, $type)
    {
        return $this->db->select('*')->from('view_procedures')->where('id', $id)->get()->row();
    }
}
