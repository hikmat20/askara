<div class="card" id="file" role="tabpanel" aria-labelledby="file-tab">
	<div style="width:92%;height:70vh;background-color: red;position: absolute;opacity: 0;"></div>
	<?php if ($ik->ext == '.pdf' || $ik->ext == '.PDF') : ?>
		<iframe src="<?= base_url("directory/WI/$ik->company_id/$ik->file_name"); ?>#toolbar=0&navpanes=0" frameborder="0" width="100%" style="height:70vh"></iframe>
	<?php else : ?>
		<iframe src="https://docs.google.com/gview?embedded=true&url=<?= base_url("directory/WI/$ik->company_id/$form->file_name"); ?>&rm=minimal#toolbar=0&navpanes=0" frameborder="0" width="100%" style="height: 70vh;"></iframe>
	<?php endif; ?>
</div