<form id="form-permission">
    <div class="content d-flex flex-column flex-column-fluid p-0">
        <div class="d-flex flex-column-fluid justify-content-between align-items-top">
            <div class="container mt-10">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0"><i class="fa fa-key mr-2"></i>Permission Menu</h4>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <label class="col-2">Group Name</label>
                            <div class="col-auto">:

                                <input type="hidden" name="group_id" value="<?= $group->id_group; ?>">
                                <input type="hidden" name="company_id" value="<?= $group->company_id; ?>">
                                <span class="font-weight-bolder"><?= $group->nm_group; ?></span>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-2">Description</label>
                            <div class="col-auto">:
                                <span><?= $group->ket; ?></span>
                            </div>
                        </div>
                        <table class="table table-hover">
                            <thead>
                                <tr class="table-light">
                                    <th>Menus</th>
                                    <th class="text-center" width="120px">Read</th>
                                    <th class="text-center" width="120px">Create</th>
                                    <th class="text-center" width="120px">Update</th>
                                    <th class="text-center" width="120px">Delete</th>
                                    <th class="text-center" width="120px">Download</th>
                                </tr>
                            </thead>
                            <?= $this->menu_generator->group_menus(); ?>
                        </table>
                    </div>
                    <div class="d-flex justify-content-between align-items-center card-footer">
                        <a href="<?= base_url($this->uri->segment(1)); ?>" class="btn btn-danger"><i class="fa fa-reply"></i>Back</a>
                        <button type="button" class="btn btn-primary save"><i class="fa fa-save"></i>Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<script>
    $(document).ready(function() {
        $(document).on('change', '.parent', function() {
            const action = $(this).data('action')
            const check = $(this).is(':checked')
            const id = $(this).data('id')
            if (check) {
                $('.child-' + action + '-' + id).prop('checked', true).val('1').change()
            } else {
                $('.child-' + action + '-' + id).prop('checked', false).val('0')
            }
        })

        $(document).on('change', '.child', function() {
            const action = $(this).data('action')
            const parent = $(this).data('parent')
            const check = $('.child-' + action + '-' + parent).is(':checked')
            count = 0
            $('.child-' + action + '-' + parent).each(function() {
                count += Number($(this).is(':checked'))
            })
            if (count > 0) {
                $('.parent-' + action + '-' + parent).prop('checked', true).val('1')
            } else {
                $('.parent-' + action + '-' + parent).prop('checked', false).val('0')
            }
        })

        $(document).on('click', '.save', function() {
            const formdata = new FormData($('#form-permission')[0])
            Swal.fire({
                title: "Are you sure?",
                text: "You will not be able to process again this data!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, Process it!",
                cancelButtonText: "No, cancel process!",
            }).then((value) => {
                if (value.isConfirmed) {
                    var baseurl = siteurl + active_controller + 'save_permission';
                    $.ajax({
                        url: baseurl,
                        type: "POST",
                        data: formdata,
                        dataType: 'json',
                        contentType: false,
                        processData: false,
                        cache: false,
                        success: function(data) {
                            if (data.status == 1) {
                                Swal.fire({
                                    title: "Success!",
                                    text: data.msg,
                                    icon: "success",
                                    timer: 3000,
                                    showCancelButton: false,
                                    showConfirmButton: false,
                                    allowOutsideClick: false
                                });
                                $('#upload').modal('hide')
                                $('#data-file').load(siteurl + active_controller + 'load_file/' + parent_id)
                            } else {
                                if (data.status == 0) {
                                    Swal.fire({
                                        title: "Failed!",
                                        html: data.msg,
                                        icon: "warning",
                                        timer: 3000,
                                    });
                                }
                            }
                        },
                        error: function() {
                            Swal.fire({
                                title: "Error Message !",
                                text: 'An Error Occured During Process. Please try again..',
                                icon: "warning",
                                timer: 3000,
                            });
                        }
                    });
                }
            });
        })

    })
</script>