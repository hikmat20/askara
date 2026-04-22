<div class="content container d-flex flex-column flex-column-fluid" id="kt_content">
	<form id="form-chapter">
		<div class="card card-stretch shadow card-custom">
			<div class="card-header justify-content-between d-flex align-items-center">
				<h2 class="m-0"><i class="fa fa-plus mr-2"></i><?= $title; ?></h2>
				<a href="<?= base_url($this->uri->segment(1)); ?>" class="btn btn-danger"><i
						class="fa fa-reply"></i>Back</a>
			</div>

			<div class="card-body">
				<div class="row flex-nowrap">
					<label for="" class="col-2  h6 col-form-label font-weight-bold">Company</label>
					<div class="col-6">:
						<input type="hidden" name="id" value="<?= $Data->id; ?>">
						<input type="hidden" name="company_id" value="<?= $Data->company_id; ?>">
						<label for="" class="col-form-label font-weight-bolder h6 "><?= $Data->nm_perusahaan; ?></label>
						<!-- <select name="company_id" id="status" class="form-control select2">
											<option value=""></option>
											<?php foreach ($Companies as $comp): ?>
												<option value="<?= $comp->id_perusahaan; ?>" <?= ($comp->id_perusahaan == $Data->company_id) ? 'selected' : ''; ?>><?= $comp->nm_perusahaan; ?></option>
											<?php endforeach; ?>
										</select> -->
					</div>
				</div>

				<div class="row flex-nowrap">
					<label for="" class="col-2 h6 col-form-label font-weight-bold">Branch Company</label>
					<div class="col-6">:
						<label for="" class="col-form-label font-weight-bolder h6 "><?= $Data->branch_name; ?></label>
					</div>
				</div>


				<!-- STANDARD -->
				<hr>
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="font-weight-bolder"><i class="fa fa-list-alt text-primary" aria-hidden="true"></i> List
						Standard</h4>
				</div>

				<table id="tableStandard" class="table table-sm table-condensed table-bordered">
					<thead class="text-center ">
						<tr class="table-light">
							<th class="py-2" width="50">No</th>
							<th class="py-2 text-start">Standard Name</th>
							<th class="py-2" width="50">Action</th>
						</tr>
					</thead>
					<tbody>
						<?php if (isset($datStd)): ?>
							<?php $n = 0;
							foreach ($datStd as $std):
								$n++; ?>
								<tr>
									<td class="text-center"><?= $n; ?>
									</td>
									<td class="">
										<span class="dataIdStd d-none"><?= $std->standard_id; ?></span>
										<?= $std->name; ?>
									</td>
									<td class="text-center">
										<button type="button" class="btn btn-danger btn-icon btn-xs del-row-std"
											data-id="<?= $std->id; ?>"><i class="fa fa-trash" aria-hidden="true"></i></button>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php else: ?>
							<tr class="empty">
								<td colspan="2" class="text-center text-muted">~ No data avilable ~</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<button type="button" class="btn btn-success btn-sm mb-10" id="add_standard"><i
						class="fa fa-plus mr-2"></i>Add Standard</button>
				<hr>
				<!-- REGULATIONS -->
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h4 class="font-weight-bolder"><i class="fa fa-list-alt text-primary" aria-hidden="true"></i> List
						Regulations</h4>
					<button type="button" class="btn btn-sm btn-success" id="new-compliance"><i class="fa fa-plus"
							aria-hidden="true"></i> New Compliance</button>
				</div>

				<?php if ($subjects)
					foreach ($subjects as $k => $s):
						$k++; ?>

						<div id="accSub" role="tablist" aria-multiselectable="true">
							<div class="card mb-3 border-primary overflow-hidden" style="border-radius: 10px;">
								<div class="card-header bg-light p-4 border-0 cursor-pointer" role="tab" id="sectionDetail">
									<h5 class="mb-0 text-primary font-weight-bolder" data-toggle="collapse"
										data-parent="#accSub" href="#sub<?= $k; ?>" aria-expanded="true"
										aria-controls="sub<?= $k; ?>">
										<?= $k . ". " . $s->name; ?>
									</h5>
								</div>

								<div id="sub<?= $k; ?>" class="collapse" role="tabpanel" aria-labelledby="sectionDetail">
									<div class="card-body p-4">
										<table id="tableRegulations<?= $k; ?>"
											class="table rounded-lg boreder table-sm table-condensed table-bordered">
											<thead class="text-center ">
												<tr class="bg-light">
													<th class="py-2" width="50">No</th>
													<th class="py-2">Regulations Name</th>
													<th class="py-2" width="50">Action</th>
												</tr>
											</thead>
											<tbody>
												<?php if (isset($ArrReg[$s->id])): ?>
													<?php $n = 0;
													foreach ($ArrReg[$s->id] as $reg):
														$n++; ?>
														<tr>
															<td class="text-center"><?= $n; ?></td>
															<td class="">
																<span class="dataIdReg d-none"><?= $reg->regulation_id; ?></span>
																<?= $reg->name; ?>
															</td>
															<td class="text-center">
																<button type="button" class="btn btn-danger btn-icon btn-xs del-row-reg"
																	data-id="<?= $reg->id; ?>" data-row="<?= $k; ?>"><i
																		class="fa fa-trash" aria-hidden="true"></i></button>
															</td>
														</tr>
													<?php endforeach; ?>
												<?php else: ?>
													<tr class="empty">
														<td colspan="2" class="text-center text-muted">~ No data avilable ~</td>
													</tr>
												<?php endif; ?>
											</tbody>
										</table>
										<button type="button" class="btn btn-success btn-sm add_regulation"
											data-sub_id="<?= $s->id; ?>" data-subject="<?= $s->subject_id; ?>"
											data-row="<?= $k; ?>"><i class="fa fa-plus font-size-base "></i>Add
											Regulations</button>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
			</div>
			<div class="card-footer justify-content-between d-flex">
				<button type="submit" class="btn btn-primary min-w-100px save"><i class="fa fa-save"></i>Save</button>
				<a href="<?= base_url($this->uri->segment(1)); ?>" class="btn btn-danger"><i
						class="fa fa-reply"></i>Back</a>
			</div>
		</div>
	</form>
