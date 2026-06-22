<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Hikmat
 * @copyright Copyright (c) 2024, Hikmat
 *
 */

class Audit_badan_sertifikasi extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->template->set([
            'title' => 'Badan Sertifikasi Audit',
            'icon' => 'fa fa-certificate'
        ]);

        date_default_timezone_set("Asia/Bangkok");
    }

    private function _getId()
    {
        $count    = 1;
        $result   = $this->db->select('MAX(RIGHT(id,3)) as id')->from('audit_badan_sertifikasi')->where(['SUBSTR(id,3,4)' => date('ym')])->get()->row();

        if ($result->id > 0) {
            $count = $result->id + 1;
        }
        return "BS" . date('ym-') . sprintf("%03d", $count);
    }

    private function _getDtltId($id)
    {
        $count    = 1;
        $result   = $this->db->select('MAX(RIGHT(id,3)) as id')->from('audit_auditor_badan_sertifikasi')->where(['badan_sert_id' => $id])->get()->row();

        if ($result->id > 0) {
            $count = $result->id + 1;
        }
        return $count;
    }

    public function index()
    {
        $data       = $this->db->get_where('audit_badan_sertifikasi', ['status' => '1'])->result();
        $auditor    = $this->db->get_where('audit_auditor_badan_sertifikasi', ['status' => '1'])->result();
        $ArrAuditor = [];
        if( $auditor) foreach ($auditor as $k => $v) {
            $ArrAuditor[$v->badan_sert_id][] = $v->name;
        }

        $this->template->set('data', $data);
        $this->template->set('ArrAuditor', $ArrAuditor);
        $this->template->render('index');
    }

    public function add()
    {
        $this->template->render('add');
    }

    public function edit($id)
    {
        $data            = $this->db->get_where('audit_badan_sertifikasi', ['id' => $id, 'status' => '1'])->row();
        $auditors        = $this->db->get_where('audit_auditor_badan_sertifikasi', ['badan_sert_id' => $id, 'status' => '1'])->result();
        $this->template->set([
            'data'       => $data,
            'auditors'   => $auditors,
        ]);
        $this->template->render('edit');
    }

    public function save()
    {
        $data       = $this->input->post();
        $auditor = $data['auditor'];
        unset($data['auditor']);

        $this->db->trans_begin();
        if ($data) {
            if (isset($data['id']) && $data['id']) {
                $data['modified_at'] = date('Y-m-d H:i:s');
                $data['modified_by'] = $this->auth->user_id();
                $this->db->update('audit_badan_sertifikasi', $data, ['id' => $data['id']]);
            } else {
                $data['id']         = $this->_getId();
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['created_by'] = $this->auth->user_id();
                $this->db->insert('audit_badan_sertifikasi', $data);
            }

            if ($auditor) {
                $n = $this->_getDtltId($data['id']);
                foreach ($auditor as $v) {
                    $n++;
                    if (isset($v['id']) && $v['id']) {
                        $v['modified_by'] = $this->auth->user_id();
                        $v['modified_at'] = date('Y-m-d H:i:s');
                        $this->db->update('audit_auditor_badan_sertifikasi', $v, ['id' => $v['id']]);
                    } else {
                        $v['id']            = $data['id'] . '-' . sprintf("%03d",$n);
                        $v['badan_sert_id'] = $data['id'];
                        $v['created_by']    = $this->auth->user_id();
                        $v['created_at']    = date('Y-m-d H:i:s');
                        $this->db->insert('audit_auditor_badan_sertifikasi', $v);
                    }
                }
            }


            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $return        = array(
                    'status'        => 0,
                    'msg'            => 'Data has Failed save. Please Try Again!'
                );
            } else {
                $this->db->trans_commit();
                $return        = array(
                    'status'        => 1,
                    'msg'            => 'Data has successfull saved. Thanks you.'
                );
            }
        } else {
            $this->db->trans_commit();
            $return        = array(
                'status'        => 0,
                'msg'            => 'Data not valid. Please Try Again!'
            );
        }
        echo json_encode($return);
    }

    function delete()
    {
        $id = $this->input->post('id');
        if ($id) {
            $this->db->trans_begin();
            $this->db->update('audit_badan_sertifikasi', ['status' => '0'], ['id' => $id]);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $Return = [
                    'msg'       => "Successfull save data audit company.",
                    'status'    => 0
                ];
            } else {
                $this->db->trans_commit();
                $Return = [
                    'msg'       => "Failed save data audit company, please try again.",
                    'status'    => 1
                ];
            }
        } else {
            $this->db->trans_rollback();
            $Return = [
                'msg'       => "Data not valid",
                'status'    => 0
            ];
        }

        echo json_encode($Return);
    }

    function delete_auditor()
    {
        $id = $this->input->post('id');
        if ($id) {
            $this->db->trans_begin();
            $this->db->update('audit_auditor_badan_sertifikasi', ['status' => '0'], ['id' => $id]);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $Return = [
                    'msg'       => "Successfull save data Auditor.",
                    'status'    => 0
                ];
            } else {
                $this->db->trans_commit();
                $Return = [
                    'msg'       => "Failed save data Auditor, please try again.",
                    'status'    => 1
                ];
            }
        } else {
            $this->db->trans_rollback();
            $Return = [
                'msg'       => "Data not valid",
                'status'    => 0
            ];
        }

        echo json_encode($Return);
    }
}
