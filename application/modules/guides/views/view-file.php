<!-- Document Detail -->
<h6 class="font-weight-bold mb-3">Document Detail</h6>
<div class="row mb-3">
	<div class="col-md-6">
		<table class="table table-borderless table-sm">
			<tbody>
				<tr>
					<td width="160">Document Number</td>
					<td width="10">:</td>
					<td><strong><?= $data->doc_number; ?></strong></td>
				</tr>
				<tr>
					<td>Document Name</td>
					<td>:</td>
					<td><strong><?= $data->doc_name; ?></strong></td>
				</tr>
				<tr>
					<td>Issue Date Rev-0</td>
					<td>:</td>
					<td><strong><?= $data->issue_date ? date('d/m/Y', strtotime($data->issue_date)) : '-'; ?></strong></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col-md-6">
		<table class="table table-borderless table-sm">
			<tbody>
				<tr>
					<td width="160">Effective Date</td>
					<td width="10">:</td>
					<td><strong><?= $data->effective_date ? date('d/m/Y', strtotime($data->effective_date)) : '-'; ?></strong></td>
				</tr>
				<tr>
					<td>Revision Number</td>
					<td>:</td>
					<td><strong><?= $data->doc_revision_number; ?></strong></td>
				</tr>
				<tr>
					<td>Prepare By</td>
					<td>:</td>
					<td><strong><?= isset($prepare_by_name) ? $prepare_by_name : '-'; ?></strong></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<hr>

<!-- Approval Detail -->
<h6 class="font-weight-bold mb-3">Approval Detail</h6>
<div class="row mb-3">
	<div class="col-md-6">
		<table class="table table-borderless table-sm">
			<tbody>
				<tr>
					<td width="160">PIC Reviewer</td>
					<td width="10">:</td>
					<td><strong><?= isset($reviewer_name) ? $reviewer_name : '-'; ?></strong></td>
				</tr>
				<tr>
					<td>PIC Approval</td>
					<td>:</td>
					<td><strong><?= isset($approval_name) ? $approval_name : '-'; ?></strong></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<hr>

<!-- Documents -->
<h6 class="font-weight-bold mb-3">Documents</h6>
<table class="table table-bordered table-striped table-hover table-sm">
	<thead>
		<tr class="bg-success text-white">
			<th width="50" class="text-center p-2">No</th>
			<th class="p-2">Name File</th>
			<th width="180" class="text-center p-2">Last Update</th>
		</tr>
	</thead>
	<tbody>
		<?php if ($DocIK) : $n = 0; foreach ($DocIK as $ik) : $n++; ?>
			<tr>
				<td class="text-center"><?= $n; ?></td>
				<td>
					<a target="_blank" href="<?= base_url('/directory/MASTER_GUIDES/' . $data->company_id . '/IK/' . $ik->file); ?>"><?= $ik->name; ?></a>
				</td>
				<td class="text-center"><?= $ik->created_at; ?></td>
			</tr>
		<?php endforeach; endif; ?>
	</tbody>
</table>
