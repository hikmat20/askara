<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<div class="card card-stretch shadow card-custom">
				<div class="card-header">
					<h2 class="mt-5"><i class="<?= $icon; ?> mr-2"></i><?= $title; ?></h2>
					<div class="mt-4 float-right">
						<a href="<?= base_url('dashboard'); ?>" class="btn btn-secondary mr-2" title="Kembali ke Dashboard">
							<i class="fa fa-arrow-left mr-1"></i>Kembali
						</a>
						<button type="button" class="btn btn-primary" id="add-card" title="Tambah Card">
							<i class="fa fa-plus mr-1"></i>Tambah Card
						</button>
					</div>
				</div>
				<div class="card-body">
					<table id="table-cards" class="table table-bordered table-sm table-condensed table-hover datatable">
						<thead class="text-center table-light">
							<tr>
								<th width="3%">No.</th>
								<th width="80">Gambar</th>
								<th class="text-left">Nama</th>
								<th class="text-left">Link</th>
								<th width="60">Urutan</th>
								<th width="80">Status</th>
								<th width="120">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($data)) :
								$n = 0;
								foreach ($data as $dt) : $n++; ?>
									<tr>
										<td class="text-center"><?= $n; ?></td>
										<td class="text-center">
											<?php if ($dt->picture) : ?>
												<img src="<?= base_url('assets/images/dashboard/' . $dt->picture); ?>" alt="<?= htmlspecialchars($dt->name); ?>" style="height: 48px; max-width: 64px; object-fit: contain;">
											<?php endif; ?>
										</td>
										<td><?= htmlspecialchars($dt->name); ?></td>
										<td><code><?= htmlspecialchars($dt->link); ?></code></td>
										<td class="text-center"><?= (int) $dt->sort_order; ?></td>
										<td class="text-center">
											<?php if ($dt->is_active === 'Y') : ?>
												<span class="badge badge-success">Aktif</span>
											<?php else : ?>
												<span class="badge badge-secondary">Nonaktif</span>
											<?php endif; ?>
										</td>
										<td class="text-center">
											<button type="button" class="btn btn-sm btn-icon rounded-circle btn-warning edit-card" data-id="<?= $dt->id; ?>" title="Edit"><i class="fa fa-edit"></i></button>
											<button type="button" class="btn btn-sm btn-icon rounded-circle btn-danger delete-card" data-id="<?= $dt->id; ?>" title="Hapus"><i class="fa fa-trash"></i></button>
										</td>
									</tr>
							<?php endforeach;
							else : ?>
								<tr>
									<td colspan="7" class="text-center text-muted">Belum ada card. Klik "Tambah Card" untuk menambahkan.</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
	#modalCard .select2-container.is-invalid .select2-selection {
		border-color: #F64E60 !important;
	}
</style>

<div class="modal fade" id="modalCard" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Card Dashboard</h5>
				<span class="close btn-cls" data-dismiss="modal" aria-label="Close"></span>
			</div>
			<div class="modal-body"></div>
			<div class="modal-footer justify-content-end">
				<button type="button" class="btn btn-primary save-card"><i class="fa fa-save"></i> Save</button>
				<button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('#table-cards').DataTable({
			ordering: false,
			pageLength: 25
		});

		$(document).on('click', '#add-card', function() {
			$('.modal-title').html('Tambah Card Dashboard');
			$('#modalCard').modal('show');
			$('.modal-body').load(siteurl + active_controller + 'add_card');
		});

		$(document).on('click', '.edit-card', function() {
			const id = $(this).data('id');
			$('.modal-title').html('Edit Card Dashboard');
			$('#modalCard').modal('show');
			$('.modal-body').load(siteurl + active_controller + 'edit_card/' + id);
		});

		$(document).on('click', '.save-card', function() {
			const name = $('#name');
			const linkMenu = $('#link_menu');
			const linkCustom = $('#link_custom');
			validation(name);

			const $linkMenuContainer = linkMenu.next('.select2-container');
			linkMenu.removeClass('is-invalid');
			$linkMenuContainer.removeClass('is-invalid');
			if (!linkMenu.val()) {
				linkMenu.addClass('is-invalid');
				$linkMenuContainer.addClass('is-invalid');
			} else if (linkMenu.val() === '__custom__' && !linkCustom.val().trim()) {
				linkCustom.addClass('is-invalid');
			} else {
				linkCustom.removeClass('is-invalid');
			}

			if (name.hasClass('is-invalid') || linkMenu.hasClass('is-invalid') || linkCustom.hasClass('is-invalid')) {
				return;
			}

			const form = $('#form-card')[0];
			if (!form.querySelector('[name="id"]') && !form.querySelector('[name="picture"]').files.length) {
				Swal.fire({ title: 'Peringatan', icon: 'warning', text: 'Gambar card wajib diupload.' });
				return;
			}

			const formdata = new FormData(form);
			const btn = $(this);

			$.ajax({
				url: siteurl + active_controller + 'save_card',
				data: formdata,
				type: 'POST',
				dataType: 'JSON',
				processData: false,
				contentType: false,
				cache: false,
				beforeSend: function() {
					btn.prop('disabled', true).html('<i class="spinner spinner-border-sm"></i> Menyimpan...');
				},
				complete: function() {
					btn.prop('disabled', false).html('<i class="fa fa-save"></i> Simpan');
				},
				success: function(result) {
					if (result.status == 1) {
						Swal.fire({ title: 'Berhasil', icon: 'success', text: result.msg, timer: 1500 }).then(function() {
							location.reload();
						});
					} else {
						Swal.fire({ title: 'Gagal', icon: 'warning', text: result.msg });
					}
				},
				error: function() {
					Swal.fire({ title: 'Error', icon: 'error', text: 'Terjadi kesalahan server.' });
				}
			});
		});

		$(document).on('click', '.delete-card', function() {
			const id = $(this).data('id');
			const btn = $(this);
			Swal.fire({
				title: 'Hapus card?',
				icon: 'question',
				text: 'Card yang dihapus tidak dapat dikembalikan.',
				showCancelButton: true,
			}).then(function(value) {
				if (!value.isConfirmed) return;
				$.ajax({
					url: siteurl + active_controller + 'delete_card',
					data: { id: id },
					type: 'POST',
					dataType: 'JSON',
					beforeSend: function() {
						btn.prop('disabled', true);
					},
					complete: function() {
						btn.prop('disabled', false);
					},
					success: function(result) {
						if (result.status == 1) {
							Swal.fire({ title: 'Berhasil', icon: 'success', text: result.msg, timer: 1500 }).then(function() {
								location.reload();
							});
						} else {
							Swal.fire({ title: 'Gagal', icon: 'warning', text: result.msg });
						}
					}
				});
			});
		});
	});

	function validation(field) {
		if (field.val() === '' || field.val() === null) {
			field.addClass('is-invalid');
		} else {
			field.removeClass('is-invalid');
		}
	}
</script>
