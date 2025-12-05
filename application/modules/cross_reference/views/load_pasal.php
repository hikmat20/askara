<div class="row mb-3">
	<label for="exampleInputEmail1" class="col-2 col-form-label"></label>
	<div class="col-12">
		<table class="table table-bordered table-striped">
			<thead>
				<tr>
					<th>No</th>
					<th width="100">Pasal</th>
					<th>Desc. Indonesian</th>
					<th>Desc. English</th>
					<th class="w-25">Proses Terkait <span class="text-danger">*</span></th>
					<th class="w-25">Dokumen Lain</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				if (isset($Data_detail) && $Data_detail) :
					$n = 0;
					foreach ($Data_detail as $key => $val) : $n++; ?>
						<tr>
							<td><?= $n; ?></td>
							<td><?= $val->chapter; ?>
								<input type="hidden" name="detail[<?= $n; ?>][chapter]" value="<?= $val->id; ?>">
							</td>
							<td>
								<?= limit_text(strip_tags($val->desc_indo), 100) . ' <a href="#read" class="link view_pasal" data-id="' . $val->id . '">[read]</a>'; ?></td>
							<td>
								<?= limit_text(strip_tags($val->desc_eng), 100) . ' <a href="#read" class="link view_pasal" data-id="' . $val->id . '">[read]</a>'; ?></td>
							<td>
								<select name="detail[<?= $n; ?>][procedure][]" multiple class="form-control select2-modal" id="procedure_<?= $n; ?>">
									<option value=""></option>
									<?php if (isset($procedure) && $procedure) :
										foreach ($procedure as $k => $p) : ?>
											<option value="<?= $p->id; ?>"><?= $p->name; ?></option>
									<?php endforeach;
									endif; ?>
								</select>
							</td>
							<td>
								<input type="other_docs" name="detail[<?= $n; ?>][other_docs]" id="other_docs_<?= $n; ?>" class="form-control" placeholder="Dokumen Terkait">
							</td>
						</tr>
				<?php endforeach;
				endif; ?>
			</tbody>
		</table>
		<button type="submit" class="btn btn-primary mt-3" id="save"><i class="fa fa-save"></i>Save</button>
	</div>
</div>

<script>
	$('.select2-modal').select2({
		placeholder: "Choose an options",
		width: "100%",
		allowClear: true
	})
</script>