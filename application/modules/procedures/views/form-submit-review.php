<form id="submit_review">
	<input type="hidden" name="id" id="id" value="<?= $id; ?>">
	<div class="form-group">
		<label for="review_date"></label>
	</div>
	<input type="date" name="review_date" id="review_date" class="form-control" value="<?= date('Y-m-d'); ?>">
	<textarea name="note" id="note" class="form-control summernote" placeholder="Note"></textarea>
	<button type="submit" class="btn btn-primary">Submit to Review</button>
</form>