<style>
	#image {
		background-image: url('assets/images/dashboard/folder-file.png');
		height: 400px;
		max-width: 400px;
		background-position: center;
		background-repeat: no-repeat;
		background-size: cover;
	}

	#image:hover {
		background-image: url('assets/images/dashboard/folder-file.gif');
	}
</style>

<div class="content d-flex flex-column flex-column-fluid p-0">
	<div class="d-flex flex-column-fluid justify-content-between align-items-top">
		<div class="container mt-10">
			<div class="text-center pb-10">
				<h2 class="text-white fa-3x">CREATE DOCUMENTS</h2>
				<p class="text-light-dark h1 font-weight-bolder">Update and Statistic</p>
			</div>
			<div class="row mt-10">
				<div class="col-md-9">
					<div class="row px-10 px-lg-0 px-md-0">
						<div class="col-12 col-sm-6 col-md-3 mb-10">
							<div class="card border-0 shadow-sm" style="border-radius: 50px 5px 50px 5px;background-color: rgba(255, 255, 255, 1);">
								<div class="card-body pb-1 d-flex justify-content-center align-items-center" style="height: 200px;">
									<img id="myImg1" src="<?= base_url('assets/images/dashboard/folder-file.png'); ?>" alt="" class="img-fluid" style="width: 200px;height: auto;">
								</div>
								<h3 class="card-title text-center pt-5 pb-10 px-4 m-0" style="min-height: 80px;">
									<a href="<?= base_url('/docs/create'); ?>" class="text-hover-primary" title="Create Document">
										<span class="card-label m-0 text-dark text-center font-weight-bolder">CREATE DOCUMENTS</span>
									</a>
								</h3>
							</div>
						</div>

						<div class="col-12 col-sm-6 col-md-3 mb-10">
							<div class="card border-0 shadow-sm " style="border-radius: 50px 5px 50px 5px;background-color: rgba(255, 255, 255, 1);">
								<div class="card-body pb-1 d-flex justify-content-center align-items-center" style="height: 200px;">
									<img id="myImg3" src="<?= base_url('assets/images/dashboard/review.png'); ?>" alt="" class="img-fluid" style="width: 200px;height: auto;">
								</div>
								<h3 class="card-title text-center pt-5 pb-10 px-4 m-0" style="min-height: 80px;">
									<a href="<?= base_url('/docs/review'); ?>" class="text-hover-primary" title="Review Document">
										<span class="card-label m-0 text-dark text-center font-weight-bolder">REVIEW DOCUMENTS</span>
									</a>
								</h3>
							</div>
						</div>
						<div class="col-12 col-sm-6 col-md-3 mb-10">
							<div class="card border-0 shadow-sm " style="border-radius: 50px 5px 50px 5px;background-color: rgba(255, 255, 255, 1);">
								<div class="card-body pb-1 d-flex justify-content-center align-items-center" style="height: 200px;">
									<img id="myImg2" src="<?= base_url('assets/images/dashboard/correction.png'); ?>" alt="" class="img-fluid" style="width: 200px;height: auto;">
								</div>
								<h3 class="card-title text-center pt-5 pb-10 px-4 m-0" style="min-height: 80px;">
									<a href="<?= base_url('/docs/correction'); ?>" class="text-hover-primary" title="Correction Document">
										<span class="card-label m-0 text-dark text-center font-weight-bolder">CORRECTION DOCUMENTS</span>
									</a>
								</h3>
							</div>
						</div>
						<div class="col-12 col-sm-6 col-md-3 mb-10">
							<div class="card border-0 shadow-sm " style="border-radius: 50px 5px 50px 5px;background-color: rgba(255, 255, 255, 1);">
								<div class="card-body pb-1 d-flex justify-content-center align-items-center" style="height: 200px;">
									<img id="myImg4" src="<?= base_url('assets/images/dashboard/approval.png'); ?>" alt="" class="img-fluid" style="width: 200px;height: auto;">
								</div>
								<h3 class="card-title text-center pt-5 pb-10 px-4 m-0" style="min-height: 80px;">
									<a href="<?= base_url('/docs/approval'); ?>" class="text-hover-primary" title="Approval Document">
										<span class="card-label m-0 text-dark text-center font-weight-bolder">APPROVAL DOCUMENTS</span>
									</a>
								</h3>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="row px-lg-5 px-md-5 px-10 ">
						<div class="col-md-10 offset-0 offset-lg-2 offset-md-2">
							<div class="card border-0 shadow-sm " style="border-radius: 50px 5px 50px 5px;background-color: rgba(255, 255, 255, 1);">
								<div class="card-body pb-1 d-flex justify-content-center align-items-center" style="height: 200px;">
									<img id="myImg5" src="<?= base_url('assets/images/dashboard/monitoring.png'); ?>" alt="" class="img-fluid" style="width: 200px;height: auto;">
								</div>
								<h3 class="card-title text-center pt-5 pb-10 px-4 m-0" style="min-height: 50px;">
									<a href="<?= base_url('/docs/monitoring'); ?>" class="text-hover-primary" title="Monitoring Document">
										<span class="card-label m-0 text-dark text-center font-weight-bolder">MONITORING DOCUMENTS</span>
									</a>
								</h3>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
	$(function() {
		$("#myImg1").hover(
			function() {
				$(this).attr("src", "assets/images/dashboard/folder-file.gif");
			},
			function() {
				$(this).attr("src", "assets/images/dashboard/folder-file.png");
			}
		);
	});
</script>