</div>

<!-- Modal -->
<div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"></h5>
				<button type="button" class="btn btn-link btn-xs btn-icon" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true"><i class="fa fa-times" aria-hidden="true"></i></span>
				</button>
			</div>
			<div class="modal-body">
			</div>
			<div class="modal-footer">
				<button id="modal-save" class="btn btn-primary"><i class="fas fa-save"></i> Save</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"
						aria-hidden="true"></i> Close</button>
			</div>
		</div>
	</div>
</div>

<style>
	.select2-selection--single {
		height: 100% !important;
	}

	.select2-selection__rendered {
		word-wrap: break-word !important;
		text-overflow: inherit !important;
		white-space: normal !important;
	}
</style>

<script>
	$(document).ready(function () {
		$('.select2').select2({
			placeholder: 'Choose an Option',
			width: '100%',
			allowClear: true,
		})

		$(document).on('submit', '#form-chapter', function (e) {
			e.preventDefault();
			let formdata = new FormData($(this)[0])
			let btn = $('.save')
			$.ajax({
				url: siteurl + active_controller + 'save',
				data: formdata,
				type: 'POST',
				dataType: 'JSON',
				processData: false,
				contentType: false,
				cache: false,
				beforeSend: function () {
					btn.attr('disabled', true)
					btn.html('<i class="spinner spinner-border-sm mr-4"></i>Loading...')
				},
				complete: function () {
					btn.attr('disabled', false)
					btn.html('<i class="fa fa-save"></i>Save')
				},
				success: function (result) {
					if (result.status == 1) {
						Swal.fire({
							title: 'Success!',
							icon: 'success',
							text: result.msg,
							timer: 1500
						}).then(() => {
							location.reload()
						})
						// $('#modelId').modal('hide')
						// location.href = siteurl + active_controller + 'edit/' + result.id
						// console.log(result);
					} else {
						Swal.fire({
							title: 'Warning!',
							icon: 'warning',
							text: result.msg,
							timer: 2000
						})
					}
				},
				error: function (result) {
					Swal.fire({
						title: 'Error!',
						icon: 'error',
						text: 'Server timeout, becuase error!',
						timer: 4000
					})
				}
			})
		})

		$(document).on('click', '#add_standard', function () {
			const row = $('table#tableStandard tbody tr.empty').length
			const num = $('table#tableStandard tbody tr.addStd').length + 1
			var html = `
			<tr class="addStd">
				<td class="text-center" style="vertical-align:middle;">
					<small class="fa fa-plus text-sm"></small>
				</td>
				<td>
					<select class="form-control select2 selectStd" name="standards[` + num + `][standard_id]">
						<option value=""></option>
						<?php foreach ($standards as $std): ?>
							<option value="<?= $std->id; ?>"><?= $std->name; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
				<td class="text-center" style="vertical-align:middle;">
					<button type="button" class="btn btn-danger btn-icon btn-xs del-row-std"><i class="fa fa-trash" aria-hidden="true"></i></button>
				</td>
			</tr>
			`;

			if (row == 1) {
				$('table#tableStandard tbody').html(html)
			} else {
				$('table#tableStandard tbody').append(html)
			}
			$('.select2').select2({
				placeholder: 'Choose an option',
				allowClear: true,
				width: '100%'
			})
			selectStd('.selectStd', '.dataIdStd')
		})

		$(document).on('change', '.selectStd', function () {
			selectStd('.selectStd', '.dataIdStd')
		})

		$(document).on('click', '.del-row-std', function () {
			const id = $(this).data('id')
			const btn = $(this)

			if (id != undefined && (id !== null || id !== '')) {
				Swal.fire({
					title: 'Confirmation!',
					text: 'Are you sure want to be delete this data?',
					icon: 'question',
					showCancelButton: true,
				}).then((value) => {
					if (value.isConfirmed) {
						$.ajax({
							url: siteurl + active_controller + 'delete',
							type: 'POST',
							data: {
								id
							},
							dataType: 'JSON',
							beforeSend: function () {
								btn.html(
									'<span class="spinner-border spinner-border-sm"></span>'
								).prop('disabled', true)
							},
							complete: function () {
								btn.html('<span class="fa fa-trash"></span>').prop(
									'disabled', false)
							},
							success: function (result) {
								if (result.status == 1) {
									Swal.fire('Success!', result.msg, 'success', 1500)
									btn.parents('tr').addClass('table-danger')
									btn.parents('tr').hide('slow')
									setTimeout(() => {
										btn.parents('tr').remove()
									}, 500);
								} else {
									Swal.fire('Failed!', result.msg, 'warning', 1500)
								}
							},
							error: function () {
								Swal.fire('Error!', 'Server timeout. Error!', 'error',
									1500)
							}
						})
					}
				})

			} else {
				btn.parents('tr').addClass('table-warning')
				btn.parents('tr').hide('fast')
				setTimeout(function () {
					btn.parents('tr').remove()
				}, 500);
			}
			selectStd('.selectStd', '.dataIdStd')
		})

		$(document).on('click', '.add_regulation', function () {
			const n = $(this).data('row')
			const row = $('table#tableRegulations' + n + ' tbody tr.empty').length
			const num = $('table#tableRegulations' + n + ' tbody tr.add').length + 1
			const subId = $(this).data('sub_id')
			const subject = $(this).data('subject')
			const ArrRegulation = <?= $ArrRegulation; ?>;
			var html = `
			<tr class="add">
				<td class="text-center" style="vertical-align:middle;">
					<small class="fa fa-plus text-light-dark"></small>
					<input type="hidden" name="regulations[` + num + `-` + subId + `][subject]" value="${subId}">
				</td>
				<td>
					<select class="form-control select2 selectReg" data-row="${n}" name="regulations[` + num + `-` + subId + `][regulation_id]">
						<option value=""></option>`;

			if (ArrRegulation) {
				$.each(ArrRegulation[subject], function (i, r) {
					html += `<option value="${i}">${r}</option>`;
				})
			}

			html += `</select>
				</td>
				<td class="text-center" style="vertical-align:middle;">
					<button type="button" class="btn btn-danger btn-icon btn-xs del-row-reg"><i class="fa fa-trash" aria-hidden="true"></i></button>
				</td>
			</tr>
			`;

			if (row == 1) {
				$('table#tableRegulations' + n + ' tbody').html(html)
			} else {
				$('table#tableRegulations' + n + ' tbody').append(html)
			}
			$('.select2').select2({
				placeholder: 'Choose an option',
				allowClear: true,
				width: '100%'
			})
			selectStd('table#tableRegulations' + n + ' tbody .selectReg', 'table#tableRegulations' + n + ' tbody .dataIdReg')
		})

		$(document).on('change', '.selectReg', function () {
			const n = $(this).data('row')
			selectStd('table#tableRegulations' + n + ' tbody .selectReg', 'table#tableRegulations' + n + ' tbody .dataIdReg')
		})

		$(document).on('click', '.del-row-reg', function () {
			const n = $(this).data('row')
			const id = $(this).data('id')
			const btn = $(this)

			if (id != undefined && (id !== null || id !== '')) {
				Swal.fire({
					title: 'Confirmation!',
					text: 'Are you sure want to be delete this data?',
					icon: 'question',
					showCancelButton: true,
				}).then((value) => {
					if (value.isConfirmed) {
						$.ajax({
							url: siteurl + active_controller + 'delete_reg',
							type: 'POST',
							data: {
								id
							},
							dataType: 'JSON',
							beforeSend: function () {
								btn.html(
									'<span class="spinner-border spinner-border-sm"></span>'
								).prop('disabled', true)
							},
							complete: function () {
								btn.html('<span class="fa fa-trash"></span>').prop(
									'disabled', false)
							},
							success: function (result) {
								if (result.status == 1) {
									Swal.fire('Success!', result.msg, 'success', 1500)
									btn.parents('tr').addClass('table-danger')
									btn.parents('tr').hide('slow')
									setTimeout(() => {
										btn.parents('tr').remove()
									}, 500);
								} else {
									Swal.fire('Failed!', result.msg, 'warning', 1500)
								}
							},
							error: function () {
								Swal.fire('Error!', 'Server timeout. Error!', 'error',
									1500)
							}
						})
					}
				})

			} else {
				btn.parents('tr').addClass('table-warning')
				btn.parents('tr').hide('fast')
				setTimeout(function () {
					btn.parents('tr').remove()
				}, 500);
			}

			selectStd('table#tableRegulations' + n + ' tbody .selectReg', 'table#tableRegulations' + n + ' tbody .dataIdReg')
		})

		/* New Compliance */
		$(document).on('click', '#new-compliance', function () {
			const branch = "<?= ($this->uri->segment(4)) ?: null; ?>";
			const reference_id = "<?= $Data->id ?>";
			$('#modelId .modal-body').html('')
			$('#modelId .modal-title').html('Select Subject Regulation')
			$('#modelId .modal-body').load(siteurl + active_controller + 'select_subjects/' + branch + "/" + reference_id)
			$('#modelId').modal('show')
		})

		$(document).on('click', '#modal-save', function () {
			const subject_id = $('#modelId .modal-body #subject_id').val()
			const reference_id = $('#modelId .modal-body #reference_id').val()
			const btn = $(this)

			if (!subject_id) {
				$('#modelId .modal-body #subject_id').next('.select2-container').find('.select2-selection').addClass('is-invalid')
				return
			}

			btn.prop('disabled', true).html('<i class="spinner spinner-border-sm mr-2"></i> Loading...')

			$.ajax({
				url: siteurl + active_controller + 'save_subject',
				type: 'POST',
				dataType: 'JSON',
				data: { subject_id, reference_id },
				success: function (result) {
					if (result.status == 1) {
						Swal.fire({ title: 'Success!', icon: 'success', text: result.msg, timer: 2000 }).then(() => {
							$('#modelId').modal('hide')
							location.reload()
						})
					} else {
						Swal.fire({ title: 'Warning!', icon: 'warning', text: result.msg, timer: 2000 })
					}
				},
				error: function () {
					Swal.fire({ title: 'Error!', icon: 'error', text: 'Server timeout. Please try again.', timer: 3000 })
				},
				complete: function () {
					btn.prop('disabled', false).html('<i class="fas fa-save"></i> Save')
				}
			})
		})
	})

	function selectStd(s, d) {
		const selectedValue = [];
		$(s)
			.find(':selected')
			.filter(function (idx, el) {
				return $(el).attr('value');
			})
			.each(function (idx, el) {
				selectedValue.push($(el).attr('value'));
			});
		$(d).each(function (idx, el) {
			selectedValue.push($(el).text());
		});

		$(s)
			.find('option')
			.each(function (idx, option) {
				if (selectedValue.indexOf($(option).attr('value')) > -1) {
					if ($(option).is(':checked')) {
						return;
					} else {
						$(this).attr('disabled', true);
					}
				} else {
					$(this).attr('disabled', false);
				}
			});
	}
</script>