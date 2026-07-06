<style>
    body { font-family: Arial, sans-serif; font-size: 11px; }
    h2 { font-size: 16px; margin-bottom: 10px; }
    h3 { font-size: 13px; margin: 15px 0 5px 0; }
    table { border-collapse: collapse; width: 100%; word-wrap: break-word; }
    th, td { border: 1px solid #333; padding: 4px 6px; font-size: 10px; word-wrap: break-word; overflow-wrap: break-word; vertical-align: top; }
    th { background: #f0f0f0; text-align: center; }
    .no-border td, .no-border th { border: none; padding: 2px 6px; }
    .text-center { text-align: center; }
    .bold { font-weight: bold; }
    .bg-red { background: #dc3545; color: #fff; }
    .bg-yellow { background: #ffc107; color: #fff; }
    .bg-blue { background: #17a2b8; color: #fff; }
    td p { margin: 0 0 6px 0; }
    td p:last-child { margin-bottom: 0; }
</style>

<h2>Summary Temuan Audit: <?= $program->id; ?></h2>

<!-- Header -->
<table class="no-border" style="margin-bottom:10px;">
    <tr>
        <td width="70" class="bold">Company</td>
        <td width="70" class="bold">Lead Auditor</td>
        <td width="70" class="bold">Audit Scope</td>
    </tr>
    <tr>
        <td><?= htmlspecialchars($program->company); ?></td>
        <td><?= htmlspecialchars($program->auditor_name); ?></td>
        <td><?= htmlspecialchars($program->audit_scope); ?></td>
    </tr>
</table>

<!-- Summary Total -->
<h3>Summary Temuan Audit</h3>
<table style="width:300px; margin-bottom:15px;">
    <tr class="text-center">
        <td>Major</td>
        <td class="bg-red bold"><?= $total_counts['Major']; ?></td>
        <td>Minor</td>
        <td class="bg-yellow bold"><?= $total_counts['Minor']; ?></td>
        <td>OFI</td>
        <td class="bg-blue bold"><?= $total_counts['OFI']; ?></td>
    </tr>
</table>

<!-- Rincian Per Proses -->
<h3>Rincian Temuan Audit Per Proses</h3>

<?php foreach ($schedule_data as $idx => $item) : $no = $idx + 1; $sched = $item->schedule; ?>

<table class="no-border" style="margin-top:12px; margin-bottom:5px;">
    <tr>
        <td width="20" class="bold"><?= $no; ?></td>
        <td width="100" class="bold">Proses</td>
        <td width="250"><?= !empty($sched->requirement_name) ? htmlspecialchars($sched->requirement_name) : (!empty($sched->process_name) ? strip_tags($sched->process_name) : htmlspecialchars($sched->process_name_free)); ?></td>
        <td width="70" class="bold">Auditor</td>
        <td><?= htmlspecialchars($sched->auditor_name); ?></td>
    </tr>
    <tr>
        <td></td>
        <td class="bold" style="white-space:nowrap;">Tanggal Audit</td>
        <td><?= date('d-m-Y', strtotime($sched->audit_date)); ?></td>
        <td class="bold">Auditee</td>
        <td><?= isset($item->audit->auditee_text) ? htmlspecialchars($item->audit->auditee_text) : (isset($sched->department_name) ? $sched->department_name : '-'); ?></td>
    </tr>
</table>

<?php if (empty($sched->requirement_id)) : ?>
<table style="width:300px; margin-bottom:8px;">
    <tr class="text-center">
        <td>Major</td>
        <td class="bg-red bold"><?= $item->counts['Major']; ?></td>
        <td>Minor</td>
        <td class="bg-yellow bold"><?= $item->counts['Minor']; ?></td>
        <td>OFI</td>
        <td class="bg-blue bold"><?= $item->counts['OFI']; ?></td>
    </tr>
</table>
<?php endif; ?>

<p class="bold" style="margin:5px 0 2px 0;">Strong Point</p>
<table style="margin-bottom:8px;">
    <tr>
        <td><?php
            if (!empty($item->conformity)) {
                $cf_texts = [];
                foreach ($item->conformity as $cf) { $cf_texts[] = htmlspecialchars($cf->description); }
                echo implode('<br>', $cf_texts);
            } else { echo '-'; }
        ?></td>
    </tr>
</table>

<?php if (empty($sched->requirement_id)) : ?>
<p class="bold" style="margin:5px 0 2px 0;">Temuan</p>
<?php if (!empty($item->temuan)) : ?>
<table>
    <thead>
        <tr>
            <th width="25">No</th>
            <th>Temuan</th>
            <th width="55">Kategori</th>
            <th width="100">Reference Standard</th>
            <th width="120">Pasal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($item->temuan as $tk => $tm) : $tk++; ?>
            <tr>
                <td class="text-center"><?= $tk; ?></td>
                <td><?= nl2br(htmlspecialchars($tm->description)); ?></td>
                <td class="text-center"><?= $tm->kategori; ?></td>
                <td><?= isset($tm->iso_id) && isset($std_map[$tm->iso_id]) ? htmlspecialchars($std_map[$tm->iso_id]) : '-'; ?></td>
                <td><?php
                    if (!empty($tm->pasal_id)) {
                        $pasal_ids = json_decode($tm->pasal_id, true);
                        if (!is_array($pasal_ids)) $pasal_ids = [$tm->pasal_id];
                        foreach ($pasal_ids as $pid) {
                            $pasal = $this->db->get_where('requirement_details', ['id' => $pid])->row();
                            if ($pasal) echo htmlspecialchars($pasal->chapter) . '<br>';
                        }
                    } else { echo '-'; }
                ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php else : ?>
<p><em>Tidak ada temuan.</em></p>
<?php endif; ?>
<?php else : ?>
<!-- Checklist Berdasarkan Persyaratan - khusus Audit Persyaratan -->
<?php
$req_detail_map = [];
if (!empty($item->audit_requirement_details)) {
    foreach ($item->audit_requirement_details as $d) {
        $req_detail_map[$d->requirement_detail_id] = $d;
    }
}
$has_filled = false;
if (!empty($item->requirement_details)) {
    foreach ($item->requirement_details as $rd) {
        $ex = isset($req_detail_map[$rd->id]) ? $req_detail_map[$rd->id] : null;
        if ($ex && (trim($ex->aktual) !== '' || trim($ex->temuan) !== '' || trim($ex->rekomendasi) !== '')) {
            $has_filled = true; break;
        }
    }
}
?>
<?php if ($has_filled) : ?>
<p class="bold" style="margin:5px 0 2px 0;">Checklist Berdasarkan Persyaratan</p>
<table>
    <thead>
        <tr>
            <th width="13%">Pasal</th>
            <th width="37%">Requirement (Des. Inggris)</th>
            <th width="17%">Aktual</th>
            <th width="17%">Temuan</th>
            <th width="16%">Rekomendasi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($item->requirement_details as $rd) :
            $ex = isset($req_detail_map[$rd->id]) ? $req_detail_map[$rd->id] : null;
            if (!$ex || (trim($ex->aktual) === '' && trim($ex->temuan) === '' && trim($ex->rekomendasi) === '')) continue;
        ?>
            <tr>
                <td><?= htmlspecialchars($rd->chapter); ?></td>
                <td><?= $rd->desc_eng; ?></td>
                <td><?= trim($ex->aktual) !== '' ? nl2br(htmlspecialchars($ex->aktual)) : '-'; ?></td>
                <td><?= trim($ex->temuan) !== '' ? nl2br(htmlspecialchars($ex->temuan)) : '-'; ?></td>
                <td><?= trim($ex->rekomendasi) !== '' ? nl2br(htmlspecialchars($ex->rekomendasi)) : '-'; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
<?php endif; ?>

<?php endforeach; ?>
