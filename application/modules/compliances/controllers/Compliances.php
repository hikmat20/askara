<?php

use Mpdf\Mpdf;

if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * @author Syamsudin
 * @copyright Copyright (c) 2021, Syamsudin
 *
 * This is controller for Perusahaan
 */

class Compliances extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('download');
        $this->load->library(array('upload', 'Image_lib'));
        $this->load->model(array(
            'Aktifitas/aktifitas_model'
        ));

        $this->template->set([
            'title' => 'Compliances',
            'icon' => 'fa fa-user-tie'
        ]);

        date_default_timezone_set("Asia/Bangkok");
    }

    private function _getId()
    {
        $count      = 1;
        $sql        = "SELECT MAX(RIGHT(id,4)) as maxId FROM compliances";
        $result     = $this->db->query($sql)->row();
        if ($result->maxId > 0) {
            $count = $result->maxId + 1;
        }
        return "COMP" . str_pad($count, 4, "0", STR_PAD_LEFT);
    }

    public function index_()
    {
        $reference = $this->db->get_where('view_references', ['status' => 'OPN'])->row();
        if ($reference) {
            $regulations = $this->db->get_where('view_ref_regulations', ['reference_id' => $reference->id])->result();
            $reviews     = $this->db->get_where('compilation_reviews', ['reference_id' => $reference->id])->result();
            $summary     = $this->db->order_by('last_review', 'DESC')->get_where('compilation_reviews', ['reference_id' => $reference->id])->row();
            $users       = $this->db->get_where('view_users', ['company_id' => $this->company, 'status' => 'ACT'])->result();
            // $ArrSub = [];
            // if($subjects) foreach($subjects as $sub){
            //     $ArrSub[$sub->id][];
            // }
            // $ArrSub = [];

        }

        $ArrUsers = [];

        foreach ($users as $usr) {
            $ArrUsers[$usr->id_user] = $usr->full_name;
        }

        $this->template->set([
            'regulations'   => $regulations,
            'reference'     => $reference,
            'reviews'       => $reviews,
            'summary'       => $summary,
            'ArrUsers'       => $ArrUsers,
        ]);

        $this->template->render('index');
    }

    public function index()
    {
        // if (isset($_GET['b']) && $_GET['b']) {
            $ArrUsers = [];
            $regulations = '';
            $reference = '';
            $reviews = '';
            $summary = '';
            $reference = $this->db->get_where('view_references', ['company_id' => $this->company])->row();

            if ($reference) {
                $regulations    = $this->db->get_where('view_ref_regulations', ['reference_id' => $reference->id])->result();
                $reviews        = $this->db->get_where('compilation_reviews', ['reference_id' => $reference->id])->result();
                $users          = $this->db->get_where('view_users', ['company_id' => $this->company, 'status' => 'ACT'])->result();
                $summary        = $this->db->order_by('last_review', 'DESC')->get_where('compilation_reviews', ['reference_id' => $reference->id])->row();
                $subjects       = $this->db->get_where('view_compliance_subjects', ['reference_id' => $reference->id])->result();

                foreach ($users as $usr) {
                    $ArrUsers[$usr->id_user] = $usr->full_name;
                }

                $ArrReg = [];
                foreach ($regulations as $reg) {
                    $ArrReg[$reg->subject][] = $reg;
                }
            }

            $this->template->set([
                'regulations'   => $regulations ?: [],
                'reference'     => $reference ?: [],
                'reviews'       => $reviews ?: [],
                'summary'       => $summary ?: '',
                'ArrUsers'      => $ArrUsers,
                'subjects'      => $subjects,
                'ArrReg'        => $ArrReg,
            ]);
            $this->template->render('list');
        // } else {
        //     $listCompliance = $this->db->get_where('view_references', ['company_id' => $this->company])->result();
        //     $ArrList = [];
        //     if ($listCompliance) foreach ($listCompliance as $k => $v) {
        //         $ArrList[] = $v;
        //     }
        //     $this->template->render('index', ['list' => $ArrList]);
        // }
    }

    public function lists($id = null)
    {
        $data = [];
        if ($id) {
            $reference = $this->db->get_where('view_references', ['id' => $id])->row();
            $data = $this->db->get_where('view_compliances', ['reference_id' => $id, 'company_id' => $reference->company_id])->result();
        }

        $this->template->set([
            'reference' => $reference,
            'data' => $data
        ]);
        $this->template->render('list');
    }

    public function details($id = null)
    {
        $data = [];
        $complianceDtl = [];
        $ArrOpports = [];
        $ArrCompl = [];
        $ArrPasal = [];

        if ($id) {
            $compliance          = $this->db->get_where('view_compliances', ['id' => $id])->row();

            if ($compliance) {
                $data            = $this->db->get_where('view_regulation_paragraphs', ['regulation_id' => $compliance->regulation_id])->result();
                /* data phoaragraph */
                $complianceDtl       = $this->db->get_where('compliance_details', ['regulation_id' => $compliance->regulation_id, 'reference_id' => $compliance->reference_id])->result();
                foreach ($complianceDtl as $dtl) {
                    $ArrCompl[$dtl->prgh_id] = $dtl;
                }

                foreach ($data as $dt) {
                    $ArrPasal[$dt->pasal_id][] = $dt;
                }

                $compOpports = $this->db->get_where('compliance_opports', ['regulation_id' => $compliance->regulation_id])->result();
                foreach ($compOpports as $opp) {
                    $ArrOpports[$opp->prgh_id][] = $opp;
                }
            }

            $users               = $this->db->get_where('view_users', ['company_id' => $this->company, 'status' => 'ACT'])->result();
        }

        $this->template->set([
            'data'          => $data,
            'ArrPasal'      => $ArrPasal,
            'users'         => $users,
            'compliance'    => $compliance,
            'ArrCompl'      => $ArrCompl,
            'ArrOpports'    => $ArrOpports,
        ]);

        $this->template->render('list-desc');
    }

    // public function add($comp_id = null)
    // {
    //     $regulations    = $this->db->get_where('view_ref_regulations', ['status' => 'OPN', 'company_id' => $comp_id])->result();
    //     $compDtl        = $this->db->get_where('view_compliances', ['company_id' => $this->company])->result();

    //     $ArrCompl = [];
    //     foreach ($compDtl as $dtl) {
    //         $ArrCompl[] = $dtl->regulation_id;
    //     }

    //     $this->template->set([
    //         'regulations' => $regulations,
    //         'ArrCompl' => $ArrCompl,
    //     ]);
    //     $this->template->render('add');
    // }

    // public function save_complience()
    // {
    //     $data = $this->input->post();
    //     $data['date']       = date('Y-m-d');
    //     $data['company_id'] = $this->company;
    //     if ($data) {
    //         $this->db->trans_begin();
    //         if (isset($data['id']) && $data['id']) {
    //             $data['modified_at']    = date('Y-m-d H:i:s');
    //             $data['modified_by']    = $this->auth->user_id();
    //             $this->db->update('compliances', $data, ['id' => $data['id']]);
    //         } else {
    //             $data['id']             = $this->_getId();
    //             $data['created_at']     = date('Y-m-d H:i:s');
    //             $data['created_by']     = $this->auth->user_id();
    //             $this->db->insert('compliances', $data);
    //         }

    //         if ($this->db->trans_status() === FALSE) {
    //             $this->db->trans_rollback();
    //             $return        = array(
    //                 'status'        => 0,
    //                 'msg'            => 'Compliance Failed save. Please Try Again!'
    //             );
    //         } else {
    //             $this->db->trans_commit();
    //             $return        = array(
    //                 'status'        => 1,
    //                 'msg'            => 'Compliance successfull saved. Thanks you.'
    //             );
    //         }
    //     } else {
    //         $this->db->trans_commit();
    //         $return        = array(
    //             'status'        => 0,
    //             'msg'            => 'Data not valid. Please Try Again!'
    //         );
    //     }
    //     echo json_encode($return);
    // }

    //Create New Customer
    // public function detail($id = null)
    // {
    //     if ($id) {
    //         $data = $this->db->get_where('view_references', ['id' => $id])->row();
    //         $regulations = $this->db->get_where('view_ref_regulations')->result();
    //         $users = $this->db->get_where('view_users', ['company_id' => $this->company, 'status' => 'ACT'])->result();

    //         $this->template->set([
    //             'data'          => $data,
    //             'regulations'   => $regulations,
    //             'users'         => $users,
    //         ]);
    //         $this->template->render('detail');
    //     } else {
    //     }
    // }

    // public function loadDesc($id = null)
    // {
    //     if ($id) {
    //         $pasal      = $this->db->get_where('regulation_pasal', ['regulation_id' => $id])->row();
    //         $data       = $this->db->get_where('view_regulation_paragraphs', ['regulation_id' => $id])->result();
    //         $users      = $this->db->get_where('view_users', ['company_id' => $this->company, 'status' => 'ACT'])->result();

    //         $ArrPasal   = [];
    //         foreach ($data as $dt) {
    //             $ArrPasal[$dt->pasal_id][] = $dt;
    //         }

    //         $this->template->set([

    //             'data'          => $data,
    //             'pasal'         => $pasal,
    //             'ArrPasal'      => $ArrPasal,
    //             'users'         => $users,
    //         ]);
    //         $this->template->render('list-desc');
    //     }
    // }

    public function save()
    {
        $data       = $this->input->post();

        if (isset($data['detail'])) {
            foreach ($data['detail'] as $key => $dtl) {
                $detailComp[$key] = [
                    'id'                => isset($dtl['id']) ? $dtl['id'] : '',
                    'complience_id'     => $data['compliance_id'],
                    'reference_id'      => $data['reference_id'],
                    'company_id'        => $this->company,
                    'regulation_id'     => $data['regulation_id'],
                    'prgh_id'           => $dtl['prgh_id'],
                    'pasal_id'          => $dtl['pasal_id'],
                    'description'       => $dtl['description'],
                    'compliance_desc'   => $dtl['complience_desc'],
                    'status'            => ($dtl['status']) ?: null,
                ];
            }
        }



        if (isset($data['opport'])) {
            foreach ($data['opport'] as $key => $dtlOpp) {
                foreach ($dtlOpp as $dtl) {
                    $detailOpport[] = [
                        'id'                => isset($dtl['id']) ? $dtl['id'] : '',
                        'compliance_id'     => $data['compliance_id'],
                        'reference_id'      => $data['reference_id'],
                        'prgh_id'           => $dtl['prgh_id'],
                        'company_id'        => $this->company,
                        'regulation_id'     => $data['regulation_id'],
                        'category'          => $dtl['category'],
                        'description'       => $dtl['description'],
                        'action_plan'       => $dtl['action_plan'],
                        'pic'               => $dtl['pic'],
                        'due_date'          => $dtl['due_date'],
                    ];
                }
            }
        }

        if ($data) {
            $this->db->trans_begin();
            if (isset($detailComp) && $detailComp) {
                foreach ($detailComp as $dtlComp) {
                    if ($dtlComp['id']) {
                        $dtlComp['modified_at']        = date('Y-m-d H:i:s');
                        $dtlComp['modified_by']        = $this->auth->user_id();
                        $this->db->update('compliance_details', $dtlComp, ['id' => $dtlComp['id']]);
                    } else {
                        $dtlComp['created_at']        = date('Y-m-d H:i:s');
                        $dtlComp['created_by']        = $this->auth->user_id();
                        $this->db->insert('compliance_details', $dtlComp);
                    }
                }
                // $this->db->insert_batch('compliance_details', $detailComp);
            }

            if (isset($detailOpport) && $detailOpport) {
                foreach ($detailOpport as $k => $op) {
                    if ($op['id']) {
                        $op['modified_at']        = date('Y-m-d H:i:s');
                        $op['modified_by']        = $this->auth->user_id();
                        $this->db->update('compliance_opports', $op, ['id' => $op['id']]);
                    } else {
                        $op['created_at']        = date('Y-m-d H:i:s');
                        $op['created_by']        = $this->auth->user_id();
                        $this->db->insert('compliance_opports', $op);
                    }
                }
            }

            foreach ($data['detail'] as $dtl) {
                $ArrStatus[$dtl['status']][] = $dtl;
            }

            $CMP = (isset($ArrStatus['CMP'])) ? count($ArrStatus['CMP']) : 0;
            $NCM = (isset($ArrStatus['NCM'])) ? count($ArrStatus['NCM']) : 0;
            $NAP = (isset($ArrStatus['NAP'])) ? count($ArrStatus['NAP']) : 0;

            $this->db->update('ref_regulations', [
                'last_update' => date('Y-m-d H:i:s'),
                'total_compliance' => $CMP,
                'total_not_compliance' => $NCM,
                'total_not_applicable' => $NAP
            ], ['id' => $data['compliance_id']]);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $return        = array(
                    'status'        => 0,
                    'msg'            => 'Data Detail Compliance Failed save. Please Try Again!'
                );
            } else {
                $this->db->trans_commit();
                $return        = array(
                    'status'        => 1,
                    'msg'            => 'Data Detail Compliance successfull saved. Thanks you.'
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

    public function saveFile()
    {
        $data = $this->input->post();

        $data['company_id']        = $this->company;
        $DIR_COMP = $this->company;

        // echo '<pre>';
        // print_r($data);
        // print_r($_FILES);
        // echo '</pre>';
        // exit;
        $current_data = $this->db->get_where('compliance_details', ['id' => $data['id']])->row();

        if (($_FILES) && $_FILES['file']['name']) {
            if (!is_dir('./directory/COMPLIANCE/' . $DIR_COMP)) {
                mkdir('./directory/COMPLIANCE/' . $DIR_COMP, 0755, TRUE);
                chmod("./directory/COMPLIANCE/" . $DIR_COMP, 0755);  // octal; correct value of mode
                chown("./directory/COMPLIANCE/" . $DIR_COMP, 'www-data');
            }
            // $new_name 					= $this->fixForUri($data['description']);
            $config['upload_path']          = "./directory/COMPLIANCE/$DIR_COMP"; //path folder
            $config['allowed_types']        = 'pdf|xlsx|docx'; //type yang dapat diakses bisa anda sesuaikan
            $config['encrypt_name']         = true; //Enkripsi nama yang terupload
            // $config['file_name'] 		= $new_name;

            $this->upload->initialize($config);
            if ($this->upload->do_upload('file')) {
                $file = $this->upload->data();
                $data['file']        = $file['file_name'];
                $this->db->trans_begin();
                $this->db->update('compliance_details', $data, ['id' => $data['id']]);

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $Return = [
                        'status' => 0,
                        'msg'     => "FAILED!! Can't save file. Please try again."
                    ];
                    echo json_encode($Return);
                } else {
                    $this->db->trans_commit();
                    if ($current_data->file) {
                        if (file_exists("./directory/COMPLIANCE/$DIR_COMP/$current_data->file")) {
                            unlink("./directory/COMPLIANCE/$DIR_COMP/$current_data->file");
                        }
                    }

                    $Return = [
                        'status' => 1,
                        'msg'     => "Save file succesfull."
                    ];
                    echo json_encode($Return);
                }
            } else {
                $error_msg = $this->upload->display_errors();
                $Return = [
                    'status' => 0,
                    'msg'     => $error_msg
                ];
                echo json_encode($Return);
                return false;
            };
        }
    }

    function file($file)
    {
        $id = $this->input->get('_init');
        if (isset($id) && $id) {
            $checkData = $this->db->get_where('compliance_details', ['id' => $id, 'file' => $file])->row();
            if ($checkData) {
                $this->load->view('file', ['id' => $id, 'file' => $file, 'comp' => $this->company]);
            } else {
                $this->error_page($id);
            }
        } else {
            $this->error_page($id);
        }
    }

    public function removeFile()
    {
        $id = $this->input->post('id');
        if (isset($id) && $id) {
            $data = $this->db->get_where('compliance_details', ['id' => $id])->row();
            if ($data) {
                $this->db->trans_begin();
                $this->db->update('compliance_details', ['file' => null], ['id' => $id]);

                if (file_exists("./directory/COMPLIANCE/$this->company/$data->file")) {
                    unlink("./directory/COMPLIANCE/$this->company/$data->file");
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $Return = [
                        'status' => 0,
                        'msg'     => "FAILED!! Can't remove file. Please try again."
                    ];
                    echo json_encode($Return);
                } else {
                    $this->db->trans_commit();
                    $Return = [
                        'status' => 1,
                        'msg'     => "Remove file succesfull."
                    ];
                    echo json_encode($Return);
                }
            } else {
                $Return = [
                    'status' => 0,
                    'msg'     => "FAILED!! File not found."
                ];
            }
        } else {
            $Return = [
                'status' => 0,
                'msg'     => "FAILED!! File not found."
            ];
        }
    }

    private function error_page($data)
    {
        $this->load->view('errors/html/error_404_custome');
        return error_log('sdaa', 0, 'file compliance', $data);
    }

    public function view_compliance($id = null)
    {
        if ($id) {
            $reference      = $this->db->get_where('view_references', ['id' => $id])->row();
            $regulations    = $this->db->get_where('view_compliance_details', ['reference_id' => $reference->id])->result();
            $opports        = $this->db->get_where('view_comp_opports', ['reference_id' => $reference->id])->result();
            $summary        = $this->db->order_by('last_review', 'DESC')->get_where('compilation_reviews', ['reference_id' => $reference->id])->row();
            $users          = $this->db->get_where('view_users', ['company_id' => $this->company, 'status' => 'ACT'])->result();

            $cat            = [
                'OPP' => 'Peluang',
                'RSK' => 'Resiko'
            ];

            $status            = [
                'CMP' => '<span class="badge badge-success">Compliance</span>',
                'NCM' => '<span class="badge badge-danger">Not Compliance</span>',
                'NAP' => '<span class="badge badge-secondary">Not Applicable</span>'
            ];

            $ArrReg         = [];
            $ArrOpports     = [];
            $ArrUsers       = [];

            foreach ($regulations as $reg) {
                $ArrReg[$reg->regulation_id][] = $reg;
            }

            foreach ($opports as $opr) {
                $ArrOpports[$opr->prgh_id][] = $opr;
            }

            foreach ($users as $usr) {
                $ArrUsers[$usr->id_user] = $usr->full_name;
            }

            $Data = [
                'reference'     => $reference,
                'regulations'   => $regulations,
                'ArrReg'        => $ArrReg,
                'ArrOpports'    => $ArrOpports,
                'cat'           => $cat,
                'summary'       => $summary,
                'ArrUsers'      => $ArrUsers,
                'status'        => $status,
            ];

            $this->template->set($Data);
            $this->template->render('view_compilation');
        }
    }

    public function view_compliance_regulation($id = null)
    {
        $data = [];
        $complianceDtl = [];
        $ArrOpports = [];
        $ArrCompl = [];
        $ArrPasal = [];
        $ArrUsers = [];

        if ($id) {
            $compliance          = $this->db->get_where('view_compliances', ['id' => $id])->row();

            if ($compliance) {
                $data            = $this->db->get_where('view_regulation_paragraphs', ['regulation_id' => $compliance->regulation_id])->result();
                /* data phoaragraph */
                $complianceDtl       = $this->db->get_where('compliance_details', ['regulation_id' => $compliance->regulation_id])->result();
                foreach ($complianceDtl as $dtl) {
                    $ArrCompl[$dtl->prgh_id] = $dtl;
                }

                foreach ($data as $dt) {
                    $ArrPasal[$dt->pasal_id][] = $dt;
                }

                $compOpports = $this->db->get_where('compliance_opports', ['regulation_id' => $compliance->regulation_id])->result();
                foreach ($compOpports as $opp) {
                    $ArrOpports[$opp->prgh_id][] = $opp;
                }
            }

            $users               = $this->db->get_where('view_users', ['company_id' => $this->company, 'status' => 'ACT'])->result();
            foreach ($users as $usr) {
                $ArrUsers[$usr->id_user] = $usr->full_name;
            }
        }

        $status            = [
            'CMP' => '<span class="badge badge-success">Compliance</span>',
            'NCM' => '<span class="badge badge-danger">Not Compliance</span>',
            'NAP' => '<span class="badge badge-secondary">Not Applicable</span>'
        ];

        $this->template->set([
            'data'          => $data,
            'ArrPasal'      => $ArrPasal,
            'ArrUsers'      => $ArrUsers,
            'compliance'    => $compliance,
            'ArrCompl'      => $ArrCompl,
            'ArrOpports'    => $ArrOpports,
            'status'        => $status,

        ]);

        $this->template->render('view_comp_regulation');
    }

    public function show_compilation($id = null, $status = null)
    {

        if ($id) {
            $reference      = $this->db->get_where('view_references', ['id' => $id])->row();
            $where = [
                'reference_id' => $reference->id,
            ];

            if ($status) {
                $where = [
                    'reference_id' => $reference->id,
                    'status'    => $status
                ];
            }

            $regulations    = $this->db->get_where('view_compliance_details', $where)->result();
            $opports        = $this->db->get_where('view_comp_opports', ['reference_id' => $reference->id])->result();
            // $summary        = $this->db->order_by('last_review', 'DESC')->get_where('compilation_reviews', ['reference_id' => $reference->id])->row();
            $users          = $this->db->get_where('view_users', ['company_id' => $this->company, 'status' => 'ACT'])->result();

            $cat            = [
                'OPP' => 'Peluang',
                'RSK' => 'Resiko'
            ];

            $status            = [
                'CMP' => '<span class="badge badge-success">Compliance</span>',
                'NCM' => '<span class="badge badge-danger">Not Compliance</span>',
                'NAP' => '<span class="badge badge-secondary">Not Applicable</span>'
            ];

            $ArrReg         = [];
            $ArrOpports     = [];
            $ArrUsers       = [];

            foreach ($regulations as $reg) {
                $ArrReg[$reg->regulation_id][] = $reg;
            }

            foreach ($opports as $opr) {
                $ArrOpports[$opr->prgh_id][] = $opr;
            }

            foreach ($users as $usr) {
                $ArrUsers[$usr->id_user] = $usr->full_name;
            }

            $Data = [
                'reference'     => $reference,
                'regulations'   => $regulations,
                'ArrReg'        => $ArrReg,
                'ArrOpports'    => $ArrOpports,
                'cat'           => $cat,
                // 'summary'       => $summary,
                'ArrUsers'      => $ArrUsers,
                'status'        => $status,
            ];

            $this->template->set($Data);
            // $this->template->render('view_compilation');
            $this->template->render('show-compilation');
        }
    }

    public function compilation($id = null)
    {
        if ($id) {
            $reference      = $this->db->get_where('view_references', ['id' => $id])->row();
            $regulations    = $this->db->get_where('view_compliance_details', ['reference_id' => $reference->id])->result();
            $opports        = $this->db->get_where('view_comp_opports', ['reference_id' => $reference->id])->result();
            $opports        = $this->db->get_where('view_comp_opports', ['reference_id' => $reference->id])->result();
            $opports        = $this->db->get_where('view_comp_opports', ['reference_id' => $reference->id])->result();
            $users          = $this->db->get_where('view_users', ['company_id' => $this->company, 'status' => 'ACT'])->result();

            $cat            = [
                'OPP' => 'Peluang',
                'RSK' => 'Resiko'
            ];

            $status            = [
                'CMP' => '<span class="badge badge-success">Compliance</span>',
                'NCM' => '<span class="badge badge-danger">Not Compliance</span>',
                'NAP' => '<span class="badge badge-secondary">Not Applicable</span>'
            ];

            $ArrReg         = [];
            $ArrOpports     = [];
            $ArrUsers       = [];

            foreach ($regulations as $reg) {
                $ArrReg[$reg->regulation_id][] = $reg;
            }

            foreach ($opports as $opr) {
                $ArrOpports[$opr->prgh_id][] = $opr;
            }

            foreach ($users as $usr) {
                $ArrUsers[$usr->id_user] = $usr->full_name;
            }

            $Data = [
                'reference'     => $reference,
                'regulations'   => $regulations,
                'ArrReg'        => $ArrReg,
                'ArrOpports'    => $ArrOpports,
                'cat'           => $cat,
                'ArrUsers'      => $ArrUsers,
                'status'        => $status,
            ];

            $this->template->set($Data);
            $this->template->render('compilation');
        }
    }

    public function review($subject = null)
    {
        if ($subject) {
            // $reference      = $this->db->get_where('view_references', ['id' => $id])->row();
            $regulations    = $this->db->get_where('view_compliance_details', ['subject' => $subject])->result();
            $users          = $this->db->get_where('view_users', ['company_id' => $this->company, 'status' => 'ACT'])->result();

            $cat            = [
                'OPP' => 'Peluang',
                'RSK' => 'Resiko'
            ];

            $status            = [
                'CMP' => '<span class="badge badge-success">Compliance</span>',
                'NCM' => '<span class="badge badge-danger">Not Compliance</span>',
                'NAP' => '<span class="badge badge-secondary">Not Applicable</span>'
            ];

            $ArrReg         = [];
            $ArrOpports     = [];
            $ArrUsers       = [];

            foreach ($regulations as $reg) {
                $ArrReg[$reg->regulation_id][] = $reg;
            }

            foreach ($users as $usr) {
                $ArrUsers[$usr->id_user] = $usr->full_name;
            }

            $Data = [
                'regulations'   => $regulations,
                'ArrReg'        => $ArrReg,
                'ArrOpports'    => $ArrOpports,
                'cat'           => $cat,
                'ArrUsers'      => $ArrUsers,
                'status'        => $status,
            ];

            $this->template->set($Data);
            $this->template->render('compilation');
        }
    }

    public function history($subject = null)
    {
        if ($subject) {
            $review    = $this->db->get_where('compilation_reviews', ['subject' => $subject])->result();
            $Data = [
                'review'   => $review,
            ];

            $this->template->set($Data);
            $this->template->render('history-review');
        }
    }

    public function save_review()
    {
        $mpdf           = new Mpdf();
        $id             = $this->input->post('id');
        $subject        = $this->input->post('subject');
        $rand_text      = uniqid(date('YmdHis-'));

        // create pdf
        $mpdf->AddPage(
            'L',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            0,
            0,
            0,
            0,
            '',
            'Legal-L'
        );

        if ($id) {
            $reference      = $this->db->get_where('view_references', ['id' => $id])->row();
            $regulations    = $this->db->get_where('view_compliance_details', ['reference_id' => $reference->id, 'subject' => $subject])->result();
            $opports        = $this->db->get_where('view_comp_opports', ['reference_id' => $reference->id])->result();
            $users          = $this->db->get_where('view_users', ['company_id' => $this->company, 'status' => 'ACT'])->result();
            $summary        = $this->db->order_by('last_review', 'DESC')->get_where('compilation_reviews', ['reference_id' => $reference->id])->row();

            $cat            = [
                'OPP' => 'Peluang',
                'RSK' => 'Resiko'
            ];

            $status            = [
                'CMP' => 'Memenuhi',
                'NCM' => 'Belum Memenuhi',
                'NAP' => 'Tidak Teraplikasi'
            ];

            $ArrReg         = [];
            $ArrOpports     = [];
            $ArrUsers       = [];
            $TC             = $TNC = $TNA = 0;

            foreach ($regulations as $reg) {
                $ArrReg[$reg->regulation_id][] = $reg;
                if ($reg->status == 'CMP') {
                    $TC++;
                }
                if ($reg->status == 'NCM') {
                    $TNC++;
                }
                if ($reg->status == 'NAP') {
                    $TNA++;
                }
            }

            $summary = [
                'TC' => $TC,
                'TNC' => $TNC,
                'TNA' => $TNA,
            ];

            foreach ($opports as $opr) {
                $ArrOpports[$opr->prgh_id][] = $opr;
            }

            foreach ($users as $usr) {
                $ArrUsers[$usr->id_user] = $usr->full_name;
            }

            $Data = [
                'reference'     => $reference,
                'regulations'   => $regulations,
                'ArrReg'        => $ArrReg,
                'ArrOpports'    => $ArrOpports,
                'cat'           => $cat,
                'ArrUsers'      => $ArrUsers,
                'summary'       => $summary,
                'status'        => $status,
            ];

            $page           = $this->load->view('export-pdf', $Data, TRUE);
            // $mpdf->Output();
            $this->db->trans_begin();
            // update review
            $Review = [
                'reference_id'           => $id,
                'company_id'             => $reference->company_id,
                'last_review'            => date('Y-m-d H:i:s'),
                'reference_id'           => $id,
                'subject'                => $subject,
                'total_compliance'       => $TC,
                'total_not_compliance'   => $TNC,
                'total_not_applicable'   => $TNA,
                'document'               => $rand_text . '.pdf',
            ];

            $this->db->insert('compilation_reviews', $Review);
            $count = $this->db->get_where('compilation_reviews')->num_rows();
            $this->db->update('references', [
                'counter_review' => $count,
                'last_review'    => $Review['last_review'],
                'review_by'      => $this->auth->user_id(),
            ], ['id' => $id]);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                $return        = array(
                    'status'   => 0,
                    'msg'      => 'Compliance Failed save. Please Try Again!'
                );
            } else {
                $this->db->trans_commit();
                $mpdf->WriteHTML($page);
                if (!is_dir("./directory/COMPILATIONS")) {
                    mkdir("./directory/COMPILATIONS", 0755, TRUE);
                    chmod("./directory/COMPILATIONS", 0755);  // octal; correct value of mode
                    chown("./directory/COMPILATIONS", 'www-data');
                }

                $mpdf->Output("./directory/COMPILATIONS/" . $rand_text . ".pdf", 'F');
                $return        = array(
                    'status'   => 1,
                    'msg'      => 'Compliance successfull saved. Thanks you.'
                );
            }
        } else {
            $return        = array(
                'status'   => 0,
                'msg'      => 'Data Not Valid. Please Try Again!'
            );
        }

        echo json_encode($return);
    }

    /* PRINTOUT */
    public function export_pdf($id = null, $status = null)
    {
        $mpdf               = new Mpdf();
        $mpdf->AddPage(
            'L',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            0,
            0,
            0,
            0,
            '',
            'Legal-L'
        );

        $where = [
            'reference_id' => $id
        ];

        if ($status) {
            $where = [
                'reference_id' => $id,
                'status' => $status
            ];
        }

        if ($id) {
            $reference      = $this->db->get_where('view_references', ['id' => $id])->row();
            $regulations    = $this->db->get_where('view_compliance_details', $where)->result();
            $opports        = $this->db->get_where('view_comp_opports', ['reference_id' => $reference->id])->result();
            $users          = $this->db->get_where('view_users', ['company_id' => $this->company, 'status' => 'ACT'])->result();
            $subject        = $this->db->get_where('view_compliance_subjects', ['company_id' => $this->company])->result();

            $TC             = $TNC = $TNA = 0;
            foreach ($regulations as $reg) {
                // $ArrReg[$reg->regulation_category][] = $reg;
                if ($reg->status == 'CMP') {
                    $TC++;
                }
                if ($reg->status == 'NCM') {
                    $TNC++;
                }
                if ($reg->status == 'NAP') {
                    $TNA++;
                }
            }

            $summary = [
                'TC' => $TC,
                'TNC' => $TNC,
                'TNA' => $TNA,
            ];

            $cat            = [
                'OPP' => 'Peluang',
                'RSK' => 'Resiko'
            ];

            $status            = [
                'CMP' => 'Memenuhi',
                'NCM' => 'Belum Memenuhi',
                'NAP' => 'Tidak Teraplikasi'
            ];

            $ArrReg         = [];
            $ArrOpports     = [];
            $ArrUsers       = [];

            foreach ($regulations as $reg) {
                $ArrReg[$reg->subject][] = $reg;
            }

            foreach ($opports as $opr) {
                $ArrOpports[$opr->prgh_id][] = $opr;
            }

            foreach ($users as $usr) {
                $ArrUsers[$usr->id_user] = $usr->full_name;
            }

            $Data = [
                'reference'     => $reference,
                'regulations'   => $regulations,
                'ArrReg'        => $ArrReg,
                'ArrOpports'    => $ArrOpports,
                'cat'           => $cat,
                'ArrUsers'      => $ArrUsers,
                'summary'       => $summary,
                'status'        => $status,
                'subject'        => $subject,
            ];

            $page           = $this->load->view('export-pdf', $Data, TRUE);
            $mpdf->WriteHTML($page);
        } else {
            $mpdf->WriteHTML("Data not valid");
        }
        $mpdf->Output();
        // $mpdf->Output('filename.pdf', 'F');
    }
}
