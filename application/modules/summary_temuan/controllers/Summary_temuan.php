<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Summary Temuan Audit Controller
 */
class Summary_temuan extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('pelaksanaan_audit/Pelaksanaan_audit_model', 'model');
        $this->template->set([
            'title' => 'Summary Temuan Audit',
            'icon'  => 'fa fa-chart-bar'
        ]);
    }

    /**
     * Index - list audit programs (same as pelaksanaan_audit index)
     */
    public function index()
    {
        $programs = $this->model->getActivePrograms();
        $this->template->set('programs', $programs);
        $this->template->render('index');
    }

    /**
     * View summary for a specific program
     *
     * @param string $program_id
     */
    public function view($program_id = null)
    {
        if (!$program_id) {
            show_404();
            return;
        }

        // Get program header
        $program = $this->db->select('audit_program.*, audit_auditor_consultant.name as auditor_name')
            ->from('audit_program')
            ->join('audit_auditor_consultant', 'audit_auditor_consultant.id = audit_program.lead_auditor_id', 'left')
            ->where('audit_program.id', $program_id)
            ->get()->row();

        if (!$program) {
            show_404();
            return;
        }

        // Get all schedules for this program
        $schedules = $this->model->getSchedulesByProgram($program_id);

        // For each schedule, get audit data (temuan, conformity, requirement details)
        $schedule_data = [];
        foreach ($schedules as $sched) {
            $audit = $this->model->getAuditByScheduleId($sched->schedule_id);
            $item = new stdClass();
            $item->schedule = $sched;
            $item->audit = $audit;
            $item->temuan = [];
            $item->conformity = [];
            $item->counts = ['Major' => 0, 'Minor' => 0, 'OFI' => 0];
            $item->requirement_details = [];
            $item->audit_requirement_details = [];

            if ($audit) {
                $temuan = $this->model->getAuditTemuan($audit->id);
                $item->temuan = $temuan;
                $item->conformity = $this->model->getAuditConformity($audit->id);

                // Count by kategori
                foreach ($temuan as $tm) {
                    if (isset($item->counts[$tm->kategori])) {
                        $item->counts[$tm->kategori]++;
                    }
                }

                // Load requirement details for Audit Persyaratan
                if (!empty($sched->requirement_id)) {
                    $item->requirement_details = $this->model->getPasalByRequirement($sched->requirement_id);
                    $item->audit_requirement_details = $this->model->getAuditRequirementDetails($audit->id);
                }
            }

            $schedule_data[] = $item;
        }

        // Calculate total summary
        $total_counts = ['Major' => 0, 'Minor' => 0, 'OFI' => 0];
        foreach ($schedule_data as $sd) {
            $total_counts['Major'] += $sd->counts['Major'];
            $total_counts['Minor'] += $sd->counts['Minor'];
            $total_counts['OFI'] += $sd->counts['OFI'];
        }

        // Get standards for ISO name lookup
        $standards = $this->db->get_where('requirements', [])->result();
        $std_map = [];
        foreach ($standards as $std) {
            $std_map[$std->id] = $std->name;
        }

        $this->template->set([
            'program'       => $program,
            'schedule_data' => $schedule_data,
            'total_counts'  => $total_counts,
            'std_map'       => $std_map,
        ]);
        $this->template->render('view');
    }

    /**
     * Print PDF - generate PDF report of summary temuan
     *
     * @param string $program_id
     */
    public function print_pdf($program_id = null)
    {
        if (!$program_id) {
            show_404();
            return;
        }

        $program = $this->db->select('audit_program.*, audit_auditor_consultant.name as auditor_name')
            ->from('audit_program')
            ->join('audit_auditor_consultant', 'audit_auditor_consultant.id = audit_program.lead_auditor_id', 'left')
            ->where('audit_program.id', $program_id)
            ->get()->row();

        if (!$program) {
            show_404();
            return;
        }

        $schedules = $this->model->getSchedulesByProgram($program_id);

        $schedule_data = [];
        foreach ($schedules as $sched) {
            $audit = $this->model->getAuditByScheduleId($sched->schedule_id);
            $item = new stdClass();
            $item->schedule = $sched;
            $item->audit = $audit;
            $item->temuan = [];
            $item->conformity = [];
            $item->counts = ['Major' => 0, 'Minor' => 0, 'OFI' => 0];
            $item->requirement_details = [];
            $item->audit_requirement_details = [];

            if ($audit) {
                $temuan = $this->model->getAuditTemuan($audit->id);
                $item->temuan = $temuan;
                $item->conformity = $this->model->getAuditConformity($audit->id);

                foreach ($temuan as $tm) {
                    if (isset($item->counts[$tm->kategori])) {
                        $item->counts[$tm->kategori]++;
                    }
                }

                // Load requirement details for Audit Persyaratan
                if (!empty($sched->requirement_id)) {
                    $item->requirement_details = $this->model->getPasalByRequirement($sched->requirement_id);
                    $item->audit_requirement_details = $this->model->getAuditRequirementDetails($audit->id);
                }
            }

            $schedule_data[] = $item;
        }

        $total_counts = ['Major' => 0, 'Minor' => 0, 'OFI' => 0];
        foreach ($schedule_data as $sd) {
            $total_counts['Major'] += $sd->counts['Major'];
            $total_counts['Minor'] += $sd->counts['Minor'];
            $total_counts['OFI'] += $sd->counts['OFI'];
        }

        $standards = $this->db->get_where('requirements', [])->result();
        $std_map = [];
        foreach ($standards as $std) {
            $std_map[$std->id] = $std->name;
        }

        $data = [
            'program'       => $program,
            'schedule_data' => $schedule_data,
            'total_counts'  => $total_counts,
            'std_map'       => $std_map,
        ];

        $html = $this->load->view('summary_temuan/pdf', $data, true);

        require_once(APPPATH . 'libraries/MPDF_/mpdf.php');
        $mpdf = new mPDF('utf-8', 'A4', 0, '', 15, 15, 15, 15, 0, 0);
        $mpdf->SetTitle('Summary Temuan Audit - ' . $program->id);
        $mpdf->WriteHTML($html);
        $mpdf->Output('Summary_Temuan_' . $program->id . '.pdf', 'I');
    }
}
