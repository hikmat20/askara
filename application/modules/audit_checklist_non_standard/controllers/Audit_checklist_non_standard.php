<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Audit Checklist Audit Berdasarkan Kinerja Controller
 *
 * Menu baru untuk membuat checklist audit non-standard
 * Data ditarik dari Jadwal Audit (audit_program_schedule) dan
 * Isu Proses (audit_program_opportunity) berdasarkan proses yang sama.
 */
class Audit_checklist_non_standard extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('audit_checklist_non_standard/Audit_checklist_ns_model', 'model');
        $this->template->set([
            'title' => 'Checklist Audit Berdasarkan Kinerja',
            'icon'  => 'fa fa-clipboard-list'
        ]);
        date_default_timezone_set("Asia/Bangkok");
    }

    /**
     * Index - list all schedules from all active audit programs
     * Each schedule row shows Process, Department, Auditor, Tanggal, Jam, Action
     */
    public function index()
    {
        $schedules = $this->model->getAllSchedules();

        // Check which schedules already have checklist data
        $has_checklist = [];
        foreach ($schedules as $s) {
            $has_checklist[$s->schedule_id] = $this->model->countChecklistByScheduleId($s->schedule_id) > 0;
        }

        $this->template->set('schedules', $schedules);
        $this->template->set('has_checklist', $has_checklist);
        $this->template->render('index');
    }

    /**
     * Create Checklist - form to create checklist for a specific schedule
     *
     * @param int $schedule_id ID from audit_program_schedule
     */
    public function create($schedule_id = null)
    {
        if (!$schedule_id) {
            show_404();
            return;
        }

        // Get schedule info
        $schedule = $this->model->getScheduleById($schedule_id);
        if (!$schedule) {
            show_404();
            return;
        }

        // Get issues matching the process from Isu Proses
        $issues = $this->model->getIssuesByProcess($schedule->program_id, $schedule->process_id);

        // Get existing checklist items if already created
        $existing = $this->model->getChecklistByScheduleId($schedule_id);

        $this->template->set([
            'schedule' => $schedule,
            'issues'   => $issues,
            'existing' => $existing,
        ]);
        $this->template->render('create');
    }

    /**
     * Save checklist items (AJAX)
     */
    public function save()
    {
        $data = $this->input->post();

        if (!$data || empty($data['schedule_id'])) {
            echo json_encode(['status' => 0, 'msg' => 'Data not valid. Please try again.']);
            return;
        }

        $schedule_id = $data['schedule_id'];
        $checklist_items = isset($data['checklist']) ? $data['checklist'] : [];

        $userId = $this->auth->user_id();
        $success = $this->model->saveChecklist($schedule_id, $checklist_items, $userId);

        if ($success) {
            echo json_encode(['status' => 1, 'msg' => 'Checklist berhasil disimpan.']);
        } else {
            echo json_encode(['status' => 0, 'msg' => 'Gagal menyimpan checklist. Silakan coba lagi.']);
        }
    }

    /**
     * Delete a single checklist item (AJAX)
     */
    public function delete_item()
    {
        $id = $this->input->post('id');
        if ($id) {
            $success = $this->model->deleteChecklistItem($id);
            if ($success) {
                echo json_encode(['status' => 1, 'msg' => 'Item berhasil dihapus.']);
            } else {
                echo json_encode(['status' => 0, 'msg' => 'Gagal menghapus item.']);
            }
        } else {
            echo json_encode(['status' => 0, 'msg' => 'Data not valid.']);
        }
    }

    /**
     * View checklist for a specific schedule (read-only)
     *
     * @param int $schedule_id
     */
    public function view($schedule_id = null)
    {
        if (!$schedule_id) {
            show_404();
            return;
        }

        $schedule = $this->model->getScheduleById($schedule_id);
        if (!$schedule) {
            show_404();
            return;
        }

        $issues = $this->model->getIssuesByProcess($schedule->program_id, $schedule->process_id);
        $existing = $this->model->getChecklistByScheduleId($schedule_id);

        $this->template->set([
            'schedule' => $schedule,
            'issues'   => $issues,
            'existing' => $existing,
        ]);
        $this->template->render('view');
    }
}
