<style>
    body { font-family: Arial, sans-serif; font-size: 9px; }
    h2 { font-size: 14px; text-align: center; margin-bottom: 10px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #333; padding: 3px 5px; font-size: 9px; }
    th { background: #f0f0f0; text-align: center; }
    .text-center { text-align: center; }
</style>

<?php
$titles = ['sop' => 'DAFTAR INDUK SOP', 'ik' => 'DAFTAR INDUK IK', 'form' => 'DAFTAR INDUK FORM', 'ik_non_process' => 'DAFTAR INDUK IK NON PROCESS'];
$sts_labels = ['DFT'=>'Draft','REV'=>'Review','APV'=>'Approval','PUB'=>'Published','RVI'=>'Revision','COR'=>'Correction','OPN'=>'Draft'];
?>

<h2><?= isset($titles[$filter]) ? $titles[$filter] : 'DOCUMENT MASTER LIST'; ?><br><small>DOCUMENT MASTER LIST</small></h2>

<?php if ($filter == 'sop') : ?>
<table>
    <thead>
        <tr>
            <th width="20">No</th>
            <th>Department</th>
            <th>Document Number</th>
            <th>Document Name</th>
            <th width="70">Effective Date Rev. 0</th>
            <th width="50">Latest Revision</th>
            <th width="70">Effective Date Latest Rev.</th>
            <th width="50">Status</th>
            <th>Cross Reference to Pasal ISO</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $k => $v) : ?>
            <tr>
                <td class="text-center"><?= $k + 1; ?></td>
                <td><?= isset($v->department) ? $v->department : '-'; ?></td>
                <td><?= $v->nomor; ?></td>
                <td><?= $v->name; ?></td>
                <td class="text-center"><?= $v->created_at ? date('d-m-Y', strtotime($v->created_at)) : '-'; ?></td>
                <td class="text-center"><?= $v->revision ? 'Rev. ' . $v->revision : '-'; ?></td>
                <td class="text-center"><?= $v->revision_date ? date('d-m-Y', strtotime($v->revision_date)) : '-'; ?></td>
                <td class="text-center"><?= isset($sts_labels[$v->status]) ? $sts_labels[$v->status] : $v->status; ?></td>
                <td><?= str_replace('<br>', "\n", $v->cross_reference); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php elseif ($filter == 'ik_non_process') : ?>
<table>
    <thead>
        <tr>
            <th width="20">No</th>
            <th>Document Number</th>
            <th>Document Name</th>
            <th width="80">Issue Date Rev-0</th>
            <th width="50">Revision</th>
            <th width="80">Effective Date</th>
            <th width="50">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $k => $v) : ?>
            <tr>
                <td class="text-center"><?= $k + 1; ?></td>
                <td><?= $v->doc_number ?: '-'; ?></td>
                <td><?= $v->doc_name ?: '-'; ?></td>
                <td class="text-center"><?= $v->issue_date ? date('d-m-Y', strtotime($v->issue_date)) : '-'; ?></td>
                <td class="text-center"><?= $v->doc_revision_number ?: '-'; ?></td>
                <td class="text-center"><?= $v->effective_date ? date('d-m-Y', strtotime($v->effective_date)) : '-'; ?></td>
                <td class="text-center">Draft</td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php else : ?>
<table>
    <thead>
        <tr>
            <th width="20">No</th>
            <th>Department</th>
            <th>Prosedur Induk</th>
            <th>Document Number</th>
            <th>Document Name</th>
            <th width="70">Effective Date Rev. 0</th>
            <th width="50">Latest Revision</th>
            <th width="70">Effective Date Latest Rev.</th>
            <th width="50">Status</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($data as $k => $v) : ?>
            <tr>
                <td class="text-center"><?= $k + 1; ?></td>
                <td><?= isset($v->departement_name) ? $v->departement_name : '-'; ?></td>
                <td><?= isset($v->procedure_name) ? strip_tags($v->procedure_name) : '-'; ?></td>
                <td><?= isset($v->number) ? $v->number : '-'; ?></td>
                <td><?= $v->name; ?></td>
                <td class="text-center"><?= isset($v->issue_date) && $v->issue_date ? date('d-m-Y', strtotime($v->issue_date)) : '-'; ?></td>
                <td class="text-center"><?= isset($v->revision_number) && $v->revision_number !== null ? 'Rev. ' . $v->revision_number : '-'; ?></td>
                <td class="text-center"><?= isset($v->effective_date) && $v->effective_date ? date('d-m-Y', strtotime($v->effective_date)) : '-'; ?></td>
                <?php $sts = isset($v->status) ? $v->status : ''; ?>
                <td class="text-center"><?= isset($sts_labels[$sts]) ? $sts_labels[$sts] : $sts; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
