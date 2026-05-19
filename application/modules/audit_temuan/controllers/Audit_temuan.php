<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Hikmat
 * @copyright Copyright (c) 2024, Hikmat
 *
 */

class Audit_temuan extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->template->set([
            'title' => 'Temuan Audit',
            'icon' => 'fa fa-clipboard-check'
        ]);

        date_default_timezone_set("Asia/Bangkok");
    }

    private function _getId()
    {
        $count    = 1;
        $result   = $this->db->select('MAX(RIGHT(id,3)) as id')->from('audit_temuan')->where(['YEAR(date)' => date('Y'), 'MONTH(date)' => date('m')])->get()->row();

        if ($result->id > 0) {
            $count = $result->id + 1;
        }
        return "AT" . date('ym-') . sprintf("%03d", $count);
    }

    private function _getStdId()
    {
        $count    = 1;
        $result   = $this->db->select('MAX(RIGHT(id,3)) as id')->from('audit_standard')->where(['SUBSTR(id,3,4)' => date('ym')])->get()->row();

        if ($result->id > 0) {
            $count = $result->id + 1;
        }

        return $count;
    }

    private function _getDtlId($audit_id)
    {
        $count    = 1;
        $result   = $this->db->select('MAX(RIGHT(id,3)) as id')->from('audit_temuan_details')->where(['audit_id' => $audit_id])->get()->row();
        if ($result->id > 0) {
            $count = $result->id + 1;
        }
        return $audit_id . "-" . sprintf("%03d", $count);
    }

    public function index()
    {
        $data = $this->db->get_where('view_audit_temuan')->result();
        $this->template->set('data', $data);
        $this->template->render('index');
    }

    public function add()
    {
        $axist_company  = $this->db->get_where('audit_temuan', ['status' => 1])->result_array();
        $arr_comp       = array_column($axist_company, 'company_id');
        $companies      = $this->db->where_not_in('id', ($arr_comp) ?: 1)->get_where('audit_company', ['status' => '1'])->result();
        $badan          = $this->db->get_where('audit_badan_sertifikasi', ['status' => '1'])->result();

        $this->template->set('badan', $badan);
        $this->template->set('companies', $companies);
        $this->template->render('add');
    }

    public function edit($id)
    {
        $data               = $this->db->get_where('view_audit_temuan_details', ['id' => $id, 'status' => '1'])->row();
        $pasals             = $this->db->get_where('requirement_details', ['requirement_id' => $data->standard_id])->result();
        $requirement        = $this->db->get_where('requirements', ['status' => '1', 'company_id' => $this->company])->result();
        $auditors           = $this->db->get_where('audit_auditor_badan_sertifikasi', ['badan_sert_id' => $data->badan_sert_id])->result();
        $process            = $this->db->get_where('audit_process', ['status' => '1'])->result();
        $consultant         = $this->db->like('position', '2')->get_where('audit_auditor_consultant', ['status' => '1'])->result();
        $auditorInternal    = $this->db->like('position', '1')->get_where('audit_auditor_consultant', ['status' => '1'])->result();

        $this->template->set([
            'data'              => $data,
            'requirement'       => $requirement,
            'pasals'            => $pasals,
            'auditors'          => $auditors,
            'process'           => $process,
            'consultant'        => $consultant,
            'auditorInternal'   => $auditorInternal,
        ]);
        $this->template->render('edit');
    }

    public function save()
    {
        $data       = $this->input->post();
        $this->db->trans_begin();
        if ($data) {
            if (isset($data['id']) && $data['id']) {
                $data['modified_at'] = date('Y-m-d H:i:s');
                $data['modified_by'] = $this->auth->user_id();
                $this->db->update('audit_temuan', $data, ['id' => $data['id']]);
            } else {
                $data['id']         = $this->_getId();
                $data['date']       = date('Y-m-d');
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['created_by'] = $this->auth->user_id();
                $this->db->insert('audit_temuan', $data);
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

    public function save_detail()
    {
        $data       = $this->input->post();
        $standard   = $data['standard'];

        $this->db->trans_begin();
        if ($standard) {
            $code = "ST" . date('ym-');
            $lastID = $this->_getStdId();
            foreach ($standard as $k => $std) {
                $std['id']         = $code . sprintf("%03d", $lastID++);
                $std['audit_id']   = $data['audit_id'];
                $std['company_id'] = $data['company_id'];
                $std['created_at'] = date('Y-m-d H:i:s');
                $std['created_by'] = $this->auth->user_id();
                $this->db->insert('audit_standard', $std);
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

    public function save_temuan()
    {
        $data           = $this->input->post();
        $ArrPasalName   = [];
        $reqDtl         = $this->db->where_in('id', $data['pasal_id'])->get('requirement_details')->result_array();
        $ArrPasalName   = json_encode(array_column($reqDtl, "chapter"));

        $this->db->trans_begin();
        if ($data) {
            $data['pasal_id']   = json_encode($data['pasal_id']);
            $data['pasal_name'] = $ArrPasalName;

            if (isset($data['id']) && $data['id']) {
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['created_by'] = $this->auth->user_id();
                $this->db->update('audit_temuan_details', $data, ['id' => $data['id']]);
            } else {
                $data['id']         = $this->_getDtlId($data['audit_id']);
                $data['audit_id']   = $data['audit_id'];
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['created_by'] = $this->auth->user_id();
                $this->db->insert('audit_temuan_details', $data);
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

    public function view($id = null)
    {
        $dataStd = $this->db->get_where('view_audit_standard', ['id' => $id])->row();
        $dataAudit = $this->db->get_where('view_audit_temuan', ['id' => $dataStd->audit_id])->row();
        $details        = $this->db->get_where('view_audit_temuan_details', ['audit_standard_id' => $id, 'status' => '1'])->result();

        $this->template->set([
            'dataStd' => $dataStd,
            'dataAudit' => $dataAudit,
            'details' => $details,
        ]);
        $this->template->render('view');
    }

    function delete_standard()
    {
        $id = $this->input->post('id');
        if ($id) {
            $this->db->trans_begin();
            $this->db->update('audit_standard', ['status' => '0'], ['id' => $id]);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $Return = [
                    'msg'       => "Successfull save data audit standard.",
                    'status'    => 0
                ];
            } else {
                $this->db->trans_commit();
                $Return = [
                    'msg'       => "Failed save data audit standard, please try again.",
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

    function delete_detail()
    {
        $id = $this->input->post('id');
        if ($id) {
            $this->db->trans_begin();
            $this->db->update('audit_temuan_details', ['status' => '0'], ['id' => $id]);
            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $Return = [
                    'msg'       => "Successfull save data scopes.",
                    'status'    => 0
                ];
            } else {
                $this->db->trans_commit();
                $Return = [
                    'msg'       => "Failed save data scopes, please try again.",
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

    /* Detail */
    public function detail($id = null, $std = null)
    {
        if ($id) {
            $this->template->set('title', 'Detail Audit');
            $this->template->set('icon', 'fa fa-list');

            $data               = $this->db->get_where('view_audit_temuan', ['id' => $id, 'status' => 1])->row();
            $dataStd            = $this->db->get_where('view_audit_standard', ['audit_id' => $id, 'status' => '1'])->result();
            $requirement        = $this->db->get_where('requirements', ['status' => '1', 'company_id' => $this->company])->result();
            // $auditors           = $this->db->get_where('audit_auditor_badan_sertifikasi', ['badan_sert_id' => $data->badan_sert_id])->result();
            $consultant         = $this->db->like('position', '2')->get_where('audit_auditor_consultant', ['status' => '1'])->result();
            $auditorInternal    = $this->db->like('position', '1')->get_where('audit_auditor_consultant', ['status' => '1'])->result();

            $this->template->set([
                'data'              => $data,
                'requirement'       => $requirement,
                'dataStd'           => $dataStd,
                // 'auditors'          => isset($auditors) ? $auditors : '',
                'consultant'        => isset($consultant) ? $consultant : '',
                'auditorInternal'   => isset($auditorInternal) ? $auditorInternal : '',
            ]);

            if ($std) {
                $this->_temuan($std);
            } else {
                $this->template->render('detail');
            }
        } else {
            show_404();
        }
    }

    private function _temuan($id)
    {
        $dataStd            = $this->db->get_where('view_audit_standard', ['id' => $id, 'status' => '1'])->row();
        $details            = $this->db->get_where('view_audit_temuan_details', ['audit_standard_id' => $id, 'status' => '1'])->result();
        $dataAudit          = $this->db->get_where('audit_temuan', ['id' => $dataStd->audit_id])->row();
        $pasals             = $this->db->get_where('requirement_details', ['requirement_id' => $dataStd->standard_id])->result();
        $process             = $this->db->get_where('audit_process', ['status' => '1'])->result();
        $auditors           = $this->db->get_where('audit_auditor_badan_sertifikasi', ['badan_sert_id' => $dataAudit->badan_sert_id])->result();
        $consultant         = $this->db->like('position', '2')->get_where('audit_auditor_consultant', ['status' => '1'])->result();
        $auditorInternal    = $this->db->like('position', '1')->get_where('audit_auditor_consultant', ['status' => '1'])->result();

        $this->template->set([
            'standard'          => $dataStd,
            'pasals'            => $pasals,
            'details'           => $details,
            'process'           => $process,
            'auditors'          => $auditors,
            'consultant'        => $consultant,
            'auditorInternal'   => $auditorInternal,
        ]);

        if ($dataStd) {
            $this->template->render('temuan');
        } else {
            show_404();
        }
    }
}
