<form id="form-approval">
	<input type="hidden" name="id" id="id" value="<?= $id; ?>">
	<div class="alert alert-custom alert-light-danger py-3 d-none" id="invalid-action" role="alert"></div>
	<div class="form-group pb-3">
		<div class="checkbox-inline">
			<label class="checkbox checkbox-outline checkbox-outline-2x checkbox-primary">
				<input type="checkbox" class="status_approve" name="status" value="PUB" />
				<span></span>
				<strong>Publish Document</strong>
			</label>
		</div>
		<div class="pl-8 pb-3">I Aggree, file ready to publish.</div>
		<div class="pl-8 mb-3">
			<label for="">Published Date</label>
			<input type="date" name="published_date" disabled min="<?= date('Y-m-d', strtotime(date('Y-m-d') . '-7 days')); ?>" id="published_date" class="form-control" placeholder="Published Date">
			<span class="invalid-feedback text-danger">Harus di isi</span>
		</div>
	</div>
	<div class="form-group pb-3">
		<div class="checkbox-inline">
			<label class="checkbox checkbox-outline checkbox-outline-2x checkbox-danger">
				<input type="checkbox" class="status_approve" name="status" value="COR" />
				<span></span>
				<strong>Correction Document</strong>
			</label>
		</div>
		<div class="pb-3 pl-8">This document still needs to be corrected.</div>
		<div class="mb-3 pl-8">
			<textarea name="note" disabled rows="5" id="note" class="form-control" placeholder="Note"></textarea>
			<span class="invalid-feedback text-danger">Harus di isi</span>
		</div>
	</div>
	<button type="submit" class="btn btn-light-info mn-w-100px" id="save-approval"><i class="fab fa-telegram-plane"></i>Submit Approve</button>
</form>