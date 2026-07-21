<form id="form" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= isset($data) ? $data->id : ''; ?>">
    <input type="hidden" name="old_file" value="<?= isset($data) ? $data->file_name : ''; ?>">

    <div class="form-group">
        <label>Upload Manual Book (PDF Only) <?= isset($data) ? '<small class="text-danger">*Biarkan kosong jika tidak ingin mengubah file</small>' : ''; ?></label>
        
        <div id="drop-zone" class="drop-zone p-5 border-dashed text-center rounded" style="border: 2px dashed #007bff; cursor: pointer;">
            <i class="fa fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
            <h5>Drag and drop your PDF here</h5>
            <p class="text-muted mb-2">Or click to browse, or paste file</p>
            <input type="file" name="document" id="document" class="d-none" accept=".pdf">
            <div id="file-name-display" class="mt-2 text-success font-weight-bold">
                <?= isset($data) ? 'Current File: ' . $data->file_name : ''; ?>
            </div>
        </div>
        <small class="form-text text-muted mb-4">Only .pdf files are allowed.</small>
    </div>

    <div class="form-group">
        <label>Deskripsi Update</label>
        <textarea name="description" id="description" class="form-control" rows="3" placeholder="Tuliskan catatan update (misal: penambahan panduan login)"><?= isset($data) ? $data->description : ''; ?></textarea>
    </div>
</form>

<script>
$(document).ready(function() {
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('document');
    const fileNameDisplay = document.getElementById('file-name-display');

    dropZone.addEventListener('click', () => fileInput.click());

    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('bg-light');
    });

    dropZone.addEventListener('dragleave', (e) => {
        dropZone.classList.remove('bg-light');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('bg-light');
        if (e.dataTransfer.files.length) {
            handleFiles(e.dataTransfer.files);
        }
    });

    document.addEventListener('paste', (e) => {
        if ($('#modalView').hasClass('show') && e.clipboardData.files.length) {
            handleFiles(e.clipboardData.files);
        }
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length) {
            handleFiles(e.target.files);
        }
    });

    function handleFiles(files) {
        const file = files[0];
        if (file.type !== 'application/pdf') {
            Swal.fire('Error', 'Only PDF files are allowed.', 'error');
            fileInput.value = '';
            fileNameDisplay.textContent = '';
            return;
        }

        // Assign file to input
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        fileInput.files = dataTransfer.files;

        fileNameDisplay.textContent = 'Selected: ' + file.name;
    }
});
</script>
