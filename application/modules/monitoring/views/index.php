<div class="content d-flex flex-column flex-column-fluid p-0">
    <div class="container mt-5">
        <h1 class="text-white pt-0 font-weight-bolder bg-white-o-50 d-inline text-center rounded-lg px-5 py-1">
            MONITORING DOCUMENTS</h1>
        <div class="pt-3 mt-4">
            <h3 class="text-white pt-0 font-weight-bolder bg-white-o-0 rounded-lg px-0 py-1">Procedures</h3>
            <div class="d-flex justify-content-start align-items-center">
                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg" style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <!-- <img src="<?= base_url('assets/images/dashboard/prosedur.png'); ?>" alt="List Procedure" class="img-fluid" style="height: 150px;"> -->
                            <h5 class="font-weight-bolder text-success" style="font-size: 48px;"><?= $dtProcedureRev; ?>
                            </h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . "/review"); ?>" class="text-hover-primary"
                                title="REVIEW DOCUMENTS">
                                <span class="card-label text-dark text-center font-weight-bolder">REVIEW DOCUMENTS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-warning" style="font-size: 48px;"><?= $dtProcedureCor; ?>
                            </h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . "/correction"); ?>"
                                class="text-hover-primary" title="CORRECTION DOCUMENTS">
                                <span class="card-label text-dark text-center font-weight-bolder">CORRECTION DOCUMENTS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-info" style="font-size: 48px;"><?= $dtProcedureApv; ?>
                            </h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . "/approval"); ?>" class="text-hover-primary"
                                title="APPROVAL DOCUMENTS">
                                <span class="card-label text-dark text-center font-weight-bolder">APPROVAL DOCUMENTS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-danger" style="font-size: 48px;"><?= $dtProcedureRvi; ?>
                            </h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . "/revision"); ?>" class="text-hover-primary"
                                title="REVISION DOCUMENT">
                                <span class="card-label text-dark text-center font-weight-bolder">REVISION DOCUMENTS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-primary" style="font-size: 48px;"><?= $dtProcedurePub; ?>
                            </h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . "/publised"); ?>" class="text-hover-primary"
                                title="PUBLISH DOCUMENT">
                                <span class="card-label text-dark text-center font-weight-bolder">PUBLISH DOCUMENTS</span>
                            </a>
                        </h6>
                    </div>
                </div>
    
            </div>
        </div>

        <div class="pt-3">
            <h3 class="text-white pt-0 font-weight-bolder bg-white-o-0 rounded-lg px-0 py-1">Forms</h3>
            <div class="d-flex justify-content-start align-items-center">
                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg" style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-success" style="font-size: 48px;"><?= $dtFormRev; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url('monitoring/forms_review'); ?>" class="text-hover-primary"
                                title="REVIEW FORMS">
                                <span class="card-label text-dark text-center font-weight-bolder">REVIEW FORMS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-warning" style="font-size: 48px;"><?= $dtFormCor; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url('monitoring/forms_correction'); ?>" class="text-hover-primary"
                                title="CORRECTION FORMS">
                                <span class="card-label text-dark text-center font-weight-bolder">CORRECTION FORMS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-info" style="font-size: 48px;"><?= $dtFormApv; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url('monitoring/forms_approval'); ?>" class="text-hover-primary"
                                title="APPROVAL FORMS">
                                <span class="card-label text-dark text-center font-weight-bolder">APPROVAL FORMS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-danger" style="font-size: 48px;"><?= $dtFormRvi; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url('monitoring/forms_revision'); ?>" class="text-hover-primary"
                                title="REVISION FORMS">
                                <span class="card-label text-dark text-center font-weight-bolder">REVISION FORMS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-primary" style="font-size: 48px;"><?= $dtFormPub; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url('monitoring/forms_published'); ?>" class="text-hover-primary"
                                title="PUBLISHED FORMS">
                                <span class="card-label text-dark text-center font-weight-bolder">PUBLISHED FORMS</span>
                            </a>
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-3">
            <h3 class="text-white pt-0 font-weight-bolder bg-white-o-0 rounded-lg px-0 py-1">Work Instructions</h3>
            <div class="d-flex justify-content-start align-items-center">
                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg" style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-success" style="font-size: 48px;"><?= $dtWiRev; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url('monitoring/wi_review'); ?>" class="text-hover-primary"
                                title="REVIEW WORK INSTRUCTIONS">
                                <span class="card-label text-dark text-center font-weight-bolder">REVIEW WORK INSTRUCTIONS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-warning" style="font-size: 48px;"><?= $dtWiCor; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url('monitoring/wi_correction'); ?>" class="text-hover-primary"
                                title="CORRECTION WORK INSTRUCTIONS">
                                <span class="card-label text-dark text-center font-weight-bolder">CORRECTION WORK INSTRUCTIONS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-info" style="font-size: 48px;"><?= $dtWiApv; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url('monitoring/wi_approval'); ?>" class="text-hover-primary"
                                title="APPROVAL WORK INSTRUCTIONS">
                                <span class="card-label text-dark text-center font-weight-bolder">APPROVAL WORK INSTRUCTIONS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-danger" style="font-size: 48px;"><?= $dtWiRvi; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url('monitoring/wi_revision'); ?>" class="text-hover-primary"
                                title="REVISION WORK INSTRUCTIONS">
                                <span class="card-label text-dark text-center font-weight-bolder">REVISION WORK INSTRUCTIONS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-primary" style="font-size: 48px;"><?= $dtWiPub; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url('monitoring/wi_published'); ?>" class="text-hover-primary"
                                title="PUBLISHED WORK INSTRUCTIONS">
                                <span class="card-label text-dark text-center font-weight-bolder">PUBLISHED WORK INSTRUCTIONS</span>
                            </a>
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-3 d-none">
            <h3 class="text-white mt-0 pt-0 font-weight-bolder bg-white-o-0 rounded-lg px-0 py-1">Other Document</h3>
            <div class="d-flex justify-content-start align-items-center">

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg" style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <!-- <img src="<?= base_url('assets/images/dashboard/prosedur.png'); ?>" alt="List Procedure" class="img-fluid" style="height: 150px;"> -->
                            <h5 class="font-weight-bolder text-success" style="font-size: 48px;"><?= $dtGuidesApv; ?>
                            </h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . "/review"); ?>" class="text-hover-primary"
                                title="REVIEW DOCUMENTS">
                                <span class="card-label text-dark text-center font-weight-bolder">REVIEW
                                    DOCUMENTS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-warning" style="font-size: 48px;"><?= $dtGuidesRev; ?>
                            </h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . "/correction"); ?>"
                                class="text-hover-primary" title="CORRECTION DOCUMENTS">
                                <span class="card-label text-dark text-center font-weight-bolder">CORRECTION
                                    DOCUMENTS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-info" style="font-size: 48px;"><?= $dtGuidesCor; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . "/approval"); ?>" class="text-hover-primary"
                                title="APPROVAL DOCUMENTS">
                                <span class="card-label text-dark text-center font-weight-bolder">APPROVAL
                                    DOCUMENTS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-danger" style="font-size: 48px;"><?= $dtGuidesRvi; ?>
                            </h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . "/revision"); ?>" class="text-hover-primary"
                                title="REVISION DOCUMENT">
                                <span class="card-label text-dark text-center font-weight-bolder">REVISION
                                    DOCUMENTS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-primary" style="font-size: 48px;"><?= $dtGuidesPub; ?>
                            </h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . "/publised"); ?>" class="text-hover-primary"
                                title="PUBLISH DOCUMENT">
                                <span class="card-label text-dark text-center font-weight-bolder">PUBLISH
                                    DOCUMENTS</span>
                            </a>
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <h1 class="text-white pt-0 font-weight-bolder bg-white-o-50 d-inline text-center rounded-lg px-5 py-1">DELETION
            DOCUMENTS</h1>
        <div class="pt-3 mt-4">
            <h3 class="text-white pt-0 font-weight-bolder bg-white-o-0 rounded-lg px-0 py-1">Procedures</h3>            <div class="d-flex justify-content-start align-items-center">
                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg" style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <!-- <img src="<?= base_url('assets/images/dashboard/prosedur.png'); ?>" alt="List Procedure" class="img-fluid" style="height: 150px;"> -->
                            <h5 class="font-weight-bolder text-warning" style="font-size: 48px;"><?= $hld; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . "/review_deletion"); ?>"
                                class="text-hover-primary" title="REVIEW DOCUMENTS">
                                <span class="card-label text-dark text-center font-weight-bolder">REVIEW
                                    DOCUMENTS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-danger" style="font-size: 48px;"><?= $revDel; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . "/approval_deletion"); ?>"
                                class="text-hover-primary" title="APPROVAL DOCUMENTS">
                                <span class="card-label text-dark text-center font-weight-bolder">APPROVAL DELETION
                                    DOCUMENTS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-muted" style="font-size: 48px;"><?= $apvDel; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . "/deletion_document"); ?>"
                                class="text-hover-primary" title="NEED ACTION TO DELETE DOCUMENTS">
                                <span class="card-label text-dark text-center font-weight-bolder">NEED ACTION TO DELETE
                                    DOCUMENTS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg " style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-success" style="font-size: 48px;"><?= $rejDel; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . "/rejected_document"); ?>"
                                class="text-hover-primary" title="APPROVAL DOCUMENTS">
                                <span class="card-label text-dark text-center font-weight-bolder">REJECTED DELETION
                                    DOCUMENTS</span>
                            </a>
                        </h6>
                    </div>
                </div>

            </div>
        </div>

        <!-- DELETION FORMS -->
        <div class="pt-3 mt-4">
            <h3 class="text-white pt-0 font-weight-bolder bg-white-o-0 rounded-lg px-0 py-1">Forms</h3>
            <div class="d-flex justify-content-start align-items-center">
                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg" style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-warning" style="font-size: 48px;"><?= $dtFormDelOPN; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . '/forms_review_deletion'); ?>" class="text-hover-primary"
                                title="REVIEW DELETION FORMS">
                                <span class="card-label text-dark text-center font-weight-bolder">REVIEW DELETION FORMS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg" style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-danger" style="font-size: 48px;"><?= $dtFormDelAPV; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . '/forms_approval_deletion'); ?>" class="text-hover-primary"
                                title="APPROVAL DELETION FORMS">
                                <span class="card-label text-dark text-center font-weight-bolder">APPROVAL DELETION FORMS</span>
                            </a>
                        </h6>
                    </div>
                </div>
            </div>
        </div>

        <!-- DELETION WORK INSTRUCTIONS -->
        <div class="pt-3 mt-4">
            <h3 class="text-white pt-0 font-weight-bolder bg-white-o-0 rounded-lg px-0 py-1">Work Instructions</h3>
            <div class="d-flex justify-content-start align-items-center">
                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg" style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-warning" style="font-size: 48px;"><?= $dtWiDelREV; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . '/wi_review_deletion'); ?>" class="text-hover-primary"
                                title="REVIEW DELETION WORK INSTRUCTIONS">
                                <span class="card-label text-dark text-center font-weight-bolder">REVIEW DELETION WORK INSTRUCTIONS</span>
                            </a>
                        </h6>
                    </div>
                </div>

                <div class="w-250px mr-5 mb-lg-5">
                    <div class="card border-0 shadow-lg rounded-lg" style="background-color: rgba(255, 255, 255,100);">
                        <div class="card-body p-2 text-center">
                            <h5 class="font-weight-bolder text-danger" style="font-size: 48px;"><?= $dtWiDelAPV; ?></h5>
                            <p>Documents</p>
                        </div>
                        <h6 class="card-title text-center px-4">
                            <a href="<?= base_url($this->uri->segment(1) . '/wi_approval_deletion'); ?>" class="text-hover-primary"
                                title="APPROVAL DELETION WORK INSTRUCTIONS">
                                <span class="card-label text-dark text-center font-weight-bolder">APPROVAL DELETION WORK INSTRUCTIONS</span>
                            </a>
                        </h6>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<style>
    p {
        margin-bottom: 0;
    }
</style>