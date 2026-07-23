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

<h2>Summary Temuan Audit: <?= htmlspecialchars($data->company_name); ?></h2>

<!-- Header -->
<table class="no-border" style="margin-bottom:10px;">
    <tr>
        <td width="100" class="bold">Company</td>
        <td width="150" class="bold">Badan Sertifikasi</td>
    </tr>
    <tr>
        <td><?= htmlspecialchars($data->company_name); ?></td>
        <td><?= htmlspecialchars($data->name); ?></td>
    </tr>
</table>

<!-- Rincian Per Standar -->
<h3>Rincian Temuan Audit Per Standar</h3>

<?php if (!empty($dataStd)) : ?>
    <?php foreach ($dataStd as $idx => $std) : $no = $idx + 1; ?>
        <table class="no-border" style="margin-top:12px; margin-bottom:5px;">
            <tr>
                <td width="20" class="bold"><?= $no; ?>.</td>
                <td width="100" class="bold">Standar</td>
                <td><?= htmlspecialchars($std->standard_name); ?></td>
            </tr>
        </table>

        <?php if (!empty($std->findings)) : ?>
            <table>
                <thead>
                    <tr>
                        <th width="25">No</th>
                        <th width="110">Pasal</th>
                        <th>Temuan</th>
                        <th width="55">Kategori</th>
                        <th width="80">Proses</th>
                        <th width="80">Auditee</th>
                        <th width="80">Auditor</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($std->findings as $tk => $tm) : $tk++; ?>
                        <tr>
                            <td class="text-center"><?= $tk; ?></td>
                            <td>
                                <?php if ($tm->pasal_name) : 
                                    $pasals = json_decode($tm->pasal_name);
                                    if (is_array($pasals)) {
                                        echo implode('<br>', array_map('htmlspecialchars', $pasals));
                                    } else {
                                        echo htmlspecialchars($tm->pasal_name);
                                    }
                                else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= $tm->description; ?></td>
                            <td class="text-center">
                                <?php
                                $cat_map = ['1' => 'Minor', '2' => 'Major', '3' => 'OFI'];
                                echo isset($cat_map[$tm->category]) ? $cat_map[$tm->category] : (isset($tm->kategori) ? $tm->kategori : '-');
                                ?>
                            </td>
                            <td><?= isset($tm->process) ? htmlspecialchars($tm->process) : '-'; ?></td>
                            <td>
                                <?php
                                if (!empty($tm->auditee)) {
                                    $auds = json_decode($tm->auditee, true);
                                    if (is_array($auds)) {
                                        $names = [];
                                        foreach ($auds as $a) {
                                            $c = $this->db->get_where('audit_auditor_consultant', ['id' => $a])->row();
                                            $names[] = $c ? $c->name : $a;
                                        }
                                        echo htmlspecialchars(implode(', ', $names));
                                    } else {
                                        echo htmlspecialchars($tm->auditee);
                                    }
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                if (!empty($tm->auditor)) {
                                    $auds = json_decode($tm->auditor, true);
                                    if (is_array($auds)) {
                                        $names = [];
                                        foreach ($auds as $a) {
                                            foreach($auditors as $v) {
                                                if($v->id == $a) {
                                                    $names[] = $v->name ? $v->name : '-';
                                                }
                                            }
                                        }
                                        echo htmlspecialchars(implode(', ', $names));
                                    } else {
                                        echo htmlspecialchars(isset($tm->auditor_name) ? $tm->auditor_name : $tm->auditor);
                                    }
                                } else {
                                    echo '-';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p><em>Tidak ada temuan untuk standar ini.</em></p>
        <?php endif; ?>
    <?php endforeach; ?>
<?php else : ?>
    <p><em>Tidak ada data standar.</em></p>
<?php endif; ?>
