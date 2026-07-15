<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SentralDocs - ISO Management Platform</title>
    <!-- Modern Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php echo recaptcha_script('login_form'); ?>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.05);
            overflow: hidden;
            background: #fff;
            max-width: 750px;
            margin: 0 auto;
        }
        .login-left {
            background: linear-gradient(135deg, #0a4b78 0%, #1771b1 100%);
            padding: 40px;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
        }
        .login-left::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('<?= base_url(); ?>assets/img/geometri.png') center/cover;
            opacity: 0.1;
            pointer-events: none;
        }
        .login-right {
            padding: 35px 30px;
            background: #ffffff;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 15px;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            font-size: 0.85rem;
        }
        .form-control:focus {
            box-shadow: 0 0 0 4px rgba(23, 113, 177, 0.1);
            border-color: #1771b1;
            background: #fff;
        }
        .form-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: #5f6368;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .btn-primary {
            background-color: #1771b1;
            border-color: #1771b1;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(23, 113, 177, 0.2);
        }
        .btn-primary:hover {
            background-color: #0a4b78;
            border-color: #0a4b78;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(23, 113, 177, 0.3);
        }
        .toggle-password {
            position: absolute;
            right: 18px;
            top: 36px; /* Adjusted based on label height */
            cursor: pointer;
            color: #9aa0a6;
            transition: color 0.2s ease;
        }
        .toggle-password:hover {
            color: #1771b1;
        }
        .form-group {
            position: relative;
            margin-bottom: 20px;
            text-align: left;
        }
        .logo-img {
            max-width: 110px;
            margin-bottom: 15px;
        }
        .iso-svg {
            max-width: 85%;
            height: auto;
            margin-top: 20px;
            z-index: 1;
        }
        .tagline {
            font-size: 0.95rem;
            opacity: 0.9;
            font-weight: 300;
        }
        .recaptcha-wrapper {
            display: flex;
            justify-content: center;
            margin-bottom: 25px;
        }
        .floating-back-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            border-radius: 50px;
            padding: 8px 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            font-weight: 500;
            transition: all 0.3s ease;
            background-color: white;
            border: 1px solid #e2e8f0;
            color: #475569;
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        .floating-back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            background-color: #f8fafc;
            color: #0f172a;
        }
        @media (max-width: 768px) {
            .floating-back-btn {
                top: 10px;
                left: 10px;
                padding: 6px 15px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <a href="https://iso.askara-int.com" class="floating-back-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-2" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        </svg>
        Back to Portal
    </a>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10 col-md-11">
                <div class="card login-card">
                    <div class="row g-0">
                        <div class="col-lg-6 login-left d-none d-lg-flex">
                            <h2 class="fw-bold mb-3">ISO Standard Platform</h2>
                            <p class="tagline mb-4">Secure, Compliant, and Centralized Document Management System.</p>
                            
                            <!-- Corporate ISO SVG Illustration -->
                            <svg class="iso-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 400">
                                <defs>
                                    <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                      <stop offset="0%" style="stop-color:#ffffff;stop-opacity:1" />
                                      <stop offset="100%" style="stop-color:#e0f2fe;stop-opacity:1" />
                                    </linearGradient>
                                    <filter id="shadow" x="-20%" y="-20%" width="140%" height="140%">
                                      <feDropShadow dx="0" dy="15" stdDeviation="15" flood-opacity="0.1"/>
                                    </filter>
                                </defs>
                                <!-- Background Elements -->
                                <circle cx="250" cy="200" r="180" fill="rgba(255,255,255,0.05)"/>
                                <circle cx="250" cy="200" r="130" fill="rgba(255,255,255,0.08)"/>
                                
                                <!-- Main Document / Certificate -->
                                <rect x="160" y="80" width="180" height="240" rx="12" fill="url(#grad1)" filter="url(#shadow)"/>
                                
                                <!-- Document Lines -->
                                <rect x="190" y="140" width="120" height="8" rx="4" fill="#cbd5e1"/>
                                <rect x="190" y="170" width="100" height="8" rx="4" fill="#cbd5e1"/>
                                <rect x="190" y="200" width="80" height="8" rx="4" fill="#cbd5e1"/>
                                <rect x="190" y="230" width="110" height="8" rx="4" fill="#cbd5e1"/>
                                
                                <!-- ISO Stamp/Seal -->
                                <circle cx="310" cy="260" r="28" fill="#10b981"/>
                                <circle cx="310" cy="260" r="22" fill="none" stroke="#ffffff" stroke-width="2" stroke-dasharray="4,4"/>
                                <path d="M298 260 l8 8 l16 -16" fill="none" stroke="#ffffff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                
                                <!-- Floating Shield (Security/Compliance) -->
                                <path d="M110 160 l30 -15 l30 15 v25 c0 20 -15 35 -30 45 c-15 -10 -30 -25 -30 -45 z" fill="#3b82f6" opacity="0.9" filter="url(#shadow)"/>
                                <path d="M130 185 l8 8 l14 -14" fill="none" stroke="#ffffff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                                
                                <!-- Floating Graph/Analytics -->
                                <rect x="360" y="110" width="70" height="60" rx="8" fill="#f8fafc" filter="url(#shadow)"/>
                                <rect x="370" y="140" width="12" height="20" rx="3" fill="#60a5fa"/>
                                <rect x="388" y="125" width="12" height="35" rx="3" fill="#3b82f6"/>
                                <rect x="406" y="150" width="12" height="10" rx="3" fill="#93c5fd"/>
                            </svg>
                        </div>
                        
                        <div class="col-lg-6 login-right">
                            <div class="text-center mb-4">
                                <?php 
                                    if (isset($company_logo_url) && $company_logo_url != '') {
                                        $logo_src = $company_logo_url;
                                    } else {
                                        $logo_inisial = (isset($company_initial) && $company_initial != '') ? strtolower($company_initial) : ''; 
                                        if ($logo_inisial != '') {
                                            $logo_src = ('/assets/login/images/1/logo_' . $logo_inisial . '.png');
                                        } else {
                                            $logo_src = base_url('assets/login/images/logo-2.png');
                                        }
                                    }
                                ?>
                                <img src="<?= $logo_src; ?>" alt="Company Logo" class="logo-img">
                                <p class="text-muted mt-3">Sign in to manage your documents</p>
                            </div>
                            
                            <?php if ($this->session->userdata('tmessage')) : 
                                $raw_msg = $this->session->userdata('tmessage');
                                $parsed_msg = $raw_msg;
                                if (strpos($raw_msg, '::') !== false) {
                                    $parts = explode('::', $raw_msg);
                                    $parsed_msg = isset($parts[1]) ? $parts[1] : $raw_msg;
                                }
                            ?>
                                <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
                                    <div id="loginToast" class="toast align-items-center text-white bg-danger border-0 show" role="alert" aria-live="assertive" aria-atomic="true" style="animation: bounceInRight 0.6s cubic-bezier(0.215, 0.610, 0.355, 1) forwards; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                        <div class="d-flex">
                                            <div class="toast-body d-flex align-items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-triangle-fill me-2" viewBox="0 0 16 16" style="flex-shrink: 0;"><path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/></svg>
                                                <div>
                                                    <strong>Peringatan!</strong><br>
                                                    <?= $parsed_msg; ?>
                                                </div>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                        </div>
                                    </div>
                                </div>
                                <style>
                                    @keyframes bounceInRight {
                                        0% { opacity: 0; transform: translateX(100%); }
                                        60% { opacity: 1; transform: translateX(-20px); }
                                        80% { transform: translateX(10px); }
                                        100% { transform: translateX(0); }
                                    }
                                    @keyframes slideOutRight { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
                                    .toast-hide { animation: slideOutRight 0.4s ease-in forwards !important; }
                                </style>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var toastEl = document.getElementById('loginToast');
                                        if (toastEl) {
                                            var hideToast = function() {
                                                toastEl.classList.add('toast-hide');
                                                setTimeout(function() { toastEl.remove(); }, 400);
                                            };
                                            var toastTimeout = setTimeout(hideToast, 3000);
                                            
                                            var closeBtn = toastEl.querySelector('.btn-close');
                                            if(closeBtn) {
                                                closeBtn.addEventListener('click', function(e) {
                                                    e.preventDefault();
                                                    clearTimeout(toastTimeout);
                                                    hideToast();
                                                });
                                            }
                                        }
                                    });
                                </script>
                            <?php endif; ?>

                            <?= form_open($this->uri->uri_string(), array('id' => 'frm_login', 'name' => 'frm_login')) ?>
                                <div class="form-group">
                                    <label class="form-label">Company Code</label>
                                    <?php $val_inisial = (isset($company_initial) && $company_initial != '') ? $company_initial : set_value('inisial'); ?>
                                    <input type="text" name="inisial" class="form-control" placeholder="Enter company code" value="<?= $val_inisial ?>" <?= (isset($company_initial) && $company_initial != '') ? 'readonly' : '' ?> required autofocus>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" placeholder="Enter your username" value="<?= set_value('username') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control pe-5" placeholder="Enter your password" required>
                                    <span toggle="#password" class="toggle-password">
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
                                <div class="recaptcha-wrapper">
                                    <?php echo recaptcha_div('login_form'); ?>
                                </div>
                                <button type="submit" name="login" class="btn btn-primary w-100">Sign In</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Footer di luar form login -->
                <div class="text-center mt-4">
                    <img src="<?= base_url('assets/login/images/logo-2.png'); ?>" alt="SentralDocs Logo" style="max-height: 40px; margin-bottom: 10px; opacity: 0.8;"><br>
                    <small class="text-muted fw-medium" style="color: #6c757d;">&copy; <?= date('Y'); ?> SentralDocs Platform. All Rights Reserved.</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        // Adjust for recapcha integration if exist getToken function in global scope
        if(typeof getToken === 'function') {
            getToken('login_form');
        }

        // Toggle Password Visibility
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