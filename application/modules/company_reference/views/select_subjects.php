<div class="mb-3 row flex-nowrap">
	<label for="" class="col-3 col-form-label font-weight-bold">Select Subject</label>
	<input type="hidden" name="reference_id" id="reference_id" value="<?= $reference_id?>">

	<div class="col-9">
		<select name="subject_id" id="subject_id" required class="form-control select2">
			<option value=""></option>
			<?php foreach ($subjects as $subj) : ?>
				<option value="<?= $subj->id; ?>" <?= (in_array($subj->id, array_column($axist_subjects,'subject_id'))) ? 'disabled' : ''; ?>><?= $subj->name; ?></option>
			<?php endforeach; ?>
		</select>
	</div>
</div>

<style>
	span.select2-selection.select2-selection--single.is-invalid {
		border-color: #f64e60;
		padding-right: calc(1.5em + 1.3rem);
		background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23F64E60' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23F64E60' stroke='none'/%3e%3c/svg%3e");
		background-repeat: no-repeat;
		background-position: right calc(0.375em + 0.325rem) center;
		background-size: calc(0.75em + 0.65rem) calc(0.75em + 0.65rem);
	}
</style>
<script>
	$(document).ready(function() {
		$('.select2').select2({
			placeholder: 'Choose an Option',
			width: '100%',
			allowClear: true,
		})
	})
</script>