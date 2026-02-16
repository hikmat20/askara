<?php defined('BASEPATH') or exit('No direct script access allowed');

class Signature_model extends CI_Model
{

    public function getByToken($token)
    {
        return $this->db->select('*')->from('view_signature_documents')->where('token', $token)->get()->row();
    }

    public function getDocument($id, $type)
    {
        return $this->db->select('*')->from('view_procedures')->where('id', $id)->get()->row();
    }
}
