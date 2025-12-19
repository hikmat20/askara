 <!--begin::Footer-->
 <div class="footer bg-transparent  d-flex flex-lg-column" id="kt_footer">
     <!--begin::Container-->
     <div class="container d-flex flex-column flex-md-row align-items-center justify-content-center">
         <!--begin::Copyright-->
         <div class="text-dark order-2 text-center order-md-1">
             <span class="text-muted  font-weight-bold mr-2">Sentral Sistem© <?= date('Y'); ?></span>
             <!-- <a href="http://keenthemes.com/keen" target="_blank" class="text-dark-75 text-hover-primary">Keenthemes</a> -->
         </div>
         <!--end::Copyright-->
         <!--begin::Nav-->
         <div class="nav nav-dark order-1 order-md-2">
             <!-- <a href="http://keenthemes.com/keen" target="_blank" class="nav-link pr-3 pl-0">About</a>
             <a href="http://keenthemes.com/keen" target="_blank" class="nav-link px-3">Team</a>
             <a href="http://keenthemes.com/keen" target="_blank" class="nav-link pl-3 pr-0">Contact</a> -->
         </div>
         <!--end::Nav-->
     </div>
     <!--end::Container-->
 </div>
 <!--end::Footer-->
 </div>
 <!--end::Wrapper-->
 </div>
 <!--end::Page-->
 </div>
 <!--end::Main-->
 <!-- begin::User Panel-->
 <div id="kt_quick_user" class="offcanvas offcanvas-right p-10">
     <!--begin::Header-->
     <div class="offcanvas-header d-flex align-items-top justify-content-between pb-5">
         <h4>My Account</h4>
         <a href="#" class="btn btn-xs btn-icon btn-light btn-hover-primary" id="kt_quick_user_close">
             <i class="ki ki-close icon-xs text-muted"></i>
         </a>
     </div>
     <div class="separator separator-dashed mt-3 mb-3"></div>
     <!--end::Header-->
     <!--begin::Content-->
     <div class="offcanvas-content pr-5 mr-n5">
         <!--begin::Header-->
         <div class="d-flex align-items-center mt-5">
             <div class="symbol symbol-100 mr-5">
                 <div class="symbol-label" style="background-image:url(<?= (isset($userData->photo) && file_exists('assets/img/avatar/' . $userData->photo)) ? base_url('assets/img/avatar/' . $userData->photo) : base_url('assets/img/avatar/no-user.jpg'); ?>)"></div>
                 <i class="symbol-badge bg-success"></i>
             </div>
             <div class="d-flex flex-column">
                 <a href="#" class="font-weight-bold font-size-h5 text-dark-75 text-hover-primary"><?= isset($userData->full_name) ? ucwords($userData->full_name) : '-'; ?></span></a>
                 <div class="text-muted mt-1">
                     <?= isset($userData->groups) ? $userData->groups : '-'; ?>
                 </div>
                 <div class="navi mt-1">
                     <a href="#" class="navi-item">
                         <span class="navi-link p-0 pb-2">
                             <span class="navi-icon mr-1">
                                 <span class="svg-icon svg-icon-lg svg-icon-primary">
                                     <!--begin::Svg Icon | path:/keen/theme/demo6/dist/assets/media/svg/icons/Communication/Mail-notification.svg-->
                                     <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                         <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                             <rect x="0" y="0" width="24" height="24" />
                                             <path d="M21,12.0829584 C20.6747915,12.0283988 20.3407122,12 20,12 C16.6862915,12 14,14.6862915 14,18 C14,18.3407122 14.0283988,18.6747915 14.0829584,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,12.0829584 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000" />
                                             <circle fill="#000000" opacity="0.3" cx="19.5" cy="17.5" r="2.5" />
                                         </g>
                                     </svg>
                                     <!--end::Svg Icon-->
                                 </span>
                             </span>
                             <span class="navi-text text-muted text-hover-primary"><?= ($userData->email) ?: '-'; ?></span>
                         </span>
                     </a>
                 </div>
             </div>
         </div>
         <!--end::Header-->
         <!--begin::Separator-->
         <div class="separator separator-dashed mt-3 mb-5"></div>
         <!--end::Separator-->
         <!--begin::Nav-->
         <div class="navi navi-spacer-x-0 p-0">
             <!--begin::Item-->
             <!-- <a href="custom/apps/user/profile-1/personal-information.html" class="navi-item">
                 <div class="navi-link">
                     <div class="symbol symbol-40 bg-light mr-3">
                         <div class="symbol-label">
                             <span class="svg-icon svg-icon-md svg-icon-danger">
                                 <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                     <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                         <rect x="0" y="0" width="24" height="24" />
                                         <path d="M18,2 L20,2 C21.6568542,2 23,3.34314575 23,5 L23,19 C23,20.6568542 21.6568542,22 20,22 L18,22 L18,2 Z" fill="#000000" opacity="0.3" />
                                         <path d="M5,2 L17,2 C18.6568542,2 20,3.34314575 20,5 L20,19 C20,20.6568542 18.6568542,22 17,22 L5,22 C4.44771525,22 4,21.5522847 4,21 L4,3 C4,2.44771525 4.44771525,2 5,2 Z M12,11 C13.1045695,11 14,10.1045695 14,9 C14,7.8954305 13.1045695,7 12,7 C10.8954305,7 10,7.8954305 10,9 C10,10.1045695 10.8954305,11 12,11 Z M7.00036205,16.4995035 C6.98863236,16.6619875 7.26484009,17 7.4041679,17 C11.463736,17 14.5228466,17 16.5815,17 C16.9988413,17 17.0053266,16.6221713 16.9988413,16.5 C16.8360465,13.4332455 14.6506758,12 11.9907452,12 C9.36772908,12 7.21569918,13.5165724 7.00036205,16.4995035 Z" fill="#000000" />
                                     </g>
                                 </svg>
                             </span>
                         </div>
                     </div>
                     <div class="navi-text">
                         <div class="font-weight-bold">My Account</div>
                         <div class="text-muted">Profile info
                             <span class="label label-light-danger label-inline font-weight-bold">update</span>
                         </div>
                     </div>
                 </div>
             </a> -->
             <a href="<?= base_url('users/logout'); ?>" class="navi-item">
                 <div class="navi-link">
                     <div class="symbol symbol-40 bg-light mr-3">
                         <div class="symbol-label">
                             <span class="svg-icon svg-icon-md svg-icon-primary">
                                 <span class="svg-icon svg-icon-2x">
                                     <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                         <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                             <rect x="0" y="0" width="24" height="24"></rect>
                                             <path d="M14.0069431,7.00607258 C13.4546584,7.00607258 13.0069431,6.55855153 13.0069431,6.00650634 C13.0069431,5.45446114 13.4546584,5.00694009 14.0069431,5.00694009 L15.0069431,5.00694009 C17.2160821,5.00694009 19.0069431,6.7970243 19.0069431,9.00520507 L19.0069431,15.001735 C19.0069431,17.2099158 17.2160821,19 15.0069431,19 L3.00694311,19 C0.797804106,19 -0.993056895,17.2099158 -0.993056895,15.001735 L-0.993056895,8.99826498 C-0.993056895,6.7900842 0.797804106,5 3.00694311,5 L4.00694793,5 C4.55923268,5 5.00694793,5.44752105 5.00694793,5.99956624 C5.00694793,6.55161144 4.55923268,6.99913249 4.00694793,6.99913249 L3.00694311,6.99913249 C1.90237361,6.99913249 1.00694311,7.89417459 1.00694311,8.99826498 L1.00694311,15.001735 C1.00694311,16.1058254 1.90237361,17.0008675 3.00694311,17.0008675 L15.0069431,17.0008675 C16.1115126,17.0008675 17.0069431,16.1058254 17.0069431,15.001735 L17.0069431,9.00520507 C17.0069431,7.90111468 16.1115126,7.00607258 15.0069431,7.00607258 L14.0069431,7.00607258 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.006943, 12.000000) scale(-1, 1) rotate(-90.000000) translate(-9.006943, -12.000000) "></path>
                                             <rect fill="#000000" opacity="0.3" transform="translate(14.000000, 12.000000) rotate(-270.000000) translate(-14.000000, -12.000000) " x="13" y="6" width="2" height="12" rx="1"></rect>
                                             <path d="M21.7928932,9.79289322 C22.1834175,9.40236893 22.8165825,9.40236893 23.2071068,9.79289322 C23.5976311,10.1834175 23.5976311,10.8165825 23.2071068,11.2071068 L20.2071068,14.2071068 C19.8165825,14.5976311 19.1834175,14.5976311 18.7928932,14.2071068 L15.7928932,11.2071068 C15.4023689,10.8165825 15.4023689,10.1834175 15.7928932,9.79289322 C16.1834175,9.40236893 16.8165825,9.40236893 17.2071068,9.79289322 L19.5,12.0857864 L21.7928932,9.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(19.500000, 12.000000) rotate(-90.000000) translate(-19.500000, -12.000000) "></path>
                                         </g>
                                     </svg>
                                     <!--end::Svg Icon-->
                                 </span>
                         </div>
                     </div>
                     <div class="navi-text">
                         <div class="font-weight-bold">Logout</div>
                     </div>
                 </div>
             </a>
         </div>
         <!--end::Nav-->
     </div>
     <!--end::Content-->
 </div>
 <!-- end::User Panel-->

 <!--begin::Scrolltop-->
 <div id="kt_scrolltop" class="scrolltop">
     <span class="svg-icon">
         <!--begin::Svg Icon | path:/keen/theme/demo6/dist/assets/media/svg/icons/Navigation/Up-2.svg-->
         <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
             <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                 <polygon points="0 0 24 0 24 24 0 24" />
                 <rect fill="#000000" opacity="0.3" x="11" y="10" width="2" height="10" rx="1" />
                 <path d="M6.70710678,12.7071068 C6.31658249,13.0976311 5.68341751,13.0976311 5.29289322,12.7071068 C4.90236893,12.3165825 4.90236893,11.6834175 5.29289322,11.2928932 L11.2928932,5.29289322 C11.6714722,4.91431428 12.2810586,4.90106866 12.6757246,5.26284586 L18.6757246,10.7628459 C19.0828436,11.1360383 19.1103465,11.7686056 18.7371541,12.1757246 C18.3639617,12.5828436 17.7313944,12.6103465 17.3242754,12.2371541 L12.0300757,7.38413782 L6.70710678,12.7071068 Z" fill="#000000" fill-rule="nonzero" />
             </g>
         </svg>
         <!--end::Svg Icon-->
     </span>
 </div>
 <!--end::Scrolltop-->
 <div id="Processing"></div>
 <div id="ajaxFailed"></div>

 <script>
     var KTAppSettings = {
         "breakpoints": {
             "sm": 576,
             "md": 768,
             "lg": 992,
             "xl": 1200,
             "xxl": 1200
         },
         "colors": {
             "theme": {
                 "base": {
                     "white": "#ffffff",
                     "primary": "#0BB783",
                     "secondary": "#E5EAEE",
                     "success": "#1BC5BD",
                     "info": "#8950FC",
                     "warning": "#FFA800",
                     "danger": "#F64E60",
                     "light": "#F3F6F9",
                     "dark": "#212121"
                 },
                 "light": {
                     "white": "#ffffff",
                     "primary": "#D7F9EF",
                     "secondary": "#ECF0F3",
                     "success": "#C9F7F5",
                     "info": "#EEE5FF",
                     "warning": "#FFF4DE",
                     "danger": "#FFE2E5",
                     "light": "#F3F6F9",
                     "dark": "#D6D6E0"
                 },
                 "inverse": {
                     "white": "#ffffff",
                     "primary": "#ffffff",
                     "secondary": "#212121",
                     "success": "#ffffff",
                     "info": "#ffffff",
                     "warning": "#ffffff",
                     "danger": "#ffffff",
                     "light": "#464E5F",
                     "dark": "#ffffff"
                 }
             },
             "gray": {
                 "gray-100": "#F3F6F9",
                 "gray-200": "#ECF0F3",
                 "gray-300": "#E5EAEE",
                 "gray-400": "#D6D6E0",
                 "gray-500": "#B5B5C3",
                 "gray-600": "#80808F",
                 "gray-700": "#464E5F",
                 "gray-800": "#1B283F",
                 "gray-900": "#212121"
             }
         },
         "font-family": "Poppins"
     };
 </script>
 <!-- REQUIRED JS SCRIPTS -->
 <!-- <script src="<?= base_url('assets/plugins/jQuery/jquery-2.2.3.min.js'); ?>"></script> -->
 <script src="<?= base_url(); ?>themes/dashboard/assets/plugins/global/plugins.bundle1036.js?"></script>
 <script src="<?= base_url(); ?>themes/dashboard/assets/plugins/custom/prismjs/prismjs.bundle1036.js?"></script>
 <script src="<?= base_url(); ?>themes/dashboard/assets/js/scripts.bundle1036.js?"></script>
 <script src="<?= base_url(); ?>themes/dashboard/assets/plugins/custom/fullcalendar/fullcalendar.bundle1036.js?"></script>
 <script src="<?= base_url(); ?>themes/dashboard/assets/plugins/custom/datatables/datatables.bundle1036.js"></script>
 <script src="<?= base_url(); ?>themes/dashboard/assets/plugins/custom/jstree/jstree.bundle.js?"></script>
 <!-- <script src="<?= base_url(); ?>assets/js/scripts.js" type="text/javascript"></script> -->
 <!-- <script src="<?= base_url(); ?>assets/plugins/jqueryform/jquery.form.js"></script> -->
 <!-- <script src="<?= base_url(); ?>assets/dist/sweetalert.min.js"></script> -->
 <script src="<?= base_url('themes\dashboard\assets\plugins\custom\select2\select21036.js'); ?>"></script>
 <script src="<?= base_url(); ?>themes/dashboard/assets/js/pages/widgets1036.js?"></script>
 <script src="<?= base_url(); ?>themes\dashboard\assets\plugins\custom\summernote\summernote-bs4.min.js"></script>
 <script src="<?= base_url(); ?>themes/dashboard/assets/plugins/custom/tinymce/tinymce.min.js"></script>
 <!-- <script src="https://cdn.tiny.cloud/1/jou4no6cbvv6kyct0kcjoumfc81n00cy2rnwk7wbidnj1d57/tinymce/8/tinymce.min.js" referrerpolicy="origin"></script> -->
 <!-- <script src="<?= base_url('assets\plugins\tinymce\tinymce.js'); ?>"></script> -->


 <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
 <script src="<?= base_url(); ?>themes/dashboard/assets/plugins/custom/jquery-ui/jquery-ui.min.js"></script>
 <script src="<?= base_url('themes/dashboard/assets/plugins/custom/monthpicker/MonthPicker.js'); ?>"></script>
 <script src="https://cdn.rawgit.com/digitalBush/jquery.maskedinput/1.4.1/dist/jquery.maskedinput.min.js"></script>

 <script>
     $(document).ready(function() {
         tinymce.init({
             selector: '.editor',
             height: 250,
             resize: true,
             indent: '10px',
             toolbar_mode: 'sliding',
             statusbar: true,
             branding: false,
             promotion: false,
             autoresize_bottom_margin: 50,
             link_default_protocol: 'https',
             menubar: false,
             plugins: [
                 'preview', 'anchor', 'accordion', 'advlist', 'paste', 'anchor', 'code', 'image', 'link', 'lists', 'table',
             ],
             toolbar: 'undo redo | paste pastetext fontsize| ' +
                 'bold italic backcolor forecolor | alignleft aligncenter ' +
                 'alignright alignjustify | bullist numlist outdent indent | table tabledelete | link ' +
                 'removeformat | help',
             content_style: `body { font-family:Helvetica,Arial,sans-serif; font-size:14px }`
         });

         $('.select2').select2({
             placeholder: 'Choose an options',
             width: '100%',
             allowClear: true
         })

         $('textarea.summernote').summernote({
             height: 250, // set editor height
             minHeight: null, // set minimum height of editor
             maxHeight: null,
             inheritPlaceholder: true,
             toolbar: [
                 ['style', ['style']],
                 ['edit', ['undo', 'redo']],
                 ['font', ['bold', 'italic', 'underline', 'clear']],
                 ['fontname', ['fontname']],
                 ['fontsize', ['fontsize', 'height']],
                 ['color', ['color']],
                 ['para', ['ul', 'ol', 'paragraph']],
                 ['table', ['table']],
                 ['insert', ['link', 'picture', 'video']],
                 ['view', ['fullscreen', 'help']],
             ],
         });
         //  loading_spinner()
     })

     function loading_spinner() {
         Swal.fire({
             title: 'Please wait...!',
             allowOutsideClick: false,
             showConfirmButton: false,
             html: `<div class="loaders">
                    <div class="dot dot-1"></div>
                    <div class="dot dot-2"></div>
                    <div class="dot dot-3"></div>
                    <div class="dot dot-4"></div>
                    <div class="dot dot-5"></div>
                    </div>`
             //  didOpen: () => {
             //      Swal.showLoading()
             //  }
         })
     }

     function getValidation(f = '') {
         var form = f;
         var count = 0;
         var success = true;
         $('input,select,textarea,file').removeClass('is-invalid')
         $('span.select2-selection').css('border-color', '');

         $("form" + f + " .required").each(function() {
             var node = $(this).prop('nodeName');
             var type = $(this).prop('type');
             var success = true;

             if (!$(this).is(':disabled')) {
                 if ((node == 'INPUT' && type == 'radio') || (node == 'INPUT' && type == 'checkbox')) {
                     //  $(this).parents('div.form-group').removeClass('validated')
                     $(this).removeClass('is-invalid')
                     var c = 0;
                     $("input[name='" + $(this).attr('name') + "']").each(function() {
                         if ($(this).prop('checked') == true) {
                             c++;
                         }
                     });

                     if (c == 0) {
                         var name = $(this).attr('name');
                         //  var id = $(this).attr('id');
                         //  $('.' + name).removeClass('hideIt');
                         //  $('.' + name).css('display', 'inline-block');
                         //  $(this).parents('div.form-group').addClass('validated')
                         $(this).addClass('is-invalid').focus()
                         count = count + 1;
                     }
                 }

                 if ((node == 'INPUT' && type == 'text') || (node == 'INPUT' && type == 'password') || (node == 'SELECT') || (node == 'TEXTAREA') || (node == 'INPUT' && type == 'date') || (node == 'INPUT' && type == 'file')) {
                     if (($(this).val() == null || $(this).val() == '')) {
                         const id = $(this).prop('id')
                         $(this).addClass('is-invalid').focus()
                         $('span[aria-labelledby=select2-' + id + '-container].select2-selection').css('border-color', 'red');
                         $('span.select2-selection.select2-selection--multiple').css('border-color', 'red');
                         count = count + 1;
                         //  console.log(name);
                     }
                 }
             }
         });
         console.log(count);
         if (count == 0) {
             return success;
         } else {
             return false;
         }
     }
 </script>
 </body>

 </html>