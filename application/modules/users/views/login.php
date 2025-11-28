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
                            <div class="form-group last mb-4">
                                <input type="password" name="password" class="form-control" placeholder="Password" value="" required>
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
    </script>

</body>

</html>