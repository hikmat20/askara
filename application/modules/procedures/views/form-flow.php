<input type="hidden" name="procedure_id" value="<?= $procedure_id; ?>">
<div class="form-group">
	<label class="">Nomor <span class="text-danger">*</span></label>
	<div class="">
		<input type="hidden" name="flow[id]" class="form-control" value="<?= ($flow) ? $flow->id : ''; ?>">
		<input type="text" name="flow[number]" id="number" class="form-control" value="<?= ($flow) ? $flow->number : ''; ?>" required placeholder="Nomor" aria-describedby="helpId">
		<small class="text-danger invalid-feedback">Nomor</small>
	</div>
</div>
<div class="form-group">
	<label class="">PIC <span class="text-danger">*</span></label>
	<div class="">
		<input type="text" name="flow[pic]" id="pic" class="form-control" value="<?= ($flow) ? $flow->pic : ''; ?>" required placeholder="PIC" aria-describedby="helpId">
		<small class="text-danger invalid-feedback">PIC</small>
	</div>
</div>

<ul class="nav nav-tabs border-0" id="myTab" role="tablist">
	<li class="nav-item" role="presentation">
		<button class="nav-link active p-3" id="flow-local-tab" data-toggle="tab" data-target="#flow-local" type="button" role="tab" aria-controls="local" aria-selected="true">Indonesia</button>
	</li>
	<?php if ($language) foreach ($language as $lang): ?>
		<li class="nav-item" role="presentation">
			<button class="nav-link p-3" id="flow-<?= $lang; ?>-tab" data-toggle="tab" data-target="#flow-<?= $lang; ?>" type="button" role="tab" aria-controls="<?= $lang; ?>" aria-selected="false"><?= ucfirst($lang); ?></button>
		</li>
	<?php endforeach; ?>
</ul>

<div class="tab-content rounded-bottom rounded-bottom-xl border">
	<div class="tab-pane fade show active p-3" id="flow-local" role="tabpanel" aria-labelledby="local-tab">
		<div class="form-group">
			<label for="description" class="">Deskripsi <span class="text-danger">*</span></label>
			<div class="">
				<textarea rows="5" name="flow[description]" id="description" class="form-control summernote" placeholder="Deskripsi" aria-describedby="helpId"><?= ($flow) ? $flow->description : ''; ?></textarea>
				<small class="text-danger invalid-feedback">Deskripsi</small>
			</div>
		</div>
	</div>
	<?php if ($language) foreach ($language as $lang): ?>
		<div class="tab-pane fade p-3" id="flow-<?= $lang; ?>" role="tabpanel" aria-labelledby="flow-<?= $lang; ?>-tab">
			<div class="form-group">
				<label for="description_2" class="">Description <span class="text-danger">*</span></label>
				<div class="">
					<textarea rows="5" name="flow[description_2]" id="description_2" class="form-control summernote" placeholder="Deskripsi" aria-describedby="helpId"><?= ($flow) ? $flow->description_2 : ''; ?></textarea>
					<small class="text-danger invalid-feedback">Deskripsi</small>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>

<div class="form-group">
	<label class="">Dok. Terkait</label>
	<h5 class="">Form</h5>
	<div class="mb-3">
		<select multiple name="flow[relate_doc][]" class="select2 form-control">
			<?php $relDocs = json_decode($flow->relate_doc); ?>
			<?php if ($forms) : ?>
				<?php foreach ($forms as $form) : ?>
					<option value="<?= $form->id; ?>" <?= ($relDocs) ? (in_array($form->id, $relDocs) ? 'selected' : '') : ''; ?>><?= $form->name; ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
		<small class="text-danger invalid-feedback">Dokumen terkait</small>
	</div>

	<h5 class="">IK</h5>
	<div class="mb-3">
		<!-- <textarea rows="5" name="flow[relate_doc]" id="relate_doc" class="form-control" required placeholder="Dokumen terkait" aria-describedby="helpId" /></textarea> -->
		<select multiple name="flow[relate_ik_doc][]" class="select2 form-control">
			<?php $relDocs = json_decode($flow->relate_ik_doc, true); ?>
			<?php if ($guides) : ?>
				<?php foreach ($guides as $guide) : ?>
					<option value="<?= $guide->id; ?>" <?= ($relDocs) ? (in_array($guide->id, $relDocs)) : ''; ?>><?= $guide->name; ?></option>
				<?php endforeach; ?>
			<?php endif; ?>
		</select>
		<small class="text-danger invalid-feedback">Dokumen IK terkait</small>
	</div>

</div>
<div class="text-center">
	<button type="submit" class="btn btn-sm btn-primary min-w-100px save"><i class="fas fa-save"></i>Save</button>
</div>

<script>
	$('.select2').select2({
		placeholder: 'Choose an options',
		width: '100%',
		allowClear: true
	})

	// if (!tinymce.get('editor')) {
	// 	TinyManager.init('.editor');
	// }


	var btnExample = function(context) {
		var ui = $.summernote.ui;

		// create button
		var button = ui.button({
			contents: '<i class="fa fa-child"/> Hello',
			tooltip: 'hello',
			click: function() {
				// invoke insertText method with 'hello' on editor module.
				context.invoke('editor.insertText', 'hello');
			}
		});

		return button.render(); // return button as jquery object
	}





	$('.summernote').summernote({
		height: 250, // set editor height
		minHeight: null, // set minimum height of editor
		maxHeight: null,
		inheritPlaceholder: true,
		toolbar: [
			// ['style', ['style']],
			['edit', ['undo', 'redo']],
			['font', ['bold', 'italic', 'underline', 'clear']],
			//  ['fontname', ['fontname']],
			['fontsize', ['fontsize', 'height']],
			['color', ['color']],
			['para', ['ul', 'ol', 'paragraph', 'alphaList']],
			// ['table', ['table']],
			// ['insert', ['link', 'picture', 'video']],
			['view', ['fullscreen', 'codeview', 'help']],
		],
		buttons: {
			alphaList: function(context) {
				var ui = $.summernote.ui;
				var button = ui.button({
					contents: '<i class="note-icon-orderedlist"></i>', // Use an appropriate icon
					tooltip: 'Ordered List (Alpha)',
					click: function() {
						// Insert an ordered list (defaults to numbers)
						context.invoke('editor.insertOrderedList');
						// Find the newly created OL and set its style
						var ol = context.layoutInfo.editable.find('ol');
						if (ol.length > 0) {
							ol.css('list-style-type', 'lower-alpha');
						}
					}
				});
				return button.render();
			}
		}
	})
</script>