<!-- Nav tabs -->
<ul class="nav pb-2 nav-success nav-tabs nav-pills">
	<li class="nav-item">
		<a href="javascript:void(0)" id="home" data-procedure="<?= $procedure_id; ?>" class="nav-link py-2 px-3">
			<i class="fa fa-home mr-2"></i>
			Home
		</a>
	</li>
	<li class="nav-item">
		<a href="javascript:void(0)" id="back" data-id="<?= ($id) ?: ''; ?>" data-procedure="<?= $procedure_id; ?>" class="nav-link py-2 px-3 <?= ($EOF) ? 'disabled' : ''; ?>">
			<i class="fa fa-arrow-up mr-2"></i>
			Up Folder
		</a>
	</li>
	<li class="nav-item">
		<a href="javascript:void(0)" id="refresh" data-id="<?= ($id) ?: ''; ?>" data-procedure="<?= $procedure_id; ?>" class="nav-link py-2 px-3">
			<i class="fa fa-sync-alt mr-2"></i>
			Refresh
		</a>
	</li>
</ul>
<table class="table table-condensed table-hover">
	<thead>
		<tr class="">
			<th class="py-1">File Name</th>
			<th class="py-1 text-right" width="250">Last Update</th>
			<th class="py-1 text-center" width="50px"></th>
		</tr>
	</thead>
	<tbody>
		<?php if (($records)) :
			$no = 0;
			foreach ($records as $lsRec) : $no++; ?>
				<tr class="cursor-pointer h6 <?= ($lsRec->flag_type == 'FOLDER') ? 'record-item' : ''; ?>  " data-procedure="<?= $procedure_id; ?>" data-id="<?= $lsRec->id; ?>">
					<td class="text-dark d-flex align-items-center">
						<?php if ($lsRec->flag_type == 'FOLDER') : ?>
							<i class="fa fa-folder text-warning font-size-h2 mr-3"></i>
						<?php else : ?>
							<i class="fa fa-file-alt text-info font-size-h2 mr-3"></i>
						<?php endif; ?>
						<span class="mt-2"><?= $lsRec->name; ?></span>
					</td>
					<td style="vertical-align: middle;" class="text-right pt-1"><?= ($lsRec->modified_at) ?: $lsRec->created_at; ?></td>
					<td class="h6 text-center pt-1" style="vertical-align: middle;">
						<?php if ($lsRec->flag_type == 'FILE') : ?>
							<button type="button" class="btn btn-icon btn-xs shadow-xs btn-info view-record" data-id="<?= $lsRec->id; ?>" data-toggle="tooltip" data-theme="dark" title="View Document"><i class="fa fa-eye"></i></button>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach;
		else : ?>
			<tr>
				<td colspan="3" class="text-center h4"><i>No data available</i></td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>