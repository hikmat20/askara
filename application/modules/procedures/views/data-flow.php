<?php if ($detail) :
	$n = 0;
	foreach ($detail as $key => $dtl) : $n++; ?>
		<tr class="sortable-row" data-id="<?= $dtl->id; ?>">
			<td style="vertical-align:middle;" class="text-center">
				<i class="fa fa-bars text-muted drag-handle mr-2" style="cursor: move;"></i>
				<?= $dtl->number; ?>
			</td>
			<td style="vertical-align:middle;" class="text-center"><?= $dtl->pic; ?></td>
			<td><?= $dtl->description; ?></td>
			<td><?= $dtl->description_2; ?></td>
			<td style="vertical-align: middle;">
				<?php $relDocs = json_decode($dtl->relate_doc); ?>
				<?php $relIK = json_decode($dtl->relate_ik_doc); ?>
				<?php if (is_array($relDocs)) : ?>
					<?php foreach ($relDocs as $relDoc) { ?>
						<span class="badge btn btn-success view-form mb-1" data-id="<?= $relDoc; ?>"><?= $ArrForms[$relDoc]->name; ?></span>
					<?php } ?>
				<?php endif; ?>

				<?php if (is_array($relIK)) : ?>
					<?php foreach ($relIK as $ik) { ?>
						<span class="badge btn btn-danger view-form mb-1" data-id="<?= $ik; ?>"><?= $ArrGuides[$ik]->name; ?></span>
					<?php } ?>
				<?php endif; ?>
			</td>
			<td class="text-center" style="vertical-align: middle;">
				<button type="button" class="btn btn-warning btn-icon rounded-circle btn-sm edit_flow" data-proc_id="<?= $dtl->procedure_id; ?>" data-id="<?= $dtl->id; ?>"><i class="fa fa-edit"></i></button>
				<button type="button" class="btn btn-danger btn-icon rounded-circle btn-sm delete_flow" data-id="<?= $dtl->id; ?>"><i class="fa fa-trash"></i></button>
			</td>
		</tr>
	<?php endforeach;
else : ?>
	<tr>
		<td colspan="5" class="text-center text-muted">~ No data avilable ~</td>
	</tr>
<?php endif; ?>