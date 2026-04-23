<?php $exec = $data->frequency_execution; ?>
<div class="content d-flex flex-column flex-column-fluid">
	<div class="d-flex flex-column-fluid justify-content-between align-items-top">
		<div class="container">
			<div class="card">
				<div class="card-header">
					<h2 class="">New Checksheet</h2>
				</div>
				<div class="card-body overflow-auto">
					<form id="form-checksheet" enctype="multipart/form-data">
						<div class="row mb-3">
							<label for="" class="col-md-2 control-label">Checksheet Name</label>
							<div class="col-md-4">:
								<input type="hidden" name="id" value="<?= $data->id; ?>">
								<label for=""><?= $data->checksheet_name; ?></label>
							</div>
						</div>
						<div class="row mb-3">
							<label for="" class="col-md-2 control-label">Frequency Execution</label>
							<div class="col-md-4">:
								<label for=""><?= $fExecution[$data->frequency_execution]; ?></label>
							</div>
						</div>
						<div class="row mb-3">
							<label for="" class="col-md-2 control-label">Periode</label>
							<div class="col-md-4">:
								<label><?= date_format(date_create($data->periode), 'M, Y'); ?></label>
							</div>
						</div>
						<div class="row mb-3">
							<label for="" class="col-md-2 control-label">Checksheet Name</label>
							<div class="col-md-4">:
								<label for=""><?= $data->checksheet_name; ?></label>
							</div>
						</div>
						<div class="row mb-3">
							<label for="" class="col-md-2 control-label">Frequency Checking</label>
							<div class="col-md-4">:
								<label for=""><?= $fChecking[$data->frequency_checking]; ?></label>
							</div>
						</div>
						<?php if ($weekOfMonth) : ?>
							<div class="row mb-3">
								<label for="" class="col-md-2 control-label">Week</label>
								<div class="col-md-4">:
									<label for=""><?= $weekOfMonth; ?></label>
								</div>
							</div>
						<?php endif; ?>
						<hr>
						<h5>List Checksheets</h5>
						<div class="table-responsive" style="overflow-x:auto;">
							<table class="table table-bordered">
								<thead class="table-light">
									<tr>
										<th rowspan="2" class="p-2" width="50">No</th>
										<th rowspan="2" class="p-2" width="">Items</th>
										<th rowspan="2" class="p-2" width="">Standard</th>
										<th colspan="<?= $count; ?>" class="p-2 text-center" width="<?= $col_width; ?>">Result</th>
									</tr>
									<tr>
										<?php for ($i = 1; $i <= $count; $i++) {
											if($current == $i) {

											
											$weekend = "";
											$tanggalkolom = date("Y-m", strtotime($data->periode)) . "-" . $i;
											if (date('w', strtotime($tanggalkolom)) % 6 == 0) $weekend = "table-danger";
											if ($data->frequency_checking != 1) $weekend = "";
										?>
											<th class="text-center <?= $weekend ?> <?= $i < (date('d')) ? 'ds-none' : ''; ?>  <?= ($weekOfMonth) && ($weekOfMonth == $i) ? 'bg-light-warning' : (($exec == 3 && $i == date('d')) ? 'bg-light-warning' : (($exec == 5 && $i == date('m')) ? 'bg-light-warning' : '')); ?>"><?= ($exec != 5) ? $name_col . " " . $i : $name_col[$i]; ?></th>
										<?php } ?>
										<?php } ?>
									</tr>
								</thead>
								<tbody>
									<?php $n = 0;
									if ($details) foreach ($details as $it) : $n++; ?>
										<tr>
											<td>
												<?= $n; ?>
											</td>
											<td><?= $it->item_name; ?></td>
											<td><?= $it->standard_check; ?></td>
											<?php for ($i = 1; $i <= $count; $i++) :
												if($current == $i) {
													
												$weekend = "";
												$tanggalkolom = date("Y-m", strtotime($data->periode)) . "-" . $i;
												if (date('w', strtotime($tanggalkolom)) % 6 == 0) $weekend = "table-danger";
												if ($data->frequency_checking != 1) $weekend = "";
											?>
												<?php $nn = "n" . $i; ?>
												<?php $Nn = "note" . $i; ?>
												<?php $NBukti = "bukti_" . $i; ?>
												<input type="hidden" name="detail[<?= $n . "_" . $i; ?>][id]" value="<?= $it->id; ?>" <?= ($weekOfMonth) ? (($weekOfMonth != $i) ? 'disabled' : '') : ($exec == 3 && ($i != date('d')) ? 'disabled' : (($exec == 5) && ($i != (date('m'))) ? 'disabled' : '')); ?>>
												<input type="hidden" name="detail[<?= $n . "_" . $i; ?>][field]" value="<?= $i; ?>" <?= ($weekOfMonth) ? (($weekOfMonth != $i) ? 'disabled' : '') : ($exec == 3 && ($i != date('d')) ? 'disabled' : (($exec == 5) && ($i != (date('m'))) ? 'disabled' : '')); ?>>
												<td class="<?= $weekend ?> <?= ($weekOfMonth) && ($weekOfMonth == $i) ? 'bg-light-warning' : (($exec == 3 && $i == date('d')) ? 'bg-light-warning' : (($exec == 5 && $i == date('m')) ? 'bg-light-warning' : '')); ?>">
													<br>
													<?php if ($it->check_type == 'boolean') : ?>
														<div class="" id="r_<?= $n . '_c_' . $i; ?>">
															<div class="d-flex justify-content-start align-items-center gap-4">
																<div class="form-check form-check-custom form-check-solid mr-10">
																	<label class="form-check-label font-weight-bolder text-dark">
																		<input class="form-check-input yes required" type="radio" value="yes" <?= ($weekOfMonth) ? (($weekOfMonth != $i) ? 'disabled' : '') : ($exec == 3 && ($i != date('d')) ? 'disabled' : (($exec == 5) && ($i != (date('m'))) ? 'disabled' : '')); ?> name="detail[<?= $n . "_" . $i; ?>][n<?= $i; ?>]" data-row="<?= $n . $i; ?>" id="boolean_<?= $i . $n; ?>" <?= ($it->$nn == 'yes') ? 'checked' : ''; ?> >
																		Yes
																		<span class="invalid-feedback font-weight-normal">
																			<i class="text-danger fa fa-exclamation-circle"></i>
																		</span>
																	</label>
																</div>
																<div class="form-check form-check-custom form-check-danger form-check-solid mr-10">
																	<label class="form-check-label font-weight-bolder text">
																		<input class="form-check-input no required" type="radio" value="no" <?= ($weekOfMonth) ? (($weekOfMonth != $i) ? 'disabled' : '') : ($exec == 3 && ($i != date('d')) ? 'disabled' : (($exec == 5) && ($i != (date('m'))) ? 'disabled' : '')); ?> name="detail[<?= $n . "_" . $i; ?>][n<?= $i; ?>]" data-row="<?= $n . $i; ?>" id="boolean_<?= $i . $n; ?>" <?= ($it->$nn == 'no') ? 'checked' : ''; ?>>
																		No
																		<span class="invalid-feedback font-weight-normal">
																			<i class="text-danger fa fa-exclamation-circle fa-md"></i>
																		</span>
																	</label>
																</div>
															</div>
															<textarea type="text" name="detail[<?= $n . "_" . $i; ?>][note<?= $i; ?>]" id="note<?= $n . $i; ?>" <?= ($weekOfMonth) ? (($weekOfMonth != $i) ? 'disabled' : ((!$it->$nn || $it->$nn == 'yes') ? 'disabled' : '')) : ($i != (date('d')) ? 'disabled' : ((!$it->$nn || $it->$nn == 'yes') ? 'disabled' : '')); ?> class="form-control <?= $i == (date('d')) ? 'required' : ''; ?>" placeholder="Reason"><?= isset($ArrNote[$it->id]) ? $ArrNote[$it->id]->$Nn : ''; ?></textarea>
															<br>
															<span style="font-weight: bold;">Bukti</span>
															<input type="file" name="bukti<?= $n.$i ?>" class="form-control">
															<?php 
															if(isset($ArrNote[$it->id]->$NBukti)){
																if($ArrNote[$it->id]->$NBukti !== '' && file_exists($ArrNote[$it->id]->$NBukti)) {
																	echo '<br>';
																	echo '<a href="'.base_url($ArrNote[$it->id]->$NBukti).'" class="btn btn-sm btn-primary" target="_blank"><i class="fa fa-file"></i> Document</a>';
																}
															}
															?>
															<span class="invalid-feedback">Can not be empty</span>
														</div>
													<?php else : ?>
														<textarea name="detail[<?= $n . "_" . $i; ?>][n<?= $i; ?>]" id="r_<?= $n . '_c_' . $i; ?>" <?= ($weekOfMonth) ? (($weekOfMonth != $i) ? 'disabled' : '') : ($exec == 3 && $i != (date('d')) ? 'disabled' : ($exec == 5 && $i != (date('m')) ? 'disabled' : '')); ?> class="form-control <?= $i == (date('d')) ? 'required' : ''; ?>" placeholder="Result"><?= ($it->$nn) ?: ''; ?></textarea>
														<span class="invalid-feedback">Can not be empty</span>
													<?php endif; ?>
												</td> <?php
												}
											 endfor; ?>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
						<hr>
						<div class="text-right">
							<button type="submit" class="btn btn-primary" id="save"><i class="fa fa-save"></i> Save</button>
							<a href="<?= base_url($this->uri->segment(1) . '/?p=' . $data->process_id . '&sub=' . $data->sub_id . '&checksheet=' . $data->dir_id); ?>" class="btn btn-danger"><i class="fa fa-reply"></i> Back</a>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('.select2').select2({
			width: '100%',
			allowClear: true,
			placeholder: 'Choose an options'
		})

		// $('.datepicker').MonthPicker('option', 'AltField': '#OtherField');

		$('.datepicker').MonthPicker({
			ShowIcon: false,
			MonthFormat: 'MM, yy',
			Button: false,
			MinMonth: "0",
			StartYear: 2023
			// Position: {
			// 	collision: 'fit flip'
			// }
		});

		$('input[type=month]').MonthPicker().css('backgroundColor', 'lightyellow');

		$('.datatable').DataTable()

		$(document).on('submit', '#form-checksheet', function(e) {
			e.preventDefault();

			var valid = 0;
			var validText = 0;
			// for (let r = 1; r <= count ; r++) {
			// 	validText
			// 	for (let i = 1; i <= count; i++) {
			// 		if ($('input[name="results[' + r + '][n' + i + ']"]').is(':checked') == false) {
			// 			// console.log(r + '-n' + i + ' not checked')
			// 			$('div#r_' + r + "_c_" + i).addClass('is-invalid')
			// 			valid++
			// 		} else {
			// 			$('div#r_' + r + "_c_" + i).removeClass('is-invalid')
			// 			valid--;
			// 		}

			// 		if ($('textarea[name="results[' + r + '][n' + i + ']"]').val() == '') {
			// 			console.log($(this).val())
			// 			$('textarea#r_' + r + "_c_" + i).addClass('is-invalid')
			// 			validText++
			// 		} else {
			// 			$('textarea#r_' + r + "_c_" + i).removeClass('is-invalid')
			// 			validText - 1;
			// 		}
			// 	}
			// }
			console.log(valid);
			console.log(validText);
			console.log(valid + validText);
			// if ((valid + validText) != 0) {
			// 	return false
			// }

			const formdata = new FormData($('#form-checksheet')[0])
			// var formdata = $('#form-checksheet').serialize();

			const isValid = getValidation('#form-checksheet')

			if (isValid == true) {
				Swal.fire({
					title: 'Confirm!',
					text: 'Are you sure you want to save this checkseet?',
					icon: 'question',
					showCancelButton: true,
				}).then((value) => {
					if (value.isConfirmed) {
						$.ajax({
							url: siteurl + active_controller + 'save_process_checksheet',
							dataType: 'JSON',
							type: 'POST',
							data: formdata,
							contentType: false,
							processData: false,
							cache: false,
							success: function(result) {
								if (result.status == 1) {
									Swal.fire({
										title: "Success!",
										text: result.msg,
										icon: "success",
										timer: 3000
									}).then(function() {
										window.location.href = siteurl + active_controller + '?p=' + <?= $data->process_id; ?> + '&sub=' + <?= $data->sub_id; ?> + '&checksheet=' + <?= $data->dir_id; ?>
									})
								} else {
									Swal.fire({
										title: "Warning!",
										text: result.msg,
										icon: "warning",
										timer: 3000
									})
								}
							},
							error: function(result) {
								Swal.fire({
									title: "Error!",
									text: "Server time out.",
									icon: "error",
									timer: 3000
								})

							}
						})
					}
				})
			}
		})

		$(document).on('change', '.no', function() {
			const row = $(this).data('row')
			$('#note' + row).val('').prop('disabled', false)
		})

		$(document).on('change', '.yes', function() {
			const row = $(this).data('row')
			$('#note' + row).val('').prop('disabled', true)
		})

	})
</script>