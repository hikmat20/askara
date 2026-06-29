<div class="container">
	<form id="move-item-form">
		<input type="hidden" name="id" id="move_item_id" value="<?= $data->id; ?>">
		<div class="form-group row">
			<label class="col-md-12 col-form-label font-weight-bold">Item to Move:</label>
			<div class="col-md-12">
				<input type="text" class="form-control" value="<?= $data->name; ?>" readonly>
			</div>
		</div>
		<div class="form-group row">
			<label for="destination_id" class="col-md-12 col-form-label font-weight-bold">Select Destination Folder:</label>
			<div class="col-md-12">
				<select name="destination_id" id="destination_id" class="form-control select2">
					<?php foreach ($folders as $f) : ?>
						<option value="<?= $f->id; ?>" <?= ($f->id == $data->parent_id) ? 'selected' : ''; ?>>
							<?= $f->name_hierarchical; ?>
						</option>
					<?php endforeach; ?>
				</select>
				<span class="invalid-feedback text-danger">Please choose a destination folder</span>
			</div>
		</div>
	</form>
</div>

<script>
	$(document).ready(function() {
		$('.select2').select2({
			placeholder: 'Choose destination folder',
			width: '100%',
			dropdownParent: $('#move-modal')
		});
	});
</script>
