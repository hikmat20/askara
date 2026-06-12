<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<h2 class="mt-5"><i class="<?= $icon; ?> text-primary mr-2"></i><?= $title; ?></h2>
					<div class="mt-4 float-right">
						<a href="<?= site_url('audit_preparation/create'); ?>" class="btn btn-primary" title="New Audit Program">
							<i class="fa fa-plus mr-1"></i>New Audit Program
						</a>
					</div>
				</div>
				<div class="card-body">
					<div class="tab-content mt-3">
						<div class="tab-pane fade active show" id="Published" role="tabpanel" aria-labelledby="Published-tab">
							<table id="table-programs" class="table table-bordered table-sm table-condensed table-hover">
								<thead class="text-center table-light">
									<tr class="text-center">
										<th width="10">No.</th>
										<th width="100">ID</th>
										<th class="text-left">Company</th>
										<th>Lead Auditor</th>
										<th width="120">Audit Scope</th>
										<th width="120">Created Date</th>
										<th width="120" class="text-center">Action</th>
									</tr>
								</thead>
								<tbody>
									<?php if ($programs) foreach ($programs as $k => $v) : $k++; ?>
										<tr>
											<td class="text-center"><?= $k; ?></td>
											<td><?= $v->id; ?></td>
											<td><?= $v->company; ?></td>
											<td><?= $v->auditor_name; ?></td>
											<td class="text-center"><?= $v->audit_scope; ?></td>
											<td class="text-center"><?= date('d-m-Y', strtotime($v->created_at)); ?></td>
											<td class="text-center">
												<a href="<?= site_url('audit_preparation/edit/' . $v->id); ?>" class="btn btn-xs btn-icon btn-primary" title="Edit"><i class="fa fa-edit" aria-hidden="true"></i></a>
												<a href="<?= site_url('audit_preparation/view/' . $v->id); ?>" class="btn btn-xs btn-icon btn-info" title="View"><i class="fa fa-eye" aria-hidden="true"></i></a>
												<button type="button" class="btn btn-xs btn-icon btn-danger delete" data-id="<?= $v->id; ?>" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></button>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('#table-programs').DataTable({
			fixedHeader: true,
			processing: true,
			destroy: true
		});

		$(document).on('click', '.delete', function(e) {
			const id = $(this).data('id');
			Swal.fire({
				title: 'Hapus Audit Program?',
				icon: 'question',
				text: 'Data akan di-nonaktifkan. Apakah Anda yakin?',
				showCancelButton: true,
				confirmButtonText: 'Ya, Hapus',
				cancelButtonText: 'Batal'
			}).then((result) => {
				if (result.isConfirmed) {
					$.post(siteurl + 'audit_preparation/delete', {id: id}, function(res) {
						if (res.status == 1) {
							Swal.fire({
								title: 'Success!',
								icon: 'success',
								text: res.msg,
								timer: 2000
							}).then(function() {
								location.reload();
							});
						} else {
							Swal.fire({
								title: 'Warning!',
								icon: 'warning',
								text: res.msg,
								timer: 3000
							});
						}
					}, 'json').fail(function() {
						Swal.fire({
							title: 'Error!',
							icon: 'error',
							text: 'Server timeout, because error!',
							timer: 4000
						});
					});
				}
			});
		});
	});
</script>
