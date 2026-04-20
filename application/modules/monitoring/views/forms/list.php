<div class="content d-flex flex-column flex-column-fluid p-0">
    <div class="container mt-3">
        <div class="mb-5">
            <div style="font-size: 36px;" class="text-white font-weight-bolder">
                <a style="font-size: 30px;" href="<?= base_url($this->uri->segment(1)); ?>" class="text-danger"
                    title="Kembali ke Monitoring">
                    <span class="fa fa-arrow-circle-left"></span>
                </a>
                <?= $title; ?>
            </div>
        </div>
        <div class="card">
            <div class="pt-1 px-3 card-body">
                <table class="table table-hover datatable">
                    <thead>
                        <tr class="table-light">
                            <th width="40px">No</th>
                            <th>Nomor</th>
                            <th>Nama</th>
                            <th>Departemen</th>
                            <th>PIC Reviewer</th>
                            <th>PIC Approver</th>
                            <th class="text-center" width="150px">Status</th>
                            <th class="text-center" width="100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($forms)) : ?>
                            <?php $n = 0; foreach ($forms as $form) : $n++; ?>
                                <tr>
                                    <td class="text-center" style="vertical-align: middle;"><?= $n; ?></td>
                                    <td style="vertical-align: middle;"><?= htmlspecialchars($form->nomor ?? '-'); ?></td>
                                    <td class="font-weight-bolder h6" style="vertical-align: middle;">
                                        <?= htmlspecialchars($form->name ?? '-'); ?>
                                    </td>
                                    <td style="vertical-align: middle;"><?= htmlspecialchars($form->departement_name ?? '-'); ?></td>
                                    <td style="vertical-align: middle;"><?= htmlspecialchars($form->reviewer_position_name ?? '-'); ?></td>
                                    <td style="vertical-align: middle;"><?= htmlspecialchars($form->approver_position_name ?? '-'); ?></td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <?= isset($sts[$form->status]) ? $sts[$form->status] : htmlspecialchars($form->status); ?>
                                    </td>
                                    <td class="text-center" style="vertical-align: middle;">
                                        <?php if ($form->status === 'REV') : ?>
                                            <a href="<?= base_url($this->uri->segment(1) . '/load_form_review_form/' . $form->id); ?>" class="btn btn-sm btn-light-warning">
                                                <i class="fa fa-edit"></i> Review
                                            </a>
                                        <?php elseif ($form->status === 'COR') : ?>
                                            <a href="<?= base_url('/forms/edit/' . $form->id); ?>" class="btn btn-sm btn-light-danger">
                                                <i class="fa fa-wrench"></i> Correction
                                            </a>
                                        <?php elseif ($form->status === 'APV') : ?>
                                            <a href="<?= base_url($this->uri->segment(1) . '/load_form_approval_form/' . $form->id); ?>" class="btn btn-sm btn-light-info">
                                                <i class="fa fa-check"></i> Approval
                                            </a>
                                        <?php else : ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada data</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
