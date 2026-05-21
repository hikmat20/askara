<div class="content d-flex flex-column flex-column-fluid p-0">
	<div class="d-flex flex-column-fluid justify-content-between align-items-top">
		<div class="container mt-5">
			<div class="d-flex justify-content-between align-items-center mb-5">
				<h1 class="text-white mb-0 mt-0 pt-0 font-weight-bolder bg-white-o-0 rounded-lg px-10 py-5">PUBLISHED DOCUMENTS</h1>
				<?php if (!empty($is_admin)) : ?>
					<a href="<?= base_url('dashboard/cards'); ?>" class="btn btn-sm font-weight-bold" title="Kelola Card Dashboard">
						<i class="fa fa-cog"></i>
					</a>
				<?php endif; ?>
			</div>
			<div class="row justify-content-center">
				<div class="col-md-10 mb-10">
					<div class="row">
						<?php if (!empty($cards)) :
							foreach ($cards as $card) :
								$href = (strpos($card->link, 'http://') === 0 || strpos($card->link, 'https://') === 0)
									? $card->link
									: base_url(ltrim($card->link, '/'));
								$imgSrc = base_url('assets/images/dashboard/' . $card->picture);
						?>
							<div class="col-md-2 col-md-3 mb-5">
								<div class="card border-0 shadow-lg" style="border-radius: 30px 5px 30px 5px;background-color: rgba(255, 255, 255, 0.50);">
									<div class="card-body pb-1 d-flex justify-content-center align-items-center" style="min-height: 120px;">
										<img src="<?= $imgSrc; ?>" alt="<?= htmlspecialchars($card->name); ?>" class="img-fluid" style="height: 150px;">
									</div>
									<h6 class="card-title text-center d-flex align-items-center m-auto" style="min-height: 60px;">
										<a href="<?= $href; ?>" class="text-hover-primary" title="<?= htmlspecialchars($card->name); ?>">
											<span class="card-label m-0 text-dark text-center font-weight-bolder"><?= htmlspecialchars($card->name); ?></span>
										</a>
									</h6>
								</div>
							</div>
						<?php endforeach;
						else : ?>
							<div class="col-12 text-center text-white py-10">
								<p class="mb-0">Belum ada card aktif di dashboard.</p>
								<?php if (!empty($is_admin)) : ?>
									<a href="<?= base_url('dashboard/cards'); ?>" class="btn btn-light mt-3">Kelola Card Dashboard</a>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
