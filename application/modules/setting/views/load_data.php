<?php if (isset($data) && $data) :
	$n = 0;
	foreach ($data as $dt) : $n++; ?>
		<tr class="">
			<td><?= $n; ?></td>
			<td class="text-left"><?= $dt->nm_perusahaan; ?></td>
			<td><?= $dt->alamat; ?></td>
			<td><?= $dt->kota; ?></td>
			<td><?= $dt->inisial; ?></td>
			<td>
				<button type="button" class="btn btn-sm btn-icon rounded-circle btn-info view" data-id="<?= $dt->id_perusahaan; ?>" title="View Data"><i class="fa fa-search"></i></button>
				<button type="button" class="btn btn-sm btn-icon rounded-circle btn-warning edit" data-id="<?= $dt->id_perusahaan; ?>" title="Edit Data"><i class="fa fa-edit"></i></button>
				<button type="button" class="btn btn-sm btn-icon rounded-circle btn-danger delete" data-id="<?= $dt->id_perusahaan; ?>" title="View Data"><i class="fa fa-trash"></i></button>
			</td>
		</tr>
<?php endforeach;
endif; ?>