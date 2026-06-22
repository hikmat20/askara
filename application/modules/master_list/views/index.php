<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<div>
						<h2 class="mt-3 mb-0"><i class="<?= $icon; ?> text-primary mr-2"></i>Master List Dokumen</h2>
						<p class="text-muted mb-0">Kelola dan pantau seluruh dokumen mutu — SOP, IK, dan Form</p>
					</div>
				</div>
				<div class="card-body">

					<!-- Summary Cards -->
					<div class="row mb-4">
						<div class="col">
							<div class="border rounded p-3" style="min-height:80px;">
								<i class="fa fa-copy text-muted mb-1"></i>
								<div class="font-size-h2 font-weight-bold"><?= $count_sop['all'] + $count_ik['all'] + $count_form['all']; ?></div>
								<div class="text-muted">Semua Dokumen</div>
							</div>
						</div>
						<div class="col">
							<div class="border rounded p-3" style="min-height:80px;">
								<i class="fa fa-file-alt text-muted mb-1"></i>
								<div class="font-size-h2 font-weight-bold"><?= $count_sop['all']; ?></div>
								<div class="text-muted">SOP</div>
							</div>
						</div>
						<div class="col">
							<div class="border rounded p-3" style="min-height:80px;">
								<i class="fa fa-file-invoice text-muted mb-1"></i>
								<div class="font-size-h2 font-weight-bold"><?= $count_ik['all']; ?></div>
								<div class="text-muted">IK / WI</div>
							</div>
						</div>
						<div class="col">
							<div class="border rounded p-3" style="min-height:80px;">
								<i class="fa fa-clipboard-list text-muted mb-1"></i>
								<div class="font-size-h2 font-weight-bold"><?= $count_form['all']; ?></div>
								<div class="text-muted">Form</div>
							</div>
						</div>
						<div class="col">
							<div class="border rounded p-3" style="min-height:80px;">
								<i class="fa fa-check-circle text-success mb-1"></i>
								<div class="font-size-h2 font-weight-bold text-success"><?= $count_sop['publish']; ?></div>
								<div class="text-muted">Publish</div>
							</div>
						</div>
						<div class="col">
							<div class="border rounded p-3" style="min-height:80px;">
								<i class="fa fa-clock text-warning mb-1"></i>
								<div class="font-size-h2 font-weight-bold text-warning"><?= $count_sop['waiting']; ?></div>
								<div class="text-muted">Waiting Approve</div>
							</div>
						</div>
						<div class="col">
							<div class="border rounded p-3" style="min-height:80px;">
								<i class="fa fa-pencil-alt text-secondary mb-1"></i>
								<div class="font-size-h2 font-weight-bold text-secondary"><?= $count_sop['draft']; ?></div>
								<div class="text-muted">Draft</div>
							</div>
						</div>
					</div>

					<hr>

					<!-- Filter Dropdown + Table -->
					<div class="d-flex justify-content-between align-items-center mb-3">
						<div class="d-flex align-items-center">
							<label class="font-weight-bold mr-2 mb-0">Master List</label>
							<select id="filterSelect" class="form-control" style="width:200px;">
								<option value="" <?= $filter == '' ? 'selected' : ''; ?>>-- Pilih Master List --</option>
								<option value="sop" <?= $filter == 'sop' ? 'selected' : ''; ?>>Master List SOP</option>
								<option value="ik" <?= $filter == 'ik' ? 'selected' : ''; ?>>Master List IK</option>
								<option value="form" <?= $filter == 'form' ? 'selected' : ''; ?>>Master List Form</option>
							</select>
						</div>
						<?php if ($filter && in_array($filter, ['sop', 'ik', 'form'])) : ?>
						<div>
							<a href="<?= site_url('master_list/export_excel?filter=' . $filter); ?>" class="btn btn-sm btn-outline-success mr-1"><i class="fa fa-file-excel mr-1"></i> Export Excel</a>
							<a href="<?= site_url('master_list/print_pdf?filter=' . $filter); ?>" class="btn btn-sm btn-outline-danger" target="_blank"><i class="fa fa-file-pdf mr-1"></i> Print PDF</a>
						</div>
						<?php endif; ?>
					</div>

					<!-- Status Filter & Table (only show when filter selected) -->
					<?php if ($filter && in_array($filter, ['sop', 'ik', 'form'])) : ?>

					<!-- Data Table -->
					<?php if ($filter == 'sop') : ?>
						<h6 class="font-weight-bold">DAFTAR INDUK SOP - DOCUMENT MASTER LIST</h6>
						<table id="dtTable" class="table table-bordered table-sm table-hover">
							<thead class="table-light text-center">
								<tr>
									<th width="30">No</th>
									<th>Department</th>
									<th>Document Number</th>
									<th>Document Name</th>
									<th width="110">Effective Date Rev. 0</th>
									<th width="80">Latest Revision</th>
									<th width="110">Effective Date Latest Rev.</th>
									<th width="80">Status</th>
									<th>Cross Reference to Pasal ISO</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($data) foreach ($data as $k => $v) : $k++; ?>
									<tr>
										<td class="text-center"><?= $k; ?></td>
										<td><?= isset($v->department) ? $v->department : '-'; ?></td>
										<td><?= $v->nomor; ?></td>
										<td><?= $v->name; ?></td>
										<td class="text-center"><?= $v->created_at ? date('d-m-Y', strtotime($v->created_at)) : '-'; ?></td>
										<td class="text-center"><?= $v->revision ? 'Rev. ' . $v->revision : '-'; ?></td>
										<td class="text-center"><?= $v->revision_date ? date('d-m-Y', strtotime($v->revision_date)) : '-'; ?></td>
										<td class="text-center"><?php
											$sts_map = ['DFT'=>'<span class="label label-secondary label-inline">Draft</span>','REV'=>'<span class="label label-warning label-inline">Review</span>','APV'=>'<span class="label label-info label-inline">Approval</span>','PUB'=>'<span class="label label-success label-inline">Published</span>','RVI'=>'<span class="label label-primary label-inline">Revision</span>','HLD'=>'<span class="label label-danger label-inline">Hold</span>','DEL'=>'DEL'];
											echo isset($sts_map[$v->status]) ? $sts_map[$v->status] : $v->status;
										?></td>
										<td><?= $v->cross_reference; ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>

					<?php elseif ($filter == 'ik') : ?>
						<h6 class="font-weight-bold">DAFTAR INDUK IK - DOCUMENT MASTER LIST</h6>
						<table id="dtTable" class="table table-bordered table-sm table-hover">
							<thead class="table-light text-center">
								<tr>
									<th width="30">No</th>
									<th>Department</th>
									<th>Document Number</th>
									<th>Prosedur Induk</th>
									<th>Document Name</th>
									<th width="110">Effective Date Rev. 0</th>
									<th width="80">Latest Revision</th>
									<th width="110">Effective Date Latest Rev.</th>
									<th width="80">Status</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($data) foreach ($data as $k => $v) : $k++; ?>
									<tr>
										<td class="text-center"><?= $k; ?></td>
										<td><?= isset($v->department) ? $v->department : '-'; ?></td>
										<td><?= isset($v->procedure_nomor) ? $v->procedure_nomor : '-'; ?></td>
										<td><?= isset($v->procedure_name) ? $v->procedure_name : '-'; ?></td>
										<td><?= $v->name; ?></td>
										<td class="text-center"><?= isset($v->proc_created_at) && $v->proc_created_at ? date('d-m-Y', strtotime($v->proc_created_at)) : '-'; ?></td>
										<td class="text-center"><?= isset($v->revision) && $v->revision ? 'Rev. ' . $v->revision : '-'; ?></td>
										<td class="text-center"><?= isset($v->revision_date) && $v->revision_date ? date('d-m-Y', strtotime($v->revision_date)) : '-'; ?></td>
										<td class="text-center"><?php
											$sts = isset($v->proc_status) ? $v->proc_status : '';
											$sts_map = ['DFT'=>'<span class="label label-secondary label-inline">Draft</span>','REV'=>'<span class="label label-warning label-inline">Review</span>','COR'=>'<span class="label label-danger label-inline">Correction</span>','APV'=>'<span class="label label-info label-inline">Approval</span>','PUB'=>'<span class="label label-success label-inline">Published</span>','RVI'=>'<span class="label label-primary label-inline">Revision</span>'];
											echo isset($sts_map[$sts]) ? $sts_map[$sts] : $sts;
										?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>

					<?php else : ?>
						<h6 class="font-weight-bold">DAFTAR INDUK FORM - DOCUMENT MASTER LIST</h6>
						<table id="dtTable" class="table table-bordered table-sm table-hover">
							<thead class="table-light text-center">
								<tr>
									<th width="30">No</th>
									<th>Department</th>
									<th>Document Number</th>
									<th>Prosedur Induk</th>
									<th>Document Name</th>
									<th width="110">Effective Date Rev. 0</th>
									<th width="80">Latest Revision</th>
									<th width="110">Effective Date Latest Rev.</th>
									<th width="80">Status</th>
								</tr>
							</thead>
							<tbody>
								<?php if ($data) foreach ($data as $k => $v) : $k++; ?>
									<tr>
										<td class="text-center"><?= $k; ?></td>
										<td><?= isset($v->department) ? $v->department : '-'; ?></td>
										<td><?= isset($v->procedure_nomor) ? $v->procedure_nomor : '-'; ?></td>
										<td><?= isset($v->procedure_name) ? $v->procedure_name : '-'; ?></td>
										<td><?= $v->name; ?></td>
										<td class="text-center"><?= isset($v->proc_created_at) && $v->proc_created_at ? date('d-m-Y', strtotime($v->proc_created_at)) : '-'; ?></td>
										<td class="text-center"><?= isset($v->revision) && $v->revision ? 'Rev. ' . $v->revision : '-'; ?></td>
										<td class="text-center"><?= isset($v->revision_date) && $v->revision_date ? date('d-m-Y', strtotime($v->revision_date)) : '-'; ?></td>
										<td class="text-center"><?php
											$sts = isset($v->proc_status) ? $v->proc_status : '';
											$sts_map = ['DFT'=>'<span class="label label-secondary label-inline">Draft</span>','REV'=>'<span class="label label-warning label-inline">Review</span>','COR'=>'<span class="label label-danger label-inline">Correction</span>','APV'=>'<span class="label label-info label-inline">Approval</span>','PUB'=>'<span class="label label-success label-inline">Published</span>','RVI'=>'<span class="label label-primary label-inline">Revision</span>'];
											echo isset($sts_map[$sts]) ? $sts_map[$sts] : $sts;
										?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php endif; ?>
					<?php endif; ?>

				</div>
			</div>
		</div>
	</div>
</div>

<script>
$(document).ready(function() {
	$('#dtTable').DataTable({
		fixedHeader: true,
		processing: true,
		destroy: true
	});

	$('#filterSelect').on('change', function() {
		var filter = $(this).val();
		if (filter) {
			window.location.href = siteurl + 'master_list?filter=' + filter + '&status=all';
		} else {
			window.location.href = siteurl + 'master_list';
		}
	});
});
</script>
