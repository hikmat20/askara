<style>
    body { font-family: Arial, sans-serif; font-size: 9px; }
    h2 { font-size: 14px; text-align: center; margin-bottom: 10px; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #333; padding: 3px 5px; font-size: 9px; }
    th { background: #f0f0f0; text-align: center; }
    .text-center { text-align: center; }
</style>

<?php
$titles = ['sop' => 'DAFTAR INDUK SOP', 'ik' => 'DAFTAR INDUK IK', 'form' => 'DAFTAR INDUK FORM'];
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

<?php else : ?>
<table>
    <thead>
        <tr>
            <th width="20">No</th>
            <th>Department</th>
            <th>Document Number</th>
            <th>Prosedur Induk</th>
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
                <td><?= isset($v->department) ? $v->department : '-'; ?></td>
                <td><?= isset($v->procedure_nomor) ? $v->procedure_nomor : '-'; ?></td>
                <td><?= isset($v->procedure_name) ? $v->procedure_name : '-'; ?></td>
                <td><?= $v->name; ?></td>
                <td class="text-center"><?= isset($v->proc_created_at) && $v->proc_created_at ? date('d-m-Y', strtotime($v->proc_created_at)) : '-'; ?></td>
                <td class="text-center"><?= isset($v->revision) && $v->revision ? 'Rev. ' . $v->revision : '-'; ?></td>
                <td class="text-center"><?= isset($v->revision_date) && $v->revision_date ? date('d-m-Y', strtotime($v->revision_date)) : '-'; ?></td>
                <?php $sts = isset($v->proc_status) ? $v->proc_status : ''; ?>
                <td class="text-center"><?= isset($sts_labels[$sts]) ? $sts_labels[$sts] : $sts; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
