<form id="form-review">
	<input type="hidden" name="id" id="id" value="<?= $id; ?>">
	<div class="form-group">
		<label class="col-form-label font-weight-bold">Action Review</label>
		<div class="div">
			<span id="invalid-action" class="d-none text-danger">Pilih salah satu Action</span>
		</div>
		<div class="col-form-label mb-3">
			<div class="radio-inline">
				<label class="radio radio-outline radio-outline-2x radio-primary">
					<input type="radio" name="status" value="APV" />
					<span></span>
					I agree to this file, and continue to the next process
				</label>
			</div>
			<span class="form-text text-muted">Ready to Approval Process</span>
			<span class="invalid-feedback text-danger"></span>
		</div>
		<div class="col-form-label mb-3">
			<div class="radio-inline">
				<label class="radio radio-outline radio-outline-2x radio-danger">
					<input type="radio" name="status" value="COR" />
					<span></span>
					I don't agree, because some need corrections
				</label>
				<span class="invalid-feedback text-danger"></span>
			</div>
			<span class="form-text text-muted">write down the reason</span>
			<textarea name="note" disabled id="note_correction" rows="6" class="form-control" placeholder="Reason"></textarea>
			<span class="invalid-feedback text-danger">Harus di isi</span>
		</div>
		<button type="submit" class="btn btn-light-primary" id="save-review"><i class="fab fa-telegram-plane"></i>Submit Review</button>
	</div>
</form>