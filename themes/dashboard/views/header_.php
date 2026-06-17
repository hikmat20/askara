<!DOCTYPE html>
<html lang="en">
<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
  <meta charset="utf-8" />
  <title><?= isset($idt->nm_perusahaan) ? $idt->nm_perusahaan : 'not-set'; ?><?= isset($template['title']) ? ' | ' . $template['title'] : ''; ?></title>
  <meta name="description" content="Updates and statistics" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <link rel="shortcut icon" href="<?= base_url('assets/logo/'.$this->session->company->id_perusahaan.'/'.$this->session->company->logo); ?>" />

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
  <link href="<?= base_url(); ?>themes/dashboard/assets/plugins/global/plugins.bundle1036.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>themes/dashboard/assets/css/style.bundle1036.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>themes/dashboard/assets/plugins/custom/prismjs/prismjs.bundle1036.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>themes/dashboard/assets/plugins/custom/fullcalendar/fullcalendar.bundle1036.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>themes/dashboard/assets/plugins/custom/jstree/jstree.bundle.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>themes/dashboard/assets/plugins/custom/datatables/datatables.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>themes/dashboard/assets/plugins/custom/monthpicker/MonthPicker.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  <script src="<?= base_url('themes\dashboard\assets\plugins\global\jquery-3.3.1.min.js'); ?>"></script>
  <link href="<?= base_url(); ?>themes\dashboard\assets\plugins\custom\jquery-ui\jquery-ui.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>themes\dashboard\assets\plugins\custom\summernote\summernote-bs4.min.css" rel="stylesheet" type="text/css" />
  <link href="<?= base_url(); ?>themes\dashboard\assets\css\loading-skeleton.css" rel="stylesheet" type="text/css" />

  <script src="<?= base_url('themes/dashboard/assets/plugins/custom/pdf/pdf.js'); ?>"></script>
  <script src="<?= base_url('themes/dashboard/assets/plugins/custom/pdf/pdf.worker.js'); ?>"></script>

  <script type="text/javascript">
    var baseurl = "<?= base_url(); ?>";
    var siteurl = "<?php echo site_url(); ?>";
    var base_url = siteurl;
    var active_controller = '<?php echo $this->uri->segment(1); ?>' + '/';
    var active_function = '<?php echo $this->uri->segment(2); ?>' + '/';


    $(document).ready(function() {
      Object.assign(DataTable.defaults, {
        responsive: true,
        autoWidth: false,
        destroy: true,
        processing: true,
        lengthChange: true,
        stateSave: true,
        stateDuration: 60,
        deferLoading: 20,
        layout: {
          topEnd: {
            search: {
              placeholder: 'Search'
            }
          },
          topStart: 'pageLength',
          bottomStart: 'info',
          bottomEnd: 'paging',
        },
        aaSorting: [
          [0, "asc"]
        ],
        lengthMenu: [10, 20, 25, 50, 100, 250, 500, 1000, {
          label: 'All',
          value: 99999999
        }],
        pageLength: 20,
        pagingType: 'simple_numbers',
        language: {
          search: "",
          sLengthMenu: "_MENU_",
          sInfo: "Showing <b>_START_</b> to <b>_END_</b> from <b>_TOTAL_</b> data",
          sInfoFiltered: "(filtered from _MAX_ total entries)",
          oPaginate: {
            sPrevious: "<i class='fa fa-chevron-left' aria-hidden='true'></i>",
            sNext: "<i class='fa fa-chevron-right' aria-hidden='true'></i>"
          }
        },
        columnDefs: [{
          "targets": 'no-sort',
          "orderable": false,
        }, {
          "targets": 'text-center',
          "className": 'text-center',
        }, {
          "targets": 'text-right',
          "className": 'text-right',
        }, {
          "targets": 'fw-bold',
          "className": 'font-wight-bold',
        }, {
          "targets": 'text-center-bold',
          "className": 'text-center font-wight-bold',
        }],
        responsive: {
          "breakpoints": [{
              "name": 'desktop',
              "width": Infinity
            },
            {
              "name": 'tablet',
              "width": 1148
            },
            {
              "name": 'mobile',
              "width": 680
            },
            {
              "name": 'mobile-p',
              "width": 320
            }
          ],
        },
      });


    })
  </script>


  <style>
    .swal2-loader {
      width: 10.2em;
      height: 10.2em;
      -webkit-animation: swal2-rotate-loading .8s linear 0s infinite normal;
      animation: swal2-rotate-loading .8s linear 0s infinite normal;
      border-width: .8em;
      border-color: #704dff #704dff #704dff #bbabff;
    }

    .swal2-container .swal2-html-container {
      min-height: 50px !important;
      overflow: visible !important;
      display: flex !important;
      justify-content: center !important;
    }

    .box {
      position: relative;
      background: #ffffff;
      width: 100%;
    }

    .box-header {
      color: #444;
      display: block;
      padding: 10px;
      position: relative;
      border-bottom: 1px solid #f4f4f4;
      margin-bottom: 10px;
    }

    .box-tools {
      position: absolute;
      right: 10px;
      top: 5px;
    }

    .dropzone-wrapper {
      border: 2px dashed #91b0b3;
      color: #92b0b3;
      position: relative;
      height: 150px;
      border-radius: 10px;
      transition: .5s ease;
      overflow: hidden;
    }

    .dropzone-desc {
      margin: 0 auto;
      text-align: center;
      font-size: 12px;
      overflow: hidden;
    }

    .dropzone-desc p {
      padding: 0 10px;
    }

    .dropzone,
    .dropzone:focus {
      position: absolute;
      outline: none !important;
      width: 100%;
      height: 150px;
      cursor: pointer;
      opacity: 0;
    }

    .dropzone-wrapper:hover,
    .dropzone-wrapper.dragover {
      background: #ecf0f5;
    }

    .middle {
      top: 0;
      left: 0;
      transition: .5s ease;
      opacity: 0;
      position: absolute;
      width: 100%;
      height: 100%;
      background-color: #3f3f3fa3;
    }



    .dropzone-wrapper:hover .middle {
      opacity: 1;
    }

    .dropzone-wrapper:hover .dropzone-wrapper {
      background-color: #444;
    }

    .preview-zone {
      text-align: center;
    }

    .preview-zone .box {
      box-shadow: none;
      border-radius: 0;
      margin-bottom: 0;
    }

    .aside {
      left: -300px;
      width: 300px;
    }

    .loaders {
      display: -webkit-box;
      display: -ms-flexbox;
      display: flex;
      -webkit-box-pack: center;
      -ms-flex-pack: center;
      justify-content: center;
      -webkit-box-align: center;
      -ms-flex-align: center;
      align-items: center;
      height: 100%;
    }

    .dot {
      display: inline-block;
      width: 10px;
      height: 10px;
      margin-right: 6px;
      border-radius: 50%;
      -webkit-animation: dot-pulse2 1.5s ease-in-out infinite;
      animation: dot-pulse2 1.5s ease-in-out infinite;
    }

    .dot-1 {
      background-color: #4285f4;
      -webkit-animation-delay: 0s;
      animation-delay: 0s;
    }

    .dot-2 {
      background-color: #34a853;
      -webkit-animation-delay: 0.3s;
      animation-delay: 0.3s;
    }

    .dot-3 {
      background-color: #fbbc05;
      -webkit-animation-delay: 0.6s;
      animation-delay: 0.6s;
    }

    .dot-4 {
      background-color: #ea4335;
      -webkit-animation-delay: 0.9s;
      animation-delay: 0.9s;
    }

    .dot-5 {
      background-color: #4285f4;
      -webkit-animation-delay: 1.2s;
      animation-delay: 1.2s;
    }

    @keyframes dot-pulse2 {
      0% {
        -webkit-transform: scale(0.5);
        transform: scale(0.5);
        opacity: 0.5;
      }

      50% {
        -webkit-transform: scale(1);
        transform: scale(1);
        opacity: 1;
      }

      100% {
        -webkit-transform: scale(0.5);
        transform: scale(0.5);
        opacity: 0.5;
      }
    }

    .tox-tinymce {
      height: 0px;
    }
  </style>
</head>
<!-- 0c18a9 -->

<body id="kt_body" class="header-fixed header-mobile-fixed aside-enabled aside-static page-loading" style="background-image: url(<?= base_url('assets/images/bg-primary.png'); ?>);background-repeat:repeat-y;background-size:cover;background-position:top;background-attachment:cover">
  <div id="kt_header_mobile" class="header-mobile header-mobile-fixed">
    <a href="<?= base_url('/'); ?>">
      <img alt="Logo" src="<?= base_url('assets/logo/') . $this->session->company->logo; ?>" class="max-h-30px" />
    </a>
    <div class="d-flex align-items-center">
      <button class=" btn btn-icon" onclick="$('#kt_aside_toggle').click()">
        <span class="svg-icon svg-icon-xl">
          <!--begin::Svg Icon | path:/keen/theme/demo6/dist/assets/media/svg/icons/Text/Toggle-Left.svg-->
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <rect x="0" y="0" width="24" height="24" />
              <path d="M2 11.5C2 12.3284 2.67157 13 3.5 13H20.5C21.3284 13 22 12.3284 22 11.5V11.5C22 10.6716 21.3284 10 20.5 10H3.5C2.67157 10 2 10.6716 2 11.5V11.5Z" fill="black" />
              <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd" d="M9.5 20C8.67157 20 8 19.3284 8 18.5C8 17.6716 8.67157 17 9.5 17H20.5C21.3284 17 22 17.6716 22 18.5C22 19.3284 21.3284 20 20.5 20H9.5ZM15.5 6C14.6716 6 14 5.32843 14 4.5C14 3.67157 14.6716 3 15.5 3H20.5C21.3284 3 22 3.67157 22 4.5C22 5.32843 21.3284 6 20.5 6H15.5Z" fill="black" />
            </g>
          </svg>
          <!--end::Svg Icon-->
        </span>
      </button>
      <!--begin::Quick Actions-->
      <div class="topbar-item" data-offset="10px,0px">
        <a href="<?= base_url('users/logout'); ?>">
          <div class="btn btn-icon btn-hover-transparent-white btn-dropdown btn-lg mr-1" title="Log Out">
            <span class="svg-icon svg-icon-2x">
              <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <rect x="0" y="0" width="24" height="24" />
                  <path d="M14.0069431,7.00607258 C13.4546584,7.00607258 13.0069431,6.55855153 13.0069431,6.00650634 C13.0069431,5.45446114 13.4546584,5.00694009 14.0069431,5.00694009 L15.0069431,5.00694009 C17.2160821,5.00694009 19.0069431,6.7970243 19.0069431,9.00520507 L19.0069431,15.001735 C19.0069431,17.2099158 17.2160821,19 15.0069431,19 L3.00694311,19 C0.797804106,19 -0.993056895,17.2099158 -0.993056895,15.001735 L-0.993056895,8.99826498 C-0.993056895,6.7900842 0.797804106,5 3.00694311,5 L4.00694793,5 C4.55923268,5 5.00694793,5.44752105 5.00694793,5.99956624 C5.00694793,6.55161144 4.55923268,6.99913249 4.00694793,6.99913249 L3.00694311,6.99913249 C1.90237361,6.99913249 1.00694311,7.89417459 1.00694311,8.99826498 L1.00694311,15.001735 C1.00694311,16.1058254 1.90237361,17.0008675 3.00694311,17.0008675 L15.0069431,17.0008675 C16.1115126,17.0008675 17.0069431,16.1058254 17.0069431,15.001735 L17.0069431,9.00520507 C17.0069431,7.90111468 16.1115126,7.00607258 15.0069431,7.00607258 L14.0069431,7.00607258 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.006943, 12.000000) scale(-1, 1) rotate(-90.000000) translate(-9.006943, -12.000000) " />
                  <rect fill="#000000" opacity="0.3" transform="translate(14.000000, 12.000000) rotate(-270.000000) translate(-14.000000, -12.000000) " x="13" y="6" width="2" height="12" rx="1" />
                  <path d="M21.7928932,9.79289322 C22.1834175,9.40236893 22.8165825,9.40236893 23.2071068,9.79289322 C23.5976311,10.1834175 23.5976311,10.8165825 23.2071068,11.2071068 L20.2071068,14.2071068 C19.8165825,14.5976311 19.1834175,14.5976311 18.7928932,14.2071068 L15.7928932,11.2071068 C15.4023689,10.8165825 15.4023689,10.1834175 15.7928932,9.79289322 C16.1834175,9.40236893 16.8165825,9.40236893 17.2071068,9.79289322 L19.5,12.0857864 L21.7928932,9.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(19.500000, 12.000000) rotate(-90.000000) translate(-19.500000, -12.000000) " />
                </g>
              </svg>
            </span>
          </div>
        </a>
      </div>
      <!--end::Quick Actions-->

      <!--begin::User-->
      <div class="topbar-item mr-3">
        <div class="btn btn-icon btn-hover-transparent-white w-auto d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle">
          <div class="symbol symbol-circle symbol-30 bg-white overflow-hidden">
            <div class="symbol-label">
              <img alt="avatar" src="<?= (isset($userData->photo) && file_exists('/assets/img/avatar/' . $userData->photo)) ? base_url('/assets/img/avatar/' . $userData->photo) : base_url('/assets/img/avatar/no-user.jpg'); ?>" class="h-75 align-self-end" />
            </div>
          </div>
        </div>
      </div>
      <!--end::User-->
    </div>
  </div>
  <!--begin::Aside-->
  <div class="aside aside-left d-flex flex-column flex-row-auto" id="kt_aside">
    <!--begin::Aside Menu-->
    <div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
      <!--begin::Menu Container-->
      <div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
        <!--begin::Menu Nav-->

        <div class="text-center">
          <img src="<?= base_url('assets/logo/') . $this->session->company->id_perusahaan . '/' . $this->session->company->logo; ?>" width="100px" class="img-fluid" alt="Logo">
          <h5 for="" class="text-center"><strong>
              <!-- <?= $this->session->company->nm_perusahaan ?> -->
            </strong></h5>
        </div>
        <hr class="mb-0">
        <?= $this->menu_generator->show_menus_new(); ?>
        <!--end::Menu Nav-->
      </div>
      <!--end::Menu Container-->
    </div>
    <!--end::Aside Menu-->
  </div>
  <!--end::Aside-->
  <div class="d-flex flex-column flex-root">
    <!--begin::Page-->
    <div class="d-flex flex-row flex-column-fluid page">
      <!--begin::Wrapper-->
      <div class="d-flex flex-column flex-row-fluid wrapper" id="kt_wrapper">
        <!--begin::Header-->
        <div id="kt_header" class="header header-fixed">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Header Menu Wrapper-->
            <div class="header-menu-wrapper header-menu-wrapper-left" id="kt_header_menu_wrapper">
              <!--begin::Logo-->
              <!-- <div class="header-logo mr-3 d-none d-lg-flex" style="background-color: transparent;">
                <a href="<?= base_url(); ?>">
                  <img alt="Logo" src="<?= base_url('assets/img/logo-2.png'); ?>" class="h-40px" />
                </a>
              </div> -->

              <!--end::Logo-->
              <!--begin::Header Menu-->
              <div id="kt_header_menu" class="header-menu header-menu-left header-menu-mobile header-menu-layout-default">
                <!--begin::Header Nav-->
                <button class="d-none" id="kt_aside_toggle"></button>
                <ul class="menu-nav">
                  <li class="menu-item">
                    <button class=" btn btn-icon" onclick="$('#kt_aside_toggle').click()">
                      <span class="svg-icon svg-icon-xl">
                        <!--begin::Svg Icon | path:/keen/theme/demo6/dist/assets/media/svg/icons/Text/Toggle-Left.svg-->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="48px" height="48px" viewBox="0 0 24 24" version="1.1">
                          <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                            <rect x="0" y="0" width="24" height="24" />
                            <path d="M2 11.5C2 12.3284 2.67157 13 3.5 13H20.5C21.3284 13 22 12.3284 22 11.5V11.5C22 10.6716 21.3284 10 20.5 10H3.5C2.67157 10 2 10.6716 2 11.5V11.5Z" fill="black" />
                            <path opacity="0.5" fill-rule="evenodd" clip-rule="evenodd" d="M9.5 20C8.67157 20 8 19.3284 8 18.5C8 17.6716 8.67157 17 9.5 17H20.5C21.3284 17 22 17.6716 22 18.5C22 19.3284 21.3284 20 20.5 20H9.5ZM15.5 6C14.6716 6 14 5.32843 14 4.5C14 3.67157 14.6716 3 15.5 3H20.5C21.3284 3 22 3.67157 22 4.5C22 5.32843 21.3284 6 20.5 6H15.5Z" fill="black" />
                          </g>
                        </svg>
                        <!--end::Svg Icon-->
                      </span>
                    </button>
                  </li>
                  <li class="menu-item <?= (check_class('dashboard', TRUE)) ? 'menu-item-active' : ''; ?>" aria-haspopup="true">
                    <a href="<?= base_url('/dashboard'); ?>" class="menu-link" style="border-radius: 14px 1px 14px 1px  ;">
                      <span class="menu-text text-white h5 my-0"><i class="fa fa-home mr-3 text-white"></i> Dashboard</span>
                    </a>
                  </li>
                  <li class="menu-item <?= (check_class('monitoring', TRUE)) ? 'menu-item-active' : ''; ?> " aria-haspopup="true">
                    <a href="<?= base_url('/monitoring'); ?>" class="menu-link" style="border-radius: 14px 1px 14px 1px  ;">
                      <span class="menu-text text-white h5 my-0"><i class="fa fa-tasks mr-3 text-white"></i> Monitoring</span>
                    </a>
                  </li>

                </ul>
                <!--end::Header Nav-->
              </div>
              <!--end::Header Menu-->
            </div>
            <!--end::Header Menu Wrapper-->
            <!--begin::Topbar-->
            <div class="topbar">
              <!--begin::Quick Actions-->
              <!-- <div class="dropdown">
                <div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
                  <div class="btn btn-icon btn-hover-transparent-white btn-dropdown btn-lg mr-1">
                    <span class="svg-icon svg-icon-xl">
                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <rect x="0" y="0" width="24" height="24" />
                          <path opacity="0.3" fill-rule="evenodd" clip-rule="evenodd" d="M14.4862 18L12.7975 21.0566C12.5304 21.54 11.922 21.7153 11.4386 21.4483C11.2977 21.3704 11.1777 21.2597 11.0887 21.1255L9.01653 18H5C3.34315 18 2 16.6569 2 15V6C2 4.34315 3.34315 3 5 3H19C20.6569 3 22 4.34315 22 6V15C22 16.6569 20.6569 18 19 18H14.4862Z" fill="black" />
                          <path fill-rule="evenodd" clip-rule="evenodd" d="M6 7H15C15.5523 7 16 7.44772 16 8C16 8.55228 15.5523 9 15 9H6C5.44772 9 5 8.55228 5 8C5 7.44772 5.44772 7 6 7ZM6 11H11C11.5523 11 12 11.4477 12 12C12 12.5523 11.5523 13 11 13H6C5.44772 13 5 12.5523 5 12C5 11.4477 5.44772 11 6 11Z" fill="black" />
                        </g>
                      </svg>
                    </span>
                  </div>
                </div>
                <div class="dropdown-menu p-0 m-0 dropdown-menu-right dropdown-menu-anim-up dropdown-menu-lg">
                  <div class="d-flex flex-column flex-center py-10 rounded-to border-bottom">
                    <h4 class="text-dark font-weight-bold">Quick Actions</h4>
                    <span class="btn btn-primary btn-sm font-weight-bold font-size-sm mt-2">23 new tasks</span>
                  </div>
                  <div class="row row-paddingless">
                  </div>
                </div>
              </div> -->

              <div class="topbar-item mr-3">
                <span class="bg-white rounded py-2 h6 my-0 pl-3 pr-10" style="margin-right:-30px"><?= $userData->full_name; ?></span>
                <div class="btn btn-icon w-auto d-flex align-items-center btn-lg px-2" onclick="$('#kt_quick_user_toggle').click()">
                  <div class="symbol symbol-circle symbol-50 bg-white overflow-hidden">
                    <div class="symbol-label">
                      <img alt="avatar" src="<?= (isset($userData->photo) && file_exists('assets/img/avatar/' . $userData->photo)) ? base_url('assets/img/avatar/' . $userData->photo) : base_url('assets/img/avatar/no-user.jpg'); ?>" class="h-100 align-self-end" />
                    </div>
                  </div>
                </div>
              </div>

              <div class="topbar-item" data-offset="10px,0px">
                <a href="<?= base_url('users/logout'); ?>">
                  <div class="btn btn-icon btn-hover-transparent-white btn-dropdown btn-lg mr-1" title="Log Out">
                    <span class="svg-icon svg-icon-2x">
                      <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                          <rect x="0" y="0" width="24" height="24" />
                          <path d="M14.0069431,7.00607258 C13.4546584,7.00607258 13.0069431,6.55855153 13.0069431,6.00650634 C13.0069431,5.45446114 13.4546584,5.00694009 14.0069431,5.00694009 L15.0069431,5.00694009 C17.2160821,5.00694009 19.0069431,6.7970243 19.0069431,9.00520507 L19.0069431,15.001735 C19.0069431,17.2099158 17.2160821,19 15.0069431,19 L3.00694311,19 C0.797804106,19 -0.993056895,17.2099158 -0.993056895,15.001735 L-0.993056895,8.99826498 C-0.993056895,6.7900842 0.797804106,5 3.00694311,5 L4.00694793,5 C4.55923268,5 5.00694793,5.44752105 5.00694793,5.99956624 C5.00694793,6.55161144 4.55923268,6.99913249 4.00694793,6.99913249 L3.00694311,6.99913249 C1.90237361,6.99913249 1.00694311,7.89417459 1.00694311,8.99826498 L1.00694311,15.001735 C1.00694311,16.1058254 1.90237361,17.0008675 3.00694311,17.0008675 L15.0069431,17.0008675 C16.1115126,17.0008675 17.0069431,16.1058254 17.0069431,15.001735 L17.0069431,9.00520507 C17.0069431,7.90111468 16.1115126,7.00607258 15.0069431,7.00607258 L14.0069431,7.00607258 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" transform="translate(9.006943, 12.000000) scale(-1, 1) rotate(-90.000000) translate(-9.006943, -12.000000) " />
                          <rect fill="#000000" opacity="0.3" transform="translate(14.000000, 12.000000) rotate(-270.000000) translate(-14.000000, -12.000000) " x="13" y="6" width="2" height="12" rx="1" />
                          <path d="M21.7928932,9.79289322 C22.1834175,9.40236893 22.8165825,9.40236893 23.2071068,9.79289322 C23.5976311,10.1834175 23.5976311,10.8165825 23.2071068,11.2071068 L20.2071068,14.2071068 C19.8165825,14.5976311 19.1834175,14.5976311 18.7928932,14.2071068 L15.7928932,11.2071068 C15.4023689,10.8165825 15.4023689,10.1834175 15.7928932,9.79289322 C16.1834175,9.40236893 16.8165825,9.40236893 17.2071068,9.79289322 L19.5,12.0857864 L21.7928932,9.79289322 Z" fill="#000000" fill-rule="nonzero" transform="translate(19.500000, 12.000000) rotate(-90.000000) translate(-19.500000, -12.000000) " />
                        </g>
                      </svg>
                      <!--end::Svg Icon-->
                    </span>

                  </div>
                </a>
              </div>


              <!--end::User-->
            </div>
            <!--end::Topbar-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::Header-->
        <div class="ajax_loader">
          <!-- <img src="<?php echo base_url('assets/images/ajax_loader.gif'); ?>"> -->
        </div>