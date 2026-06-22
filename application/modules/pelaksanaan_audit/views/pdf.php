<style>
    body { font-family: sans-serif; font-size: 11px; }
    h2 { font-size: 16px; margin-bottom: 10px; }
    h3 { font-size: 13px; margin: 15px 0 8px 0; color: #333; }
    h4 { font-size: 12px; margin: 12px 0 6px 0; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    th, td { border: 1px solid #333; padding: 5px 8px; vertical-align: top; }
    th { background-color: #f0f0f0; font-weight: bold; text-align: center; }
    .no-border td, .no-border th { border: none; padding: 3px 8px; }
    .text-center { text-align: center; }
    .text-muted { color: #999; font-style: italic; }
    .section-title { background-color: #e8f5e9; padding: 6px 10px; margin: 15px 0 8px 0; font-weight: bold; font-size: 12px; }
</style>

<h2 style="text-align:center;">Laporan Pelaksanaan Audit</h2>

<!-- HEADER -->
<table class="no-border" style="width:50%;">
    <tr><th style="width:150px; text-align:left;">Prosedur</th><td><?= !empty($schedule->process_name) ? strip_tags($schedule->process_name) : htmlspecialchars($schedule->process_name_free); ?></td></tr>
    <tr><th style="text-align:left;">Date</th><td><?= date('d/m/Y', strtotime($schedule->audit_date)); ?></td></tr>
    <tr><th style="text-align:left;">Department - Company</th><td><?= isset($schedule->department_name) ? $schedule->department_name : '-'; ?></td></tr>
    <tr><th style="text-align:left;">Auditor</th><td><?= isset($schedule->auditor_name) ? $schedule->auditor_name : '-'; ?></td></tr>
    <tr><th style="text-align:left;">Auditee</th><td><?= isset($audit_data->auditee_text) && $audit_data->auditee_text ? nl2br(htmlspecialchars($audit_data->auditee_text)) : '-'; ?></td></tr>
</table>

<!-- ISU PROSES -->
<div class="section-title">Isu Proses</div>
<?php if (!empty($issues)) : ?>
<table>
    <thead><tr><th width="35%">Issue</th><th>Investigasi</th></tr></thead>
    <tbody>
        <?php foreach ($issues as $issue) : ?>
            <tr><td><?= htmlspecialchars($issue->description); ?></td><td><?= htmlspecialchars(isset($issue->investigation) ? $issue->investigation : ''); ?></td></tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else : ?>
<p class="text-muted">Tidak ada isu proses.</p>
<?php endif; ?>

<!-- KESIMPULAN AUDIT -->
<div class="section-title">Kesimpulan Audit</div>

<!-- Conformity / Strong Point -->
<h4>Conformity / Strong Point</h4>
<?php if (!empty($audit_conformity)) : ?>
<table>
    <thead><tr><th width="30">No</th><th>Strong Point</th></tr></thead>
    <tbody>
        <?php foreach ($audit_conformity as $k => $cf) : $k++; ?>
            <tr>
                <td class="text-center"><?= $k; ?></td>
                <td><?= nl2br(htmlspecialchars($cf->description)); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else : ?>
<p class="text-muted">Tidak ada data conformity.</p>
<?php endif; ?>

<!-- Temuan -->
<h4>Temuan</h4>
<?php if (!empty($audit_temuan)) : ?>
<table>
    <thead><tr><th width="30">No</th><th>Temuan</th><th width="60">Kategori</th><th width="80">Reference Standard</th><th width="100">Pasal</th></tr></thead>
    <tbody>
        <?php foreach ($audit_temuan as $k => $tm) : $k++;
            $iso_name = '';
            $pasal_name = '';
            if (!empty($tm->iso_id)) {
                foreach ($standards as $std) {
                    if ($std->id == $tm->iso_id) { $iso_name = $std->name; break; }
                }
            }
            if (!empty($tm->pasal_id)) {
                $pasal_ids = json_decode($tm->pasal_id, true);
                if (!is_array($pasal_ids)) $pasal_ids = [$tm->pasal_id];
                $pasal_arr = [];
                foreach ($pasal_ids as $pid) {
                    $pasal_row = $this->db->get_where('requirement_details', ['id' => $pid])->row();
                    if ($pasal_row) $pasal_arr[] = $pasal_row->chapter;
                }
                $pasal_name = implode(', ', $pasal_arr);
            }
        ?>
            <tr>
                <td class="text-center"><?= $k; ?></td>
                <td><?= nl2br(htmlspecialchars($tm->description)); ?></td>
                <td class="text-center"><?= $tm->kategori; ?></td>
                <td><?= htmlspecialchars($iso_name); ?></td>
                <td><?= htmlspecialchars($pasal_name); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else : ?>
<p class="text-muted">Tidak ada temuan.</p>
<?php endif; ?>
