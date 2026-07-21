<div class="content d-flex flex-column flex-column-fluid" id="kt_content">
    <div class="d-flex flex-column-fluid">
        <div class="container">
            <div class="card card-stretch shadow card-custom">
                <div class="card-header">
                    <h2 class="mt-5"><i class="<?= $icon; ?> mr-2"></i><?= $title; ?></h2>
                    <div class="mt-4 float-right ">
                        <button type="button" class="btn btn-primary" id="add" title="Upload New Manual Book">
                            <i class="fa fa-upload mr-1"></i>Upload Manual Book
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="example1" class="table table-bordered table-striped table-sm">
                            <thead class="text-center table-light">
                                <tr>
                                    <th width="50">No</th>
                                    <th>File Name</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Uploaded At</th>
                                    <th width="200">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $n=1; foreach($data as $dt): ?>
                                <tr>
                                    <td class="text-center"><?= $n++; ?></td>
                                    <td>
                                        <a href="#" class="view-pdf" data-url="<?= base_url('assets/files/'.$dt->file_name); ?>#toolbar=0&navpanes=0&scrollbar=0">
                                            <?= $dt->file_name; ?>
                                        </a>
                                    </td>
                                    <td><?= nl2br(htmlspecialchars($dt->description)); ?></td>
                                    <td class="text-center">
                                        <?php if($dt->status == 'Y'): ?>
                                            <span class="badge badge-success">Active</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Inactive</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center"><?= $dt->created_on; ?></td>
                                    <td class="text-center">
                                        <?php if($dt->status != 'Y'): ?>
                                            <button type="button" class="btn btn-sm btn-success set-active" data-id="<?= $dt->id; ?>" title="Set as Active">
                                                <i class="fa fa-check"></i> Set Active
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-primary edit" data-id="<?= $dt->id; ?>" title="Edit">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete" data-id="<?= $dt->id; ?>" title="Delete">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalView" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Upload Manual Book</h5>
                <span class="close btn-cls" data-dismiss="modal" aria-label="Close"></span>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer justify-content-end">
                <button type="button" class="btn btn-primary save w-100px"><i class="fa fa-save"></i>Save</button>
                <button type="button" class="btn btn-danger text-end" onclick="$('#modalView .modal-body').empty();setTimeout(()=>{$('.save').removeClass('d-none')},500)" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal View PDF -->
<div class="modal fade" id="modalViewPdf" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Manual Book</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body p-0" style="height: 80vh;">
                <iframe id="pdfViewer" src="" width="100%" height="100%" frameborder="0" style="border:0;" allowfullscreen oncontextmenu="return false;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#example1').DataTable();

    $(document).on('click', '#add', function() {
        const url = siteurl + active_controller + 'add';
        $('#modalView .modal-title').html('Upload Manual Book')
        $('#modalView').modal('show')
        $('#modalView .modal-body').load(url)
    })

    $(document).on('click', '.edit', function() {
        const id = $(this).data('id')
        const url = siteurl + active_controller + 'edit/' + id;
        $('#modalView .modal-title').html('Edit Manual Book')
        $('#modalView').modal('show')
        $('#modalView .modal-body').load(url)
    })

    $(document).on('click', '.view-pdf', function(e) {
        e.preventDefault();
        const url = $(this).data('url');
        $('#pdfViewer').attr('src', url);
        $('#modalViewPdf').modal('show');
    })

    $(document).on('click', '.save', function(e) {
        let fileInput = $('#form-manual #document')[0];
        let idInput = $('#form-manual input[name="id"]').val();
        
        if (!idInput && fileInput.files.length === 0) {
            Swal.fire('Warning', 'Please select a PDF file first.', 'warning');
            return;
        }

        let formdata = new FormData($('#form-manual')[0])
        let btn = $(this)
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
                    }).then(function() {
                        $('#modalView').modal('hide')
                        location.reload();
                    })
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
                    text: 'Server timeout or error!',
                    timer: 4000
                })
            }
        })
    })

    $(document).on('click', '.set-active', function(e) {
        const id = $(this).data('id')
        const btn = $(this)
        Swal.fire({
            title: 'Set Active!',
            icon: 'question',
            text: 'Are you sure you want to set this as the active manual book?',
            showCancelButton: true,
        }).then((value) => {
            if (value.isConfirmed) {
                $.ajax({
                    url: siteurl + active_controller + 'set_active',
                    data: { id },
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) {
                        if (result.status == 1) {
                            Swal.fire('Success!', result.msg, 'success').then(() => location.reload())
                        } else {
                            Swal.fire('Warning!', result.msg, 'warning')
                        }
                    }
                })
            }
        })
    })

    $(document).on('click', '.delete', function(e) {
        const id = $(this).data('id')
        const btn = $(this)
        Swal.fire({
            title: 'Delete!',
            icon: 'question',
            text: 'Are you sure to delete this data?',
            showCancelButton: true,
        }).then((value) => {
            if (value.isConfirmed) {
                $.ajax({
                    url: siteurl + active_controller + 'delete',
                    data: { id },
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(result) {
                        if (result.status == 1) {
                            Swal.fire('Success!', result.msg, 'success').then(() => location.reload())
                        } else {
                            Swal.fire('Warning!', result.msg, 'warning')
                        }
                    }
                })
            }
        })
    })
})

function clear(e) {
    setTimeout(() => { $(e).html(''); }, 500)
}
</script>
