<div class="content d-flex flex-column flex-column-fluid p-0">
	<div class="d-flex flex-column-fluid justify-content-between align-items-top">
		<div class="container">
			<div class="d-flex justify-content-start align-items-center my-3">
				<a href="<?= base_url('dashboard'); ?>">
					<h4 class="text-dark font-weight-bold my-1 mr-2"><i class="fa fa-home"></i></h4>
				</a>
				<ul class="breadcrumb breadcrumb-transparent breadcrumb-dot font-weight-bold p-0 my-2 font-size-sm">
					<li class="breadcrumb-item text-muted">
						PROSEDUR, FORM, IK DAN RECORD
					</li>
				</ul>
			</div>
			<h1 class="text-white fa-3x mb-5">PROSEDUR, FORM, IK DAN RECORD</h1>
			<div class="row mb-5">
				<div class="col-md-4">
					<input type="text" name="serarch" id="search" placeholder="Pencarian" class="form-control rounded form-control-sm">
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<ul class="nav nav-warning nav-pills nav-bolder" id="myTab2" role="tablist">
						<?php $n = 0;
						foreach ($groups as $grp) : $n++; ?>
							<li class="nav-item mx-0">
								<a class="rounded-bottom-0 nav-link  <?= ($n == '1') ? 'active' : ''; ?>" id="tab_<?= $grp->id; ?>" data-toggle="tab" href="#data_<?= $grp->id; ?>">
									<span class="nav-icon ">
										<i class="fa fa-file-alt"></i>
									</span>
									<span class="text-white h5 my-0"><?= $grp->name; ?>
										<small class="">
											<div class="badge bg-white rounded-circle text-warning"><?= (isset($ArrPro[$grp->id])) ? count($ArrPro[$grp->id]) : '0'; ?></div>
										</small>
									</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>

					<div class="card rounded-top-0 border-0">
						<div class="card-body p-5">
							<div class="tab-content">
								<?php $n = 0;
								foreach ($groups as $grp) :  $n++; ?>
									<div class="tab-pane fade <?= ($n == '1') ? 'active show' : ''; ?>" id="data_<?= $grp->id; ?>" role="tabpanel" aria-labelledby="tab_<?= $grp->id; ?>">
										<?php if (isset($ArrPro[$grp->id])) : ?>
											<table class="table table-sm table-hover datatable">
												<thead>
													<tr class="">
														<th class="py-2 " width="15px">No.</th>
														<th class="py-2 px-5">File Name</th>
													</tr>
												</thead>
												<tbody>
													<?php $no = 0;
													foreach ($ArrPro[$grp->id] as $list) : $no++; ?>
														<tr class="cursor-pointer list-document text-dark h5">
															<td class="align-middle"><?= $no; ?></td>
															<td class="d-flex align-items-center px-5">
																<i class="fa fa-folder text-warning fa-2x mr-2 pt-0"></i>
																<strong class="mt-1">
																	<a class="link-action" href="<?= base_url($this->uri->segment(1) . '/procedures/' . $list['id']); ?>"><?= $list['name']; ?></a>
																</strong>
															</td>
														</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
										<?php else : ?>
											<div class="text-center my-4">
												<i>No data available</i>
											</div>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document" style="max-width:90%">
		<div class="modal-content" data-scroll="true" data-height="700">
			<div class="modal-header">
				<h5 class="modal-title">View Document</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body pt-1" id="data-file">
				File not found
			</div>
			<div class="modal-footer py-2">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<style>
	.link-action {
		color: #000;
	}

	.list-document:hover .link-action {
		color: #0bb783;
	}

	#DataTables_Table_0_filter,
	#DataTables_Table_1_filter,
	#DataTables_Table_2_filter {
		display: none;
	}
</style>
<script>
	function show(id) {
		$('#modelId').modal('show')
		$('#data-file').load(siteurl + active_controller + 'show/' + id)
	}

	$(document).ready(function() {
		$('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
			$.fn.dataTable.tables({
				visible: true,
				api: true,
				searching: false,
				lengthChange: false,
				paging: true,
				info: false,
				stateSave: true,
				fixedHeader: true,
				pageLength: 10,
				scrollCollapse: true
			}).columns.adjust();
		});

		oTable = $('.datatable').DataTable({
			layout: {
				topStart:'',
				topEnd:'',
			},
			language: {
				searchPanes: {
					i18n: {
						emptyMessage: "<i></b>No results returned</b></i>"
					}
				},
				infoEmpty: "No results returned",
				zeroRecords: "No results returned",
				emptyTable: "No results returned",
			},
			stateSave: false,
			pageLength: 20,
		})

		$(document).on('input paste', '#search', function() {
			oTable.search($(this).val()).draw();
		})
	})
</script>