<form id="form-check-done">
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
	<hr>
	<h4>List Checksheets</h4>
	<div class="table-responsive" style="overflow-x:auto;">
		<table class="table table-bordered" style="width:<?= $width; ?>;">
			<thead class="table-light">
				<tr>
					<th rowspan="2" class="p-2" width="50">No</th>
					<th rowspan="2" class="p-2" width="">Items</th>
					<th rowspan="2" class="p-2" width="">Standard</th>
					<th colspan="<?= $count; ?>" class="p-2 text-center" width="<?= $col_width; ?>">Result</th>
				</tr>
				<tr>
					<?php for ($i = 1; $i <= $count; $i++): ?>
						<th class="text-center"><?= $name_col . " " . $i; ?>
							<?php if ($current == $i): ?>
								<input type="hidden" name="field" value="<?= $i; ?>">
							<?php endif; ?>
						</th>

					<?php endfor; ?>
				</tr>
			</thead>
			<tbody>
				<?php $n = 0;
				if ($details)
					foreach ($details as $it):
						$n++; ?>
						<tr>
							<td>
								<?= $n; ?>
							</td>
							<td><?= $it->item_name; ?></td>
							<td><?= $it->standard_check; ?></td>
							<?php for ($i = 1; $i <= $count; $i++): ?>
								<?php $nn = "n" . $i; ?>
								<?php $Nn = "note" . $i; ?>
								<td class="<?= ($it->$nn == '') ? 'bg-light' : ''; ?>">
									<?php if ($it->check_type == 'boolean'): ?>
										<?php if ($it->$nn == 'no'): ?>
											<label for="" class="label-danger label"><?= ucfirst($it->$nn); ?></label>
											<?php if (isset($ArrNotes[$it->id]->$Nn)): ?>
												<div class="alert alert-light p-2 my-1 font-italic" role="alert">
													<?= $ArrNotes[$it->id]->$Nn; ?>
												</div>
											<?php endif; ?>
										<?php elseif ($it->$nn == 'yes'): ?>
											<label for="" class="label-success label"><?= ucfirst($it->$nn); ?></label>
										<?php endif; ?>
									<?php else: ?>
										<?= ($it->$nn) ?: ''; ?>
									<?php endif; ?>
								</td>
							<?php endfor; ?>
						</tr>
					<?php endforeach; ?>
			</tbody>
			<tfoot>
				<tr>
					<th rowspan="" class="p-1" width=""></th>
					<th rowspan="" class="p-1" width=""></th>
					<th rowspan="" class="p-1 text-right" width="">Execution By</th>
					<?php
					$day = 'day';
					$date = 'date';
					for ($i = 1; $i <= $count; $i++):
						$dayCheck = $day . $i;
						$dateCheck = $date . $i;
						?>
						<td class="text-muted p-1">
							<small for="">
								<?= isset($ArrExe[$data->id]->$dayCheck) ? $ArrUsers[$ArrExe[$data->id]->$dayCheck] . " | " : ''; ?>
							</small><small for="">
								<?= isset($ArrExeDate[$data->id]->$dateCheck) ? $ArrExeDate[$data->id]->$dateCheck : '' ?>
							</small>
						</td>
					<?php endfor; ?>
				</tr>
				<tr>
					<th rowspan="" class="p-1" width=""></th>
					<th rowspan="" class="p-1" width=""></th>
					<th rowspan="" class="p-1 text-right" width="">Checker By</th>
					<?php
					$day = 'day';
					$date = 'date';
					for ($i = 1; $i <= $count; $i++):
						$dayCheck = $day . $i;
						$dateCheck = $date . $i; ?>
						<td class="p-1">
							<?php
								if (!isset($ArrCheck[$data->id]->$dayCheck)): ?>
									<div class="" id="r_<?= $n . '_c_' . $i; ?>">
										<div class="d-flex justify-content-start align-items-center gap-4">
											<div class="form-check form-check-custom form-check-solid mr-10">
												<label class="form-check-label font-weight-bolder text-dark">
													<input class="form-check-input yes required" type="radio" value="yes"
														name="checker[n<?= $i; ?>]" data-row="<?= "checker_" . $i; ?>"
														id="boolean_checker_<?= $i; ?>">
													Yes
													<span class="invalid-feedback font-weight-normal">
														<i class="text-danger fa fa-exclamation-circle"></i>
													</span>
												</label>
											</div>
											<div class="form-check form-check-custom form-check-danger form-check-solid mr-10">
												<label class="form-check-label font-weight-bolder text-dark">
													<input class="form-check-input no required" type="radio" value="no"
														name="checker[n<?= $i; ?>]" data-row="<?= "checker_" . $i; ?>"
														id="boolean_checker_<?= $i; ?>">
													No
													<span class="invalid-feedback font-weight-normal">
														<i class="text-danger fa fa-exclamation-circle fa-md"></i>
													</span>
												</label>
											</div>
										</div>
									</div>
								<?php else: ?>
									<label for="">
										<?= isset($ArrCheck[$data->id]->$dayCheck) ? $ArrUsers[$ArrCheck[$data->id]->$dayCheck] . " | " : ''; ?>
									</label>
									<?php
									$checkVal = "check" . $i;
									if (isset($ArrCheckValue[$data->id]->$checkVal)) {
										$val = $ArrCheckValue[$data->id]->$checkVal;
										$class = ($val == 'yes') ? 'label-success' : 'label-danger';
										echo '<label class="label ' . $class . '">' . ucfirst($val) . '</label> | ';
									}
									?>
									<label for="">
										<?= isset($ArrCheckDate[$data->id]->$dateCheck) ? $ArrCheckDate[$data->id]->$dateCheck : '' ?>
									</label>
								<?php endif;
							if ($current == $i):
							else: ?>
								<label for="">
									<?= isset($ArrCheck[$data->id]->$dayCheck) ? $ArrUsers[$ArrCheck[$data->id]->$dayCheck] . " | " : ''; ?>
								</label>
								<label for="">
									<?= isset($ArrCheckDate[$data->id]->$dateCheck) ? $ArrCheckDate[$data->id]->$dateCheck : '' ?>
								</label>
							<?php endif; ?>
						</td>
					<?php endfor; ?>
				</tr>
				<tr>
					<th rowspan="" class="p-1" width=""></th>
					<th rowspan="" class="p-1" width=""></th>
					<th rowspan="" class="p-1 text-right" width="">Note</th>
					<?php
					for ($i = 1; $i <= $count; $i++): ?>
						<td class="p-1">
							<?php $dayNote = 'day' . $i; ?>
							<?php if (isset($ArrCheckNote[$data->id]->$dayNote)) {
								echo $ArrCheckNote[$data->id]->$dayNote;
							} else {
								$inputName = "checker_note[note" . $i . "]";
								echo '<div class="" id="r_' . $n . '_c_' . $i . '">
										<textarea type="text" name="' . $inputName . '" id="note_checker_' . $i . '"
											class="form-control" placeholder="Reason"></textarea>
										<span class="invalid-feedback">Can not be empty</span>
									</div>';
							} ?>
						</td>	
					<?php endfor; ?>
				</tr>
			</tfoot>
		</table>
	</div>
	<br>
	<button type="submit" id="btn-save-checker" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
</form>