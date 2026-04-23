<!-- <input type="hidden" class="form-control" name="parent_id" id="parent_id" value="<?= isset($data) ? $data->parent_id : $parent_id; ?>" placeholder="Folder Name"> -->
<div class="container">
	<form id="create-folder">
		<div class="form-group row">
			<label for="inputName" class="col-md-12 col-form-label"><?= isset($title) ? $title : 'Folder Name'; ?></label>
			<div class="col-md-12">
				<input type="hidden" name="folder" value="<?= isset($folder) ? $folder : ''; ?>">
				<input type="hidden" class="form-control" name="id" id="id" value="<?= isset($data) ? $data->id : ''; ?>" placeholder="Folder Name">
				<input type="hidden" id="parent_id" name="parent_id" class="form-control" value="<?= $parent_id; ?>" />
				<input type="text" class="form-control" name="folder_name" id="folder_name" placeholder="Folder Name" value="<?= isset($data) ? $data->name : ''; ?>">
				<span class="invalid-feedback text-danger">Nama folder harus di isi</span>
			</div>
		</div>
	</form>
</div>