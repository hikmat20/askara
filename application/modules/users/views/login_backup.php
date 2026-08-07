<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&amp;display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url(); ?>assets/login/css/fonts%2c_icomoon%2c_style.css%2bcss%2c_owl.carousel.min.css%2bcss%2c_bootstrap.min.css%2bcss%2c_style.css.pagespeed.cc.WuwWHFx2BT.css" />
    <title>SentralDocs</title>
    <?php echo recaptcha_script('login_form'); ?>
</head>

<body>
    <div class="content" style="background-image: url(<?= base_url(); ?>assets/img/geomtri.png);background-repeat:repeat">
        <div class="container">
            <?php if ($this->session->userdata('tmessage')) : ?>
                <div class="alert alert-danger">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <strong>Failed!</strong> <?= $this->session->userdata('tmessage'); ?>
                </div>
            <?php endif; ?>
            <div class="row px-4">
                <div class="col-md-6 order-md-2">
                    <img src="<?= base_url(); ?>assets/img/Doc-Man.png" alt="Image" class="img-fluid" style="max-height: 60vh;">
                </div>
                <div class="col-md-6 contents">
                    <div class="row justify-content-center">
                        <div class="col-md-8 shadow bg-white py-4 px-4" style="border-radius: 1em;">
                            <img src="<?= base_url('assets/login/images/logo-2.png'); ?>" width="100%" class="img-responsive" alt="Image">
                            <div class="my-4 text- text-muted">
                                <p>Login</p><!-- <p class="mb-4">Login</p> -->
                            </div>
                            <?= form_open($this->uri->uri_string(), array('id' => 'frm_login', 'name' => 'frm_login', 'class' => 'login')) ?>
                            <i class="fa fa-user"></i>
                            <i class="fa fa-key"></i>
                            <div class="form-group first">
                                <input type="text" name="inisial" class="form-control" placeholder="Company" value="<?= set_value('inisial') ?>" required autofocus>
                            </div>
                            <div class="form-group">
                                <input type="text" name="username" class="form-control" placeholder="Username" value="<?= set_value('username') ?>" required>
                            </div>
                            <div class="form-group last mb-4" style="position: relative;">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Password" value="" required style="padding-right: 40px;">
                                <span toggle="#password" class="toggle-password" style="position: absolute; right: 0px; top: 50%; transform: translateY(-50%); cursor: pointer; z-index: 10; color: #6c757d; padding: 10px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16" id="eye-icon-open">
                                      <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                      <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                    </svg>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16" id="eye-icon-closed" style="display: none;">
                                      <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l-.708.709z"/>
                                      <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                                      <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>
                                    </svg>
                                </span>
                            </div>
                            <div class="d-flex mb-5 align-items-center">
                                <!-- <label class="control control--checkbox mb-0"><span class="caption">Remember me</span>
                                        <input type="checkbox" checked />
                                        <div class="control__indicator"></div>
                                    </label> -->
                                <!-- <span class="ml-auto"><a href="#" class="forgot-pass">Forgot Password</a></span> -->
                            </div>
                            <?php echo recaptcha_div('login_form'); ?>
                            <button type="submit" name="login" class="btn text-white btn-block btn-primary">Login</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?= base_url(); ?>assets/login/js/jquery-3.3.1.min.js"></script>
    <script src="<?= base_url(); ?>assets/login/js/popper.min.js%2bbootstrap.min.js%2bmain.js.pagespeed.jc.AM7zHOnWML.js"></script>
    <script defer src="https://static.cloudflareinsights.com/beacon.min.js" data-cf-beacon='{"rayId":"67e0441d4d13103f","token":"cd0b4b3a733644fc843ef0b185f98241","version":"2021.8.0","si":10}'></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script>
        getToken('login_form');

        $(".toggle-password").click(function() {
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
                $(this).find("#eye-icon-open").hide();
                $(this).find("#eye-icon-closed").show();
            } else {
                input.attr("type", "password");
                $(this).find("#eye-icon-open").show();
                $(this).find("#eye-icon-closed").hide();
            }
        });
    </script>

</body>

</html>