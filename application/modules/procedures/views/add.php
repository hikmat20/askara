<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
	<div class="d-flex flex-column-fluid">
		<div class="container">
			<form id="form-procedure">
				<div class="card card-stretch shadow card-custom">
					<div class="card-header justify-content-between d-flex align-items-center">
						<h2 class="m-0"><i class="fa fa-plus mr-2"></i><?= $title; ?></h2>
						<a href="<?= base_url($this->uri->segment(1)); ?>" class="btn btn-danger"><i class="fa fa-reply"></i>Back</a>
					</div>

					<div class="card-body">
						<div id="accProcedure" role="tablist" aria-multiselectable="true">
							<ul class="nav nav-tabs nav-pills border-0 mb-5" id="myTab" role="tablist">
								<li class="nav-item" role="presentation">
									<button class="nav-link active" id="procedures-tab" data-toggle="tab" data-target="#procedures" type="button" role="tab" aria-controls="procedures" aria-selected="true">PROCEDURE</button>
								</li>
								<li class="nav-item" role="presentation">
									<button class="nav-link" disabled id="form-tab" data-toggle="tab" data-target="#form" type="button" role="tab" aria-controls="form" aria-selected="false">FORM</button>
								</li>
								<li class="nav-item" role="presentation">
									<button class="nav-link" disabled id="guides-tab" data-toggle="tab" data-target="#guides" type="button" role="tab" aria-controls="guides" aria-selected="false">IK</button>
								</li>
								<!-- <li class="nav-item" role="presentation">
									<button class="nav-link" disabled id="records-tab" data-toggle="tab" data-target="#records" type="button" role="tab" aria-controls="records" aria-selected="false">RECORD</button>
								</li> -->
							</ul>
							<!-- Tab panes -->
							<div class="tab-content rounded-lg">
								<div class="tab-pane fade show active" id="procedures" role="tabpanel" aria-labelledby="procedures-tab">
									<!-- DETAIL PROSES -->
									<div class="card shadow- mb-3 border-0 " style="border-radius: 10px;">
										<div class="card-header bg-light border-0 py-4 cursor-pointer" role="tab" id="sectionDetail" style="border-radius: 10px;">
											<h4 class="mb-0 font-weight-bolder" data-toggle="collapse" data-parent="#accProcedure" href="#detailProcess" aria-expanded="true" aria-controls="detailProcess">
												DETAIL PROSES
											</h4>
										</div>

										<div id="detailProcess" class="collapse in show" role="tabpanel" aria-labelledby="sectionDetail">
											<div class="card-body">
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label class="font-size-h5"><strong><span class="text-danger"></span> Departement</strong></label>
															<div class="">
																<select name="departement_id" class="form-control select2">
																	<option value=""></option>
																	<?php foreach ($depts as $dept) : ?>
																		<option value="<?= $dept->id; ?>"><?= $dept->name; ?></option>
																	<?php endforeach; ?>
																</select>
															</div>
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label class="font-size-h5"><strong><span class="text-danger">*</span> Kelompok Proces</strong></label>
															<div class="">
																<select name="group_procedure" id="group_procedure" class="form-control select2">
																	<option value=""></option>
																	<?php foreach ($grProcess as $pro) : ?>
																		<option value="<?= $pro->id; ?>"><?= $pro->name; ?></option>
																	<?php endforeach; ?>
																</select>
															</div>
														</div>
													</div>

													<div class="col-md-6">
														<div class="form-group">
															<label class="font-size-h5"><strong><span class="text-danger">*</span> Nomor</strong></label>
															<div class="">
																<input name="nomor" id="nomor" class="form-control" placeholder="Nomor" aria-describedby="helpId"></input>
																<small class="text-danger invalid-feedback">Nomor</small>
															</div>
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label class="font-size-h5"><strong><span class="text-danger">*</span> Status</strong></label>
															<div class="">
																<select name="status" id="status" class="form-control select2">
																	<option value="DFT" selected>Draft</option>
																</select>
															</div>
														</div>
													</div>

												</div>

												<!-- indonesia - english -->
												<ul class="nav nav-tabs border-0" id="myTab" role="tablist">
													<li class="nav-item" role="presentation">
														<button class="nav-link active p-3" id="local-tab" data-toggle="tab" data-target="#local" type="button" role="tab" aria-controls="local" aria-selected="true">Indonesia</button>
													</li>
													<li class="nav-item" role="presentation">
														<button class="nav-link p-3" id="asing-tab" data-toggle="tab" data-target="#asing" type="button" role="tab" aria-controls="asing" aria-selected="false">English</button>
													</li>
												</ul>

												<div class="tab-content rounded-bottom rounded-bottom-xl border">
													<div class="tab-pane fade show active p-3" id="local" role="tabpanel" aria-labelledby="local-tab">
														<div class="row">
															<div class="col-md-12">
																<div class="form-group">
																	<label class="font-size-h5"><strong><span class="text-danger">*</span> Nama Proses</strong></label>
																	<div class="">
																		<input name="name" id="name" class="form-control" placeholder="Nama Proses" aria-describedby="helpId"></input>
																		<small class="text-danger invalid-feedback">Nama Proses</small>
																	</div>
																</div>
															</div>

														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="form-group">
																	<label class="font-size-h5"><strong><span class="text-danger">*</span> Objektif Proses</strong></label>
																	<div class="">
																		<textarea name="object" id="object" class="form-control editor" placeholder="Objektif Proses" aria-describedby="helpId"></textarea>
																		<small class="text-danger invalid-feedback">Objektif Proses</small>
																	</div>
																</div>
															</div>
															<div class="col-md-12">
																<div class="form-group">
																	<label class="font-size-h5"><strong><span class="text-danger">*</span> Ruang Lingkup</strong></label>
																	<div class="">
																		<textarea name="scope" id="scope" class="form-control editor" placeholder="Ruang Lingkup" aria-describedby="helpId"></textarea>
																		<small class="text-danger invalid-feedback">Ruang Lingkup</small>
																	</div>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="form-group">
																	<label class="font-size-h5"><strong><span class="text-danger">*</span> Performa Indikator</strong></label>
																	<div class="">
																		<textarea name="performance" id="performance" class="form-control editor" placeholder="Performa Indikator" aria-describedby="helpId"></textarea>
																		<small class="text-danger invalid-feedback">Performa Indikator</small>
																	</div>
																</div>
																<div class="form-group">
																	<label class="font-size-h5"><strong>Definisi</strong></label>
																	<div class="">
																		<textarea name="define" id="define" class="form-control editor " placeholder="Definisi" aria-describedby="helpId"></textarea>
																		<small class="text-danger invalid-feedback">Definisi Proses</small>
																	</div>
																</div>
																<div class="form-group">
																	<label class="font-size-h5"><strong>Tanggung Jawab</strong></label>
																	<div class="">
																		<textarea name="responsibility" id="responsibility" class="form-control editor " placeholder="Tanggung Jawab" aria-describedby="helpId"></textarea>
																		<small class="text-danger invalid-feedback">Tanggung Jawab</small>
																	</div>
																</div>
																<div class="form-group">
																	<label class="font-size-h5"><strong>Ketentuan Umum</strong></label>
																	<div class="">
																		<textarea name="general_requirement" id="general_requirement" class="form-control editor " placeholder="Ketentuan Umum" aria-describedby="helpId"></textarea>
																		<small class="text-danger invalid-feedback">Ketentuan Umum</small>
																	</div>
																</div>
															</div>
														</div>
													</div>
													<div class="tab-pane fade p-3" id="asing" role="tabpanel" aria-labelledby="asing-tab">
														<div class="row">
															<div class="col-md-12">
																<div class="form-group">
																	<label class="font-size-h5"><strong><span class="text-danger">*</span> Process Name</strong></label>
																	<div class="">
																		<input name="bilingual[english][name]" class="form-control" placeholder="Process Name" aria-describedby="helpId"></input>
																		<small class="text-danger invalid-feedback">Process Name</small>
																	</div>
																</div>
															</div>

														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="form-group">
																	<label class="font-size-h5"><strong><span class="text-danger">*</span> Objektive Process</strong></label>
																	<div class="">
																		<textarea name="bilingual[english][object]" class="form-control editor" placeholder="Objektive Process" aria-describedby="helpId"></textarea>
																		<small class="text-danger invalid-feedback">Objektive Process</small>
																	</div>
																</div>
															</div>
															<div class="col-md-12">
																<div class="form-group">
																	<label class="font-size-h5"><strong><span class="text-danger">*</span> Scope</strong></label>
																	<div class="">
																		<textarea name="bilingual[english][scope]" class="form-control editor" placeholder="Scope" aria-describedby="helpId"></textarea>
																		<small class="text-danger invalid-feedback">Scope</small>
																	</div>
																</div>
															</div>
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="form-group">
																	<label class="font-size-h5"><strong><span class="text-danger">*</span> Indicator Performance</strong></label>
																	<div class="">
																		<textarea name="bilingual[english][performance]" class="form-control editor" placeholder="Indicator Performance" aria-describedby="helpId"></textarea>
																		<small class="text-danger invalid-feedback">Indicator Performance</small>
																	</div>
																</div>
																<div class="form-group">
																	<label class="font-size-h5"><strong>Define</strong></label>
																	<div class="">
																		<textarea name="bilingual[english][define]" class="form-control editor " placeholder="Define" aria-describedby="helpId"></textarea>
																		<small class="text-danger invalid-feedback">Define Proses</small>
																	</div>
																</div>
																<div class="form-group">
																	<label class="font-size-h5"><strong>Responsibility</strong></label>
																	<div class="">
																		<textarea name="bilingual[english][responsibility]" class="form-control editor " placeholder="Responsibility" aria-describedby="helpId"></textarea>
																		<small class="text-danger invalid-feedback">Responsibility</small>
																	</div>
																</div>
																<div class="form-group">
																	<label class="font-size-h5"><strong>General Requirement</strong></label>
																	<div class="">
																		<textarea name="bilingual[english][general_requirement]" class="form-control editor " placeholder="General Requirement" aria-describedby="helpId"></textarea>
																		<small class="text-danger invalid-feedback">General Requirement</small>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<!-- SIPOCOR -->
									<div class="card shadow- border-0 mb-3" style="border-radius: 10px;">
										<div class="card-header bg-light border-0 py-4 cursor-pointer" role="tab" id="sectionSipocor" style="border-radius: 10px;">
											<h4 class="mb-0 font-weight-bolder" data-toggle="collapse" data-parent="#accProcedure" href="#sipocor" aria-expanded="true" aria-controls="sipocor">
												SIPOCOR
											</h4>
										</div>

										<div id="sipocor" class="collapse in" role="tabpanel" aria-labelledby="sectionSipocor">
											<div class="card-body">
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label for="Supplier" class="font-weight-bold font-size-"><strong>1. Supplier</strong></label>
															<div class="">
																<textarea name="supplier" id="supplier" class="form-control editor" placeholder="Supplier" aria-describedby="helpId"></textarea>
																<small class="text-danger invalid-feedback">Supplier</small>
															</div>
														</div>
														<div class="form-group">
															<label for="Proses" class="font-weight-bold font-size-"><strong>3. Proses</strong></label>
															<div class="">
																<textarea name="process" id="process" class="form-control editor" placeholder="Proses" aria-describedby="helpId"></textarea>
																<small class="text-danger invalid-feedback">Proses</small>
															</div>
														</div>
														<div class="form-group">
															<label for="Customer" class="font-weight-bold font-size-"><strong>5. Customer</strong></label>
															<div class="">
																<textarea name="customer" id="customer" class="form-control editor" placeholder="Customer" aria-describedby="helpId"></textarea>
																<small class="text-danger invalid-feedback">Customer</small>
															</div>
														</div>
														<div class="form-group">
															<label for="Risk" class="font-weight-bold font-size-"><strong>7. Risk</strong></label>
															<div class="">
																<textarea name="risk" id="risk" class="form-control editor" placeholder="Risk" aria-describedby="helpId"></textarea>
																<small class="text-danger invalid-feedback">Risk</small>
															</div>
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label for="Input" class="font-weight-bold font-size-"><strong>2. Input</strong></label>
															<div class="">
																<textarea rows="5" name="input" id="input" class="form-control editor" placeholder="Input" aria-describedby="helpId"></textarea>
																<small class="text-danger invalid-feedback">Input</small>
															</div>
														</div>
														<div class="form-group">
															<label for="Output" class="font-weight-bold font-size-"><strong>4. Output</strong></label>
															<div class="">
																<textarea rows="5" name="output" id="output" class="form-control editor" placeholder="Output" aria-describedby="helpId"></textarea>
																<small class="text-danger invalid-feedback">Output</small>
															</div>
														</div>
														<div class="form-group">
															<label for="Objective" class="font-weight-bold font-size-"><strong>6. Objective</strong></label>
															<div class="">
																<textarea rows="5" name="objective" id="objective" class="form-control editor" placeholder="Objective" aria-describedby="helpId"></textarea>
																<small class="text-danger invalid-feedback">Order</small>
															</div>
														</div>
														<div class="form-group">
															<label for="Objective" class="font-weight-bold font-size-"><strong>8. Mitigation</strong></label>
															<div class="">
																<textarea rows="5" name="mitigation" id="mitigation" class="form-control editor" placeholder="Mitigation" aria-describedby="helpId"></textarea>
																<small class="text-danger invalid-feedback">Mitigation</small>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<!-- FLOW IMAGES -->
									<div class="card shadow- border-0 mb-3" style="border-radius: 10px;">
										<div class="card-header bg-light border-0 py-4 cursor-pointer" role="tab" id="sectionFlowImages" style="border-radius: 10px;">
											<h4 class="mb-0 font-weight-bolder" data-toggle="collapse" data-parent="#accProcedure" href="#flowImages" aria-expanded="true" aria-controls="flowImages">
												FLOW IMAGES
											</h4>
										</div>
										<div id="flowImages" class="collapse in" role="tabpanel" aria-labelledby="sectionFlowImages">
											<div class="card-body">
												<h5 class="mb-4">Upload Images</h5>
												<div class="mb-4">
													<div class="preview-zone hidden">
														<div class="box box-solid">
															<div class="box-body d-flex justify-content-start align-items-center">
																<div class="dropzone-wrapper mr-2 d-flex align-items-center" style="width: 150px;">
																	<div class="dropzone-desc">
																		<i class="fa fa-upload"></i>
																		<p>Choose an image file or drag it here.</p>
																	</div>
																	<input type="file" name="img_flow[]" data-index="1" class="dropzone dropzone-1">
																</div>
																<div class="dropzone-wrapper mr-2 d-flex align-items-center" style="width: 150px;">
																	<div class="dropzone-desc">
																		<i class="fa fa-upload"></i>
																		<p>Choose an image file or drag it here.</p>
																	</div>

																	<input type="file" name="img_flow[]" data-index="2" class="dropzone dropzone-2">
																</div>
																<div class="dropzone-wrapper mr-2 d-flex align-items-center" style="width: 150px;">
																	<div class="dropzone-desc">
																		<i class="fa fa-upload"></i>
																		<p>Choose an image file or drag it here.</p>
																	</div>
																	<input type="file" name="img_flow[]" data-index="3" class="dropzone dropzone-3">
																</div>
															</div>
														</div>
													</div>
												</div>
												<h5 class="mb-4">Upload File</h5>
												<div class="mb-4">
													<div class="preview-zone hidden">
														<div class="box box-solid">
															<div class="box-body d-flex justify-content-start align-items-center">
																<div class="dropzone-wrapper mr-2 d-flex align-items-center" style="width: 150px;">
																	<div class="dropzone-desc">
																		<i class="fa fa-upload"></i>
																		<p>Choose an pdf file or drag it here.</p>
																	</div>
																	<input type="file" id="pdf-file" name="flow_file" accept="application/pdf" data-index="1" class="dropzone dropzone-1">
																	<canvas id="pdf-preview" class="d-none" width="150"></canvas>
																</div>
																<!-- <div class="dropzone-wrapper mr-2 d-flex align-items-center" style="width: 150px;">
																			<div class="dropzone-desc">
																				<i class="fa fa-upload"></i>
																				<p>Choose an image file or drag it here.</p>
																			</div>
																			<input type="file" id="pdf-file" name="documents" accept="application/pdf" class="dropzone dropzone-1" />
																			<div class="for-delete">
																				<div class="middle d-flex justify-content-center align-items-center">
																					<button type="button" class="btn btn-sm mr-1 btn-icon btn-warning change-image rounded-circle"><i class="fa fa-edit"></i></button>
																					<button type="button" onclick="remove_image(this)" class="btn btn-sm mr-1 btn-icon btn-danger remove-image rounded-circle"><i class="fa fa-trash"></i></button>
																				</div>
																			</div>
																			<canvas id="pdf-preview" width="150"></canvas>
																		</div> -->

															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<!-- MEDIA VIDEO -->
									<div class="card shadow- border-0 mb-3" style="border-radius: 10px;">
										<div class="card-header bg-light border-0 py-4 cursor-pointer" role="tab" id="sectionMediaVideo" style="border-radius: 10px;">
											<h4 class="mb-0 font-weight-bolder" data-toggle="collapse" data-parent="#accProcedure" href="#MediaVideo" aria-expanded="true" aria-controls="MediaVideo">
												MEDIA VIDEO
											</h4>
										</div>
										<div id="MediaVideo" class="collapse in" role="tabpanel" aria-labelledby="sectionMediaVideo">
											<div class="card-body">
												<div class="mb-3">
													<h4 class="">Embed Video Link</h4>
													<div class="mt-1 mb-2">
														<div class="row">
															<div class="input-group">
																<div class="input-group-prepend">
																	<span class="input-group-text"><i class="fa fa-link"></i></span>
																</div>
																<input type="text" name="link_video" class="form-control" placeholder="Link Video">
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

									<!-- FLOW DETAIL -->
									<div class="card shadow- border-0 mb-3" style="border-radius: 10px;">
										<div class="card-header bg-light border-0 py-4 cursor-pointer" role="tab" id="sectionFlowDetail" style="border-radius: 10px;">
											<h4 class="mb-0 font-weight-bolder" data-toggle="collapse" data-parent="#accProcedure" href="#flowDetail" aria-expanded="true" aria-controls="flowDetail">
												PROSES TERKAIT
											</h4>
										</div>
										<div id="flowDetail" class="collapse in" role="tabpanel" aria-labelledby="sectionFlowDetail">
											<div class="card-body">
												<div class="d-flex justify-content-between align-items-center mb-3">
													<button type="button" class="btn btn-primary btn-sm" id="add_flow"><i class="fa fa-plus mr-2"></i>Tambah Proses</button>
												</div>
												<table class="table table-sm table-condensed table-bordered">
													<thead class="text-center ">
														<tr class="table-light">
															<th width="50">No</th>
															<th>PIC</th>
															<th colspan="2">Deskripsi</th>
															<th>Dokumen Terkait</th>
															<th width="80">Action</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td colspan="5" class="text-center text-muted">~ No data avilable ~</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>

									<!-- APPROVAL -->
									<div class="card border-0 mb-3" style="border-radius: 10px;">
										<div class="card-header bg-light border-0 py-4 cursor-pointer" role="tab" id="sectionApproval" style="border-radius: 10px;">
											<h4 class="mb-0 font-weight-bolder" data-toggle="collapse" data-parent="#accProcedure" href="#approvalDocs" aria-expanded="true" aria-controls="approvalDocs">
												DATA APPROVAL
											</h4>
										</div>
										<div id="approvalDocs" class="collapse in" role="tabpanel" aria-labelledby="sectionApproval">
											<div class="card-body">
												<div class="row">
													<div class="col-6">
														<div class="form-group row">
															<label class="col-12 col-form-label"><span class="text-danger">*</span> Prepared By :</label>
															<div class="col-12">
																<select name="prepared_by" id="prepared_by" class="form-control select2">;
																	<option value=""></option>
																	<?php foreach ($users as $usr) : ?>
																		<option value="<?= $usr->id_user; ?>"><?= $usr->full_name; ?></option>
																	<?php endforeach; ?>
																</select>
																<span class="form-text text-danger invalid-feedback">Prepared By harus di isi</span>
															</div>
														</div>

														<div class="form-group">
															<h5 class="col-12 px-0"><span class="text-danger">*</span> This document requires approval :</h5>
														</div>

														<div class="form-group row">
															<label class="col-lg-3"><span class="text-danger">*</span> Review By</label>
															<div class="col-lg-9">
																<select name="reviewer_id" id="reviewer_id" class="form-control select2">;
																	<option value=""></option>
																	<?php foreach ($jabatan as $jbt) : ?>
																		<option value="<?= $jbt->id; ?>"><?= $jbt->name; ?></option>
																	<?php endforeach; ?>
																</select>
																<span class="form-text text-danger invalid-feedback"><span class="text-danger">*</span> Review By harus di isi</span>
															</div>
														</div>

														<div class="form-group row">
															<label class="col-lg-3"><span class="text-danger">*</span> Approval By</label>
															<div class="col-lg-9">
																<select name="approval_id" id="approval_id" class="form-control select2">;
																	<option value=""></option>
																	<?php foreach ($jabatan as $jbt) : ?>
																		<option value="<?= $jbt->id; ?>"><?= $jbt->name; ?></option>
																	<?php endforeach; ?>
																</select>
																<span class="form-text text-danger invalid-feedback">Approval By harus di isi</span>
															</div>
														</div>

														<div class="form-group row">
															<label class="col-lg-3">Distribusi</label>
															<div class="col-lg-9">
																<select name="distribute_id[]" multiple id="distribute_id" data-placeholder="Choose an options" class="form-control select2">;
																	<option value=""></option>
																	<?php foreach ($depts as $dept) : ?>
																		<option value="<?= $dept->id; ?>"><?= $dept->name; ?></option>
																	<?php endforeach; ?>
																</select>
																<span class="form-text text-danger invalid-feedback">Distribusi By harus di isi</span>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>

								</div>
								<div class="tab-pane fade" id="form" role="tabpanel" aria-labelledby="form-tab">
									<button type="button" class="btn btn-primary mb-3" id="add_form"><i class="fa fa-plus"></i> Add Form</button>
									<table class="table table-bordered table-sm table-condensed">
										<thead>
											<tr>
												<th width="50" class="text-center">No</th>
												<th class="text-center">Name</th>
												<th width="250" class="text-center">Opis</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td colspan="3" class="text-center py-3">
													<h5 class="text-light-secondary">~ No data available~ </h5>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="tab-pane fade" id="guides" role="tabpanel" aria-labelledby="guides-tab">
									<button type="button" class="btn btn-primary mb-3" id="add_guide"><i class="fa fa-plus"></i> Add Guide</button>
									<table class="table table-bordered table-sm table-condensed">
										<thead>
											<tr>
												<th width="50" class="text-center">No</th>
												<th class="text-center">Name</th>
												<th width="250" class="text-center">Opis</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td colspan="3" class="text-center py-3">
													<h5 class="text-light-secondary">~ No data available~ </h5>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
								<div class="tab-pane fade" id="records" role="tabpanel" aria-labelledby="records-tab">
									<button type="button" class="btn btn-primary mb-3" id="add_record"><i class="fa fa-plus"></i> Add Record</button>
									<table class="table table-bordered table-sm table-condensed">
										<thead>
											<tr>
												<th width="50" class="text-center">No</th>
												<th class="text-center">Name</th>
												<th width="250" class="text-center">Opis</th>
											</tr>
										</thead>
										<tbody>
											<tr>
												<td colspan="3" class="text-center py-3">
													<h5 class="text-light-secondary">~ No data available~ </h5>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<hr>
						<div class="d-flex justify-content-between align-items-center mb-3">
							<button type="submit" class="btn btn-primary save"><i class="fa fa-save"></i>Save</button>
							<a href="<?= base_url($this->uri->segment(1)); ?>" class="btn btn-danger"><i class="fa fa-reply"></i>Back</a>
						</div>
					</div>
				</div>

				<!-- Modal -->
				<div class="modal fade" id="modelId" tabindex="-1" data-backdrop="static" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title">Modal title</h5>
								<button type="button" class="close" onclick="$('#content_modal').html('')" data-dismiss="modal" aria-label="Close">
									<span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div class="modal-body" id="content_modal">
							</div>
							<!-- <div class="modal-footer justify-content-between align-items-center">
								<button type="submit" class="btn btn-primary save"><i class="fa fa-save"></i>Save</button>
								<button type="button" class="btn btn-danger" onclick="$('#content_modal').html('')" data-dismiss="modal"><i class="fa fa-times"></i>Cancel</button>
							</div> -->
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	$(document).ready(function() {
		$('.select2').select2({
			placeholder: "Choose an options",
			width: "100%",
			allowClear: true
		})

		function handlePromise(promiseList) {
			return promiseList.map(promise =>
				promise.then((res) => ({
					status: 'ok',
					res
				}), (err) => ({
					status: 'not ok',
					err
				}))
			)
		}
		Promise.allSettled = function(promiseList) {
			return Promise.all(handlePromise(promiseList))
		}

		$(document).on('click', '#add_flow', function() {
			const proc_id = $(this).data('id')
			const url = siteurl + active_controller + 'add_flow/' + proc_id
			$('#content_modal').load(url)
			$('#modelId').modal('show')
		})

		$(document).on('click', '#wadd_flow', function() {
			let html = `
			<div class="form-group">
				<label class="">Nomor</label>
				<div class="">
					<input type="text" name="flow[number]" id="number" class="form-control" required placeholder="Nomor" value="">
					<small class="text-danger invalid-feedback">Nomor</small>
				</div>
			</div>
			<div class="form-group">
				<label class="">PIC/Penanggung Jawab</label>
				<div class="">
					<input type="text" name="flow[pic]" id="pic" class="form-control" required placeholder="PIC" aria-describedby="helpId">
					<small class="text-danger invalid-feedback">PIC</small>
				</div>
			</div>
			<div class="form-group">
				<label for="description" class="">Deskripsi</label>
				<div class="">
					<textarea rows="5" name="flow[description]" id="description" class="form-control" placeholder="Deskripsi" aria-describedby="helpId"></textarea>
					<small class="text-danger invalid-feedback">Deskripsi</small>
				</div>
			</div>
			<div class="form-group">
				<label class="">Dok. Terkait</label>
				<div class="">
					<textarea rows="5" name="flow[relate_doc]" id="relate_doc" class="form-control" required placeholder="Dokumen terkait" aria-describedby="helpId" /></textarea>
					<small class="text-danger invalid-feedback">Dokumen terkait</small>
				</div>
			</div> 
			`;

			$('#content_modal').html(html)
			$('#modelId').modal('show')
		})

		$(document).on('submit', '#form-procedure', function(e) {
			e.preventDefault();
			let formdata = new FormData($(this)[0])
			let btn = $('.save')

			$('#description').removeClass('is-invalid')
			$('#prepared_by').removeClass('is-invalid')
			$('#approval_id').removeClass('is-invalid')
			$('#reviewer_id').removeClass('is-invalid')
			$('#distribute_id').removeClass('is-invalid')
			$('#image').removeClass('is-invalid')
			$('#group_procedure').removeClass('is-invalid')
			$('#status').removeClass('is-invalid')
			$('#name').removeClass('is-invalid')
			$('#scope').removeClass('is-invalid')
			$('#object').removeClass('is-invalid')
			$('#performance').removeClass('is-invalid')



			const group_procedure = $('#group_procedure').val();
			const description = $('#description').val();
			const prepared_by = $('#prepared_by').val();
			const reviewer_id = $('#reviewer_id').val();
			const approval_id = $('#approval_id').val();
			const distribute_id = $('#distribute_id').val();
			const id_master = $('#id_master').val();
			const image = $('#image').val();
			const parent_id = $('#parent_id').val();
			const status = $('#status').val();
			const name = $('#name').val()
			const scope = $('#scope').val()
			const object = $('#object').val()
			const performance = $('#performance').val()

			if (group_procedure !== undefined && (group_procedure == '' || group_procedure == null)) {
				Swal.fire({
					title: "Error Message!",
					text: 'Empty Group Procedure, please input Group Procedure  first.....',
					icon: "warning"
				});
				$('#group_procedure').addClass('is-invalid')
				// $('#approvalDocs').addClass('show');
				return false;
			}
			if (status !== undefined && (status == '' || status == null)) {
				Swal.fire({
					title: "Error Message!",
					text: 'Empty Status, please input Status first.....',
					icon: "warning"
				});
				$('#status').addClass('is-invalid')
				// $('#approvalDocs').addClass('show');
				return false;
			}
			if (name !== undefined && (name == '' || name == null)) {
				Swal.fire({
					title: "Error Message!",
					text: 'Empty Name Procedure, please input Name Procedure first.....',
					icon: "warning"
				});
				$('#name').addClass('is-invalid')
				// $('#approvalDocs').addClass('show');
				return false;
			}
			if (scope !== undefined && (scope == '' || scope == null)) {
				Swal.fire({
					title: "Error Message!",
					text: 'Empty Ruang Lingkup, please input Ruang Lingkup first.....',
					icon: "warning"
				});
				$('#scope').addClass('is-invalid')
				// $('#approvalDocs').addClass('show');
				return false;
			}
			if (object !== undefined && (object == '' || object == null)) {
				Swal.fire({
					title: "Error Message!",
					text: 'Empty Objective Process, please input Objective Process first.....',
					icon: "warning"
				});
				$('#object').addClass('is-invalid')
				// $('#approvalDocs').addClass('show');
				return false;
			}
			if (performance !== undefined && (performance == '' || performance == null)) {
				Swal.fire({
					title: "Error Message!",
					text: 'Empty Performa Indikator, please input Performa Indikator first.....',
					icon: "warning"
				});
				$('#performance').addClass('is-invalid')
				// $('#approvalDocs').addClass('show');
				return false;
			}
			if (prepared_by !== undefined && (prepared_by == '' || prepared_by == null)) {
				Swal.fire({
					title: "Error Message!",
					text: 'Empty User Prepared, please input User Prepared  first.....',
					icon: "warning"
				});
				$('#prepared_by').addClass('is-invalid')
				$('#approvalDocs').addClass('show');
				return false;
			}
			if ((reviewer_id == '' && reviewer_id != undefined) || (reviewer_id == null && reviewer_id != undefined)) {
				Swal.fire({
					title: "Error Message!",
					text: 'Empty reviewer, please input reviewer first.....',
					icon: "warning"
				});
				$('#reviewer_id').addClass('is-invalid')
				$('#approvalDocs').addClass('show');
				return false;
			}
			if ((approval_id == '' && approval_id != undefined) || (approval_id == null && approval_id != undefined)) {
				Swal.fire({
					title: "Error Message!",
					text: 'Empty approval, please input approval first!',
					icon: "warning"
				});
				$('#approval_id').addClass('is-invalid')
				$('#approvalDocs').addClass('show');
				return false;
			}

			// if ((distribute_id == '' && distribute_id != undefined) || (distribute_id == null && distribute_id != undefined)) {
			// 	$('#distribute_id').addClass('is-invalid')
			// 	Swal.fire({
			// 		title: "Error Message!",
			// 		text: 'Empty distribusi, please input distribusi first.....',
			// 		icon: "warning"
			// 	});
			// 	$('#approvalDocs').addClass('show');
			// 	return false;
			// }


			$.ajax({
				url: siteurl + active_controller + 'save',
				data: formdata,
				type: 'POST',
				dataType: 'JSON',
				processData: false,
				contentType: false,
				cache: false,
				beforeSend: function() {
					btn.attr('disabled', true)
					btn.html('<i class="spinner spinner-border-sm"></i>Loading...')
				},
				complete: function() {
					btn.attr('disabled', false)
					btn.html('<i class="fa fa-save"></i>Save')
				},
				success: function(result) {
					if (result.status == 1) {
						Swal.fire({
							title: 'Success!',
							icon: 'success',
							text: result.msg,
							timer: 2000
						})
						$('#modelId').modal('hide')
						location.href = siteurl + active_controller + 'edit/' + result.id
					} else {
						Swal.fire({
							title: 'Warning!',
							icon: 'warning',
							text: result.msg,
							timer: 2000
						})
					}
				},
				error: function(result) {
					Swal.fire({
						title: 'Error!',
						icon: 'error',
						text: 'Server timeout, becuase error!',
						timer: 4000
					})
				}
			})
		})
	})

	function readFile(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();

			index = $(input).data('index')

			reader.onload = function(e) {
				console.log(e)
				var htmlPreview = '<img width="150" src="' + e.target.result + '" />';

				var overlay = `<div class="middle d-flex justify-content-center align-items-center">
				<button type="button" onclick="$(this).parent().parent().find('.dropzone').click()" class="btn btn-sm mr-1 btn-icon btn-warning change-image rounded-circle"><i class="fa fa-edit"></i></button>
				<button type="button" onclick="remove_image(this)" class="btn btn-sm mr-1 btn-icon btn-danger remove-image rounded-circle"><i class="fa fa-trash"></i></button>
				</div>`;
				var wrapperZone = $(input).parent();
				var previewZone = $(input).parent().parent().find('.preview-zone');
				var boxZone = $(input).parent().find('.dropzone-desc');

				wrapperZone.removeClass('dragover');
				previewZone.removeClass('hidden');
				boxZone.html('');
				boxZone.append(htmlPreview);
				wrapperZone.find('.middle').remove();
				wrapperZone.append(overlay);
			};

			reader.readAsDataURL(input.files[0]);
		}
	}

	function reset(e) {
		// e.wrap('<form>').closest('form').get(0).reset();
		// e.unwrap();
	}

	function remove_image(e) {
		Swal.fire({
			title: 'Are you sure to delete this data?',
			icon: 'question',
			showCancelButton: true,
			confirmButtonColor: '#DD6B55',
			confirmButtonText: 'Yes, Delete <i class="fa fa-trash text-white"></i>',
		}).then((value) => {
			let srcFile = $(e).parent().parent().find('.dropzone-desc').find('img').attr('src')
			$(e).parent().parent().find('input.dropzone').val();
			$(e).parent().parent().find('input.dropzone').off();
			$(e).parent().parent().find('.dropzone-desc').empty().append('<i class="fa fa-upload"></i><p> Choose an image file or drag it here. </p>');
			// $(e).parent().parent().find('.for-delete').empty().append('<input type="hidden" name="delete_image[]" value="' + srcFile + '">');
			$(e).parent().remove();
		})

		// $(e).parent().parent().find('input.dropzone').val();
		// $(e).parent().parent().find('input.dropzone').off();
		// $(e).parent().parent().find('.dropzone-desc').empty().append('<i class="fa fa-upload"></i><p> Choose an image file or drag it here. </p>');
		// $(e).parent().remove();

	}

	$(document).on('change', ".dropzone", function() {
		readFile(this);
	});

	$('.dropzone-wrapper').on('dragover', function(e) {
		e.preventDefault();
		e.stopPropagation();
		$(this).addClass('dragover');
	});

	$('.dropzone-wrapper').on('dragleave', function(e) {
		e.preventDefault();
		e.stopPropagation();
		$(this).removeClass('dragover');
	});

	$('.remove-preview').on('click', function() {
		var boxZone = $(this).parents('.preview-zone').find('.box-body');
		var previewZone = $(this).parents('.preview-zone');
		var dropzone = $(this).parents('.form-group').find('.dropzone');
		boxZone.empty();
		previewZone.addClass('hidden');
		reset(dropzone);
	});


	/* PDF FILE */

	var _PDF_DOC,
		_CANVAS = document.querySelector('#pdf-preview'),
		_OBJECT_URL;

	function showPDF(pdf_url) {

		PDFJS.getDocument({
			url: pdf_url
		}).then(function(pdf_doc) {
			_PDF_DOC = pdf_doc;

			// Show the first page
			showPage(1);

			// destroy previous object url
			URL.revokeObjectURL(_OBJECT_URL);
		}).catch(function(error) {
			// trigger Cancel on error
			$("#cancel-pdf").click();

			// error reason
			alert(error.message);
		});;
	}

	function showPage(page_no) {
		var _CANVAS = document.querySelector('#pdf-preview');
		// fetch the page
		console.log(page_no);
		console.log(_PDF_DOC.getPage(page_no));
		_PDF_DOC.getPage(page_no).then(function(page) {
			// set the scale of viewport
			var scale_required = _CANVAS.width / page.getViewport(1).width;

			// get viewport of the page at required scale
			var viewport = page.getViewport(scale_required);

			// set canvas height
			_CANVAS.height = viewport.height;

			var renderContext = {
				canvasContext: _CANVAS.getContext('2d'),
				viewport: viewport
			};

			// render the page contents in the canvas
			page.render(renderContext).then(function() {
				$("#pdf-preview").css('display', 'inline-block');
				$("#pdf-loader").css('display', 'none');
			});
		});
	}

	$(document).on('click', '.change-image', function() {
		$('#pdf-file').click()
	})

	/* Selected File has changed */
	$(document).on('change', "#pdf-file", function() {
		// user selected file
		console.log($(this));
		var file = $(this)[0].files[0];

		// allowed MIME types
		var mime_types = ['application/pdf'];

		// Validate whether PDF
		if (mime_types.indexOf(file.type) == -1) {
			alert('Error : Incorrect file type');
			return;
		}

		// validate file size
		if (file.size > 10 * 1024 * 1024) {
			alert('Error : Exceeded size 10MB');
			return;
		}

		// validation is successful

		// hide upload dialog button
		$("#upload-dialog").css('display', 'none');

		// set name of the file
		// $("#pdf-name").text(file.name);
		// $("#pdf-name").css('display', 'inline-block');

		// show cancel and upload buttons now
		$("#cancel-pdf").removeClass('d-none');
		$("#pdf-preview").removeClass('d-none');
		$("#remove-file").removeClass('d-none');
		// $("#upload-button").css('display', 'inline-block');

		// Show the PDF preview loader
		$("#pdf-loader").css('display', 'inline-block');

		// object url of PDF 
		// console.log(file);
		_OBJECT_URL = URL.createObjectURL(file)

		// send the object url of the pdf to the PDF preview function
		showPDF(_OBJECT_URL);
	});

	/* Reset file input */
	$(document).on('click', "#cancel-pdf,.remove-image", function() {
		// show upload dialog button
		$("#upload-dialog").css('display', 'inline-block');

		// reset to no selection
		$("#pdf-file").val('');

		// hide elements that are not required
		$("#pdf-name").css('display', 'none');
		// $("#pdf-preview").css('display', 'none');
		$("#pdf-preview").addClass('d-none');
		$("#pdf-loader").css('display', 'none');
		$("#cancel-pdf").addClass('d-none');
		$("#upload-button").css('display', 'none');
	});

	$(document).on('click', "#remove-file", function() {
		// show upload dialog button
		$("#upload-dialog").css('display', 'inline-block');
		$("#remove-document").val('x');

		// reset to no selection
		$("#pdf-file").val('');

		// hide elements that are not required
		$("#pdf-name").css('display', 'none');
		$("#pdf-preview").addClass('d-none');
		$("#pdf-loader").css('display', 'none');
		$("#cancel-pdf").addClass('d-none');
		$("#upload-button").css('display', 'none');
	});

	$(document).on('click', '#add_form', function() {
		const id = $('#procedure_id').val() || null;
		$('#modelId').modal('show')
		$('.modal-title').text('Add Form')
		$('#content_modal').load(siteurl + active_controller + 'upload_form/' + id)

	})

	$(document).on('click', '#add_guide', function() {
		const id = $('#procedure_id').val() || null;
		$('#modelId').modal('show')
		$('.modal-title').text('Add IK')
		$('#content_modal').load(siteurl + active_controller + 'upload_guide/' + id)

	})
	$(document).on('click', '#add_record', function() {
		const id = $('#procedure_id').val() || null;
		$('#modelId').modal('show')
		$('.modal-title').text('Add Record')
		$('#content_modal').load(siteurl + active_controller + 'upload_record/' + id)

	})
</script>