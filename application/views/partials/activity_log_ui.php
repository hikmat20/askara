<?php 
$table_id = 'dt_activity_log_' . uniqid(); 

// Title logic
$doc_title = 'Activity Log';
if (isset($file)) {
    if (isset($file->name)) $doc_title .= ' - ' . $file->name;
    elseif (isset($file->nama_prosedur)) $doc_title .= ' - ' . $file->nama_prosedur;
    elseif (isset($file->title)) $doc_title .= ' - ' . $file->title;
} elseif (isset($procedure)) {
    if (isset($procedure->name)) $doc_title .= ' - ' . $procedure->name;
    elseif (isset($procedure->nama_prosedur)) $doc_title .= ' - ' . $procedure->nama_prosedur;
    elseif (isset($procedure->title)) $doc_title .= ' - ' . $procedure->title;
}

// Download permission logic
$ci =& get_instance();
$can_download = false;

if (isset($ci->auth) && method_exists($ci->auth, 'is_admin') && $ci->auth->is_admin()) {
    $can_download = true;
} else {
    if (isset($allow_download) && $allow_download) {
        $can_download = true;
    } elseif (isset($allow_download_procedure) && $allow_download_procedure) {
        $can_download = true;
    } elseif (isset($allow_download_form) && $allow_download_form) {
        $can_download = true;
    } elseif (isset($allow_download_wi) && $allow_download_wi) {
        $can_download = true;
    } else {
        // Direct DB fallback check for 'procedures'
        $group = $ci->session->userdata('group');
        $group_id = $ci->session->userdata('group_id') ?: ($group->id_group ?? ($group->group_id ?? null));
        if ($group_id) {
             $perm = $ci->db->select('group_menus.download')
                 ->from('group_menus')
                 ->join('menus', 'group_menus.menu_id = menus.id')
                 ->where('group_menus.group_id', $group_id)
                 ->where('menus.link', 'procedures')
                 ->get()->row();
             if ($perm && $perm->download == '1') {
                 $can_download = true;
             }
        }
    }
}
?>
<div class="table-responsive w-100">
    <table id="<?= $table_id; ?>" class="table table-bordered table-sm table-hover">
        <thead class="table-light">
            <tr>
                <th width="15%" class="text-center">Tanggal & Waktu</th>
                <th width="20%">Pengguna</th>
                <th width="15%" class="text-center">Status</th>
                <th width="50%">Catatan / Alasan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (isset($history) && !empty($history)) :
                foreach ($history as $his) : 
                    // Determine styling based on status
                    $status_badge = 'badge-primary';
                    if (in_array($his->new_status, ['OPN', 'PUB', 'APV'])) {
                        $status_badge = 'badge-success';
                    } elseif (in_array($his->new_status, ['RVI', 'COR', 'REV'])) {
                        $status_badge = 'badge-warning';
                    } elseif (in_array($his->new_status, ['DEL', 'REJ'])) {
                        $status_badge = 'badge-danger';
                    }
                    // Map status codes to full readable names
                    $status_map = [
                        'DFT' => 'Draft',
                        'OPN' => 'New',
                        'REV' => 'Waiting Review',
                        'COR' => 'Need Correction',
                        'APV' => 'Waiting Approval',
                        'PUB' => 'Published',
                        'RVI' => 'Revision',
                        'HLD' => 'Hold',
                        'DEL' => 'Request Deletion',
                        'REJ' => 'Rejected Deletion'
                    ];
                    
                    if (isset($sts) && isset($sts[$his->new_status])) {
                        $readable_status = strip_tags($sts[$his->new_status]);
                    } elseif (isset($status_map[$his->new_status])) {
                        $readable_status = $status_map[$his->new_status];
                    } else {
                        $readable_status = $his->new_status;
                    }
            ?>
            <tr>
                <td class="my-1 text-center align-middle"><?= date('d M Y, H:i', strtotime($his->updated_at)); ?></td>
                <td class="my-1 align-middle font-weight-bold">
                    <i class="fa fa-user-circle text-muted mr-1"></i> <?= $his->full_name; ?>
                </td>
                <td class="my-1 text-center align-middle">
                    <span class="badge <?= $status_badge; ?>"><?= $readable_status; ?></span>
                </td>
                <td class="my-1 align-middle">
                    <?php if ($his->note && $his->note != '~' && $his->note != '') : ?>
                        <?= nl2br($his->note); ?>
                    <?php else : ?>
                        <em class="text-muted">Tidak ada catatan</em>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; else: ?>
            <tr><td colspan="4" class="text-center text-muted">Belum ada riwayat aktivitas</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    // Check if DataTables is loaded, if not, wait for it or just initialize
    if ($.fn.DataTable) {
        
        // Dynamically load DataTables Buttons extension if it's not present
        if (!$.fn.dataTable.ext.buttons || !$.fn.dataTable.ext.buttons.excelHtml5) {
            let scripts = [
                "https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js",
                "https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js",
                "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js",
                "https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js",
                "https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js",
                "https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"
            ];

            let loadScripts = function(index) {
                if (index < scripts.length) {
                    $.getScript(scripts[index], function() {
                        loadScripts(index + 1);
                    });
                } else {
                    initTable();
                }
            };
            loadScripts(0);
        } else {
            initTable();
        }

        function initTable() {
            if ($.fn.DataTable.isDataTable('#<?= $table_id; ?>')) {
                $('#<?= $table_id; ?>').DataTable().destroy();
            }
            
            <?php if ($can_download): ?>
            var domConfig = "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6 text-right'B>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>";
            var buttonsConfig = [
                {
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel-o"></i> Export Excel',
                    className: 'btn btn-success btn-sm mb-3',
                    title: <?= json_encode($doc_title); ?>,
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fa fa-file-pdf-o"></i> Export PDF',
                    className: 'btn btn-danger btn-sm mb-3',
                    title: <?= json_encode($doc_title); ?>,
                    orientation: 'portrait',
                    pageSize: 'A4',
                    exportOptions: {
                        columns: [0, 1, 2, 3]
                    },
                    customize: function(doc) {
                        // Set table width to 100% and adjust column ratios
                        doc.content[1].table.widths = ['15%', '25%', '15%', '45%'];
                        doc.pageMargins = [20, 30, 20, 30]; // left, top, right, bottom
                        doc.defaultStyle.fontSize = 9; // Reduce font size slightly
                        doc.styles.tableHeader.fontSize = 10;
                        doc.styles.tableHeader.alignment = 'left';
                    }
                }
            ];
            <?php else: ?>
            var domConfig = "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>";
            var buttonsConfig = [];
            <?php endif; ?>

            $('#<?= $table_id; ?>').DataTable({
                dom: domConfig,
                buttons: buttonsConfig,
                order: [], // Let the DB query order prevail initially, or set to [[0, 'desc']]
                pageLength: 10,
                language: {
                    emptyTable: "Belum ada riwayat aktivitas",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data"
                }
            });
            
            // Adjust columns on tab shown if this table is inside a bootstrap tab
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                $('#<?= $table_id; ?>').DataTable().columns.adjust().draw();
            });
        }
    }
});
</script>
