<?php
$isEdit = isset($data) && $data;
$useCustomLink = $isEdit && empty($link_in_menu);
$selectedMenuLink = ($isEdit && !empty($link_in_menu)) ? $stored_link : '';
?>
<form id="form-card" enctype="multipart/form-data">
	<?php if ($isEdit) : ?>
		<input type="hidden" name="id" value="<?= $data->id; ?>">
		<input type="hidden" name="old_picture" value="<?= htmlspecialchars($data->picture); ?>">
	<?php endif; ?>

	<div class="mb-3 row">
		<label class="col-3 col-form-label font-weight-bold">Nama Card</label>
		<div class="col-9">
			<input type="text" name="name" id="name" class="form-control" placeholder="Contoh: PROSEDUR, FORM, IK DAN RECORD"
				value="<?= $isEdit ? htmlspecialchars($data->name) : ''; ?>" required>
			<span class="invalid-feedback">Nama wajib diisi</span>
		</div>
	</div>

	<div class="mb-3 row">
		<label class="col-3 col-form-label font-weight-bold">Link (Menu)</label>
		<div class="col-9">
			<select name="link_menu" id="link_menu" class="form-control select2-link-menu" required
				data-placeholder="Cari atau pilih menu...">
				<option value="">-- Pilih Menu --</option>
				<?php if (!empty($menu_options)) :
					foreach ($menu_options as $menu) : ?>
						<option value="<?= htmlspecialchars($menu->link); ?>"
							data-title="<?= htmlspecialchars($menu->label); ?>"
							<?= ($selectedMenuLink === $menu->link) ? 'selected' : ''; ?>>
							<?= htmlspecialchars($menu->label); ?> (<?= htmlspecialchars($menu->link); ?>)
						</option>
				<?php endforeach;
				endif; ?>
				<option value="__custom__" <?= $useCustomLink ? 'selected' : ''; ?>>Link kustom (di luar daftar menu)</option>
			</select>
			<small class="form-text text-muted">Link diambil dari master menu aplikasi (path yang sama dengan sidebar).</small>
			<span class="invalid-feedback">Pilih menu atau isi link kustom</span>
		</div>
	</div>

	<div class="mb-3 row <?= $useCustomLink ? '' : 'd-none'; ?>" id="link-custom-wrap">
		<label class="col-3 col-form-label font-weight-bold">Link Kustom</label>
		<div class="col-9">
			<input type="text" name="link_custom" id="link_custom" class="form-control"
				placeholder="Contoh: list/procedures atau process_checksheets"
				value="<?= $useCustomLink ? htmlspecialchars($stored_link) : ''; ?>">
			<small class="form-text text-muted">Tanpa domain. Contoh: <code>procedures</code> atau <code>list/procedures</code></small>
		</div>
	</div>

	<div class="mb-3 row">
		<label class="col-3 col-form-label font-weight-bold">Urutan</label>
		<div class="col-9">
			<input type="number" name="sort_order" class="form-control" min="0" value="<?= $isEdit ? (int) $data->sort_order : 0; ?>">
		</div>
	</div>

	<div class="mb-3 row">
		<label class="col-3 col-form-label font-weight-bold">Status</label>
		<div class="col-9">
			<select name="is_active" class="form-control">
				<option value="Y" <?= ($isEdit && $data->is_active === 'Y') || !$isEdit ? 'selected' : ''; ?>>Aktif (tampil di dashboard)</option>
				<option value="N" <?= ($isEdit && $data->is_active === 'N') ? 'selected' : ''; ?>>Nonaktif (disembunyikan)</option>
			</select>
		</div>
	</div>

	<div class="mb-3 row">
		<label class="col-3 col-form-label font-weight-bold">Gambar</label>
		<div class="col-9">
			<?php if ($isEdit && $data->picture) : ?>
				<div class="mb-2">
					<img src="<?= base_url('assets/images/dashboard/' . $data->picture); ?>" alt="preview" style="max-height: 120px;">
					<p class="text-muted small mb-0">Upload file baru untuk mengganti gambar.</p>
				</div>
			<?php endif; ?>
			<input type="file" name="picture" class="form-control-file" accept="image/*" <?= $isEdit ? '' : 'required'; ?>>
			<small class="form-text text-muted">Format: JPG, PNG, GIF, WEBP. Maks. 2MB.</small>
		</div>
	</div>
</form>

<script>
	(function() {
		const $linkMenu = $('#link_menu');
		const $linkCustomWrap = $('#link-custom-wrap');
		const $linkCustom = $('#link_custom');
		const $name = $('#name');
		const $modal = $('#modalCard');

		function initLinkMenuSelect2() {
			if (!$linkMenu.length || typeof $.fn.select2 !== 'function') {
				return;
			}
			if ($linkMenu.hasClass('select2-hidden-accessible')) {
				$linkMenu.select2('destroy');
			}
			$linkMenu.select2({
				width: '100%',
				placeholder: $linkMenu.data('placeholder') || 'Cari atau pilih menu...',
				allowClear: true,
				dropdownParent: $modal.length ? $modal : $(document.body)
			});
		}

		function toggleCustomLink() {
			if ($linkMenu.val() === '__custom__') {
				$linkCustomWrap.removeClass('d-none');
				$linkCustom.prop('required', true);
			} else {
				$linkCustomWrap.addClass('d-none');
				$linkCustom.prop('required', false);
			}
		}

		function onLinkMenuChange() {
			toggleCustomLink();
			const val = $linkMenu.val();
			const $opt = $linkMenu.find('option:selected');
			if (val && val !== '__custom__' && $opt.data('title') && !$name.val()) {
				const title = String($opt.data('title'));
				$name.val(title.indexOf(' › ') > -1 ? title.split(' › ').pop() : title);
			}
		}

		initLinkMenuSelect2();
		$linkMenu.on('change select2:select select2:clear', onLinkMenuChange);
		toggleCustomLink();
	})();
</script>
