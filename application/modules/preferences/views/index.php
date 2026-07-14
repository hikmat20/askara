<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="card card-stretch shadow card-custom">
                <div class="card-header">
                    <h2 class="mt-5"><i class="<?= $icon; ?> mr-2"></i><?= $title; ?></h2>
                </div>
                <div class="card-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs nav-pills pb-3 border-0" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="reviewer-tab" data-toggle="tab" href="#reviewer" role="tab" aria-controls="reviewer" aria-selected="true">
                                <span class="nav-icon"><i class="fa fa-user-check"></i></span>
                                <span class="nav-text">Default Reviewer</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="future-tab" data-toggle="tab" href="#future" role="tab" aria-controls="future" aria-selected="false">
                                <span class="nav-icon"><i class="fa fa-cogs"></i></span>
                                <span class="nav-text">Pengaturan Lain</span>
                            </a>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content mt-5" id="myTabContent">
                        
                        <!-- Tab: Default Reviewer -->
                        <div class="tab-pane fade show active" id="reviewer" role="tabpanel" aria-labelledby="reviewer-tab">
                            <form id="form-preferences">
                                <div class="form-group row">
                                    <label class="col-form-label col-3">Default Reviewer Procedure</label>
                                    <div class="col-4">
                                        <select name="default_reviewer_procedure" id="default_reviewer_procedure" class="form-control select2">
                                            <option value=""></option>
                                            <?php foreach ($positions as $pos) : ?>
                                                <option value="<?= $pos->id; ?>" <?= (isset($settings['default_reviewer_procedure']) && $settings['default_reviewer_procedure'] == $pos->id) ? 'selected' : ''; ?>>
                                                    <?= $pos->name; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-3"></div>
                                    <div class="col-4">
                                        <button type="button" class="btn btn-primary font-weight-bolder" id="save-preferences">
                                            <i class="fa fa-save"></i> Save Preferences
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Tab: Future Settings -->
                        <div class="tab-pane fade" id="future" role="tabpanel" aria-labelledby="future-tab">
                            <div class="alert alert-custom alert-light-primary fade show mb-5" role="alert">
                                <div class="alert-icon"><i class="flaticon-warning"></i></div>
                                <div class="alert-text">Pengaturan lainnya akan ditambahkan di sini pada pengembangan berikutnya.</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: 'Select an option',
            width: '100%',
            allowClear: true
        });

        $('#save-preferences').click(function(e) {
            e.preventDefault();
            
            var formData = $('#form-preferences').serialize();
            var btn = $(this);
            
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to save these preferences?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Save it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.attr('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
                    
                    $.ajax({
                        url: siteurl + 'preferences/save',
                        type: 'POST',
                        dataType: 'json',
                        data: formData,
                        success: function(res) {
                            btn.attr('disabled', false).html('<i class="fa fa-save"></i> Save Preferences');
                            
                            if (res.status == 1) {
                                Swal.fire('Success!', res.msg, 'success');
                            } else {
                                Swal.fire('Warning!', res.msg, 'warning');
                            }
                        },
                        error: function() {
                            btn.attr('disabled', false).html('<i class="fa fa-save"></i> Save Preferences');
                            Swal.fire('Error!', 'Server error occurred.', 'error');
                        }
                    });
                }
            });
        });
    });
</script>
