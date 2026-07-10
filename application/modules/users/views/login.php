<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>SentralDocs - Login</title>
    <!-- Google Fonts Roboto -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php echo recaptcha_script('login_form'); ?>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            background-color: #0f172a; /* Slate dark background */
            background-image: 
                radial-gradient(circle at 15% 50%, rgba(37, 99, 235, 0.15), transparent 25%),
                radial-gradient(circle at 85% 30%, rgba(14, 165, 233, 0.15), transparent 25%);
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            z-index: -1;
        }
        .login-wrapper {
            width: 100%;
            max-width: 950px; /* Widened for the side image */
            padding: 20px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.1);
            overflow: hidden;
            display: flex;
        }
        .login-form-side {
            padding: 40px 40px;
            width: 50%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-image-side {
            width: 50%;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 40px;
            color: white;
            text-align: center;
            border-left: 1px solid rgba(255,255,255,0.05);
        }
        .logo-container {
            text-align: center;
            margin-bottom: 25px;
        }
        .logo-img {
            max-width: 140px;
            height: auto;
        }
        .form-floating > .form-control {
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            padding-left: 15px;
            font-size: 0.95rem;
            color: #334155;
            box-shadow: none;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .form-floating > .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        .form-floating > label {
            padding-left: 15px;
            color: #64748b;
        }
        .btn-login {
            background-color: #1e293b;
            color: #fff;
            border-radius: 10px;
            padding: 12px;
            font-weight: 500;
            font-size: 1rem;
            width: 100%;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            background-color: #2563eb;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(37, 99, 235, 0.25);
        }
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            z-index: 5;
            transition: color 0.3s;
        }
        .toggle-password:hover {
            color: #475569;
        }
        .alert-danger {
            border-radius: 10px;
            font-size: 0.85rem;
            padding: 10px 15px;
            display: flex;
            align-items: center;
        }
        .alert-danger .btn-close {
            padding: 12px;
        }
        .recaptcha-wrapper {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            transform: scale(0.9);
            transform-origin: center;
        }
        /* Responsiveness for mobile */
        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
            }
            .login-form-side {
                width: 100%;
                padding: 30px 20px;
            }
            .login-image-side {
                display: none; /* Hide image on small screens to save space */
            }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            
            <!-- Left Side: Form -->
            <div class="login-form-side">
                <div class="logo-container">
                    <img src="<?= base_url('assets/login/images/logo-2.png'); ?>" alt="SentralDocs Logo" class="logo-img">
                    <h5 class="mt-4 fw-semibold" style="color: #1e293b; font-size: 1.15rem; letter-spacing: 0.5px;">Login to Your Account</h5>
                </div>
                
                <?php if ($this->session->userdata('tmessage')) : ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle-fill me-2" viewBox="0 0 16 16">
                          <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8 4a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 4zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                        </svg>
                        <div><?= $this->session->userdata('tmessage'); ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?= form_open($this->uri->uri_string(), array('id' => 'frm_login', 'name' => 'frm_login')) ?>
                    <div class="form-floating mb-3">
                        <input type="text" name="inisial" class="form-control" id="inisialInput" placeholder="Company Code" value="<?= set_value('inisial') ?>" required autofocus>
                        <label for="inisialInput">Company Code</label>
                    </div>
                    
                    <div class="form-floating mb-3">
                        <input type="text" name="username" class="form-control" id="usernameInput" placeholder="Username" value="<?= set_value('username') ?>" required>
                        <label for="usernameInput">Username</label>
                    </div>
                    
                    <div class="form-floating mb-4 position-relative">
                        <input type="password" name="password" class="form-control" id="passwordInput" placeholder="Password" required>
                        <label for="passwordInput">Password</label>
                        <span toggle="#passwordInput" class="toggle-password">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16" id="eye-icon-open">
                              <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                              <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                            </svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-eye-slash" viewBox="0 0 16 16" id="eye-icon-closed" style="display: none;">
                              <path d="M13.359 11.238C15.06 9.72 16 8 16 8s-3-5.5-8-5.5a7.028 7.028 0 0 0-2.79.588l.77.771A5.944 5.944 0 0 1 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.134 13.134 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755-.165.165-.337.328-.517.486l-.708.709z"/>
                              <path d="M11.297 9.176a3.5 3.5 0 0 0-4.474-4.474l.823.823a2.5 2.5 0 0 1 2.829 2.829l.822.822zm-2.943 1.299.822.822a3.5 3.5 0 0 1-4.474-4.474l.823.823a2.5 2.5 0 0 0 2.829 2.829z"/>
                              <path d="M3.35 5.47c-.18.16-.353.322-.518.487A13.134 13.134 0 0 0 1.172 8l.195.288c.335.48.83 1.12 1.465 1.755C4.121 11.332 5.881 12.5 8 12.5c.716 0 1.39-.133 2.02-.36l.77.772A7.029 7.029 0 0 1 8 13.5C3 13.5 0 8 0 8s.939-1.721 2.641-3.238l.708.709zm10.296 8.884-12-12 .708-.708 12 12-.708.708z"/>
                            </svg>
                        </span>
                    </div>
                    
                    <div class="recaptcha-wrapper">
                        <?php echo recaptcha_div('login_form'); ?>
                    </div>
                    
                    <button type="submit" name="login" class="btn btn-login mb-2">Sign In</button>
                </form>
                <div class="text-center mt-3" style="color: rgba(30,41,59,0.5); font-size: 0.75rem;">
                    &copy; <?= date('Y'); ?> SentralDocs Platform
                </div>
            </div>

            <!-- Right Side: Illustration -->
            <div class="login-image-side">
                <h3 class="fw-semibold mb-2">ISO Certification</h3>
                <p class="mb-5" style="color: rgba(255,255,255,0.7); font-size: 0.95rem; font-weight: 300;">Manage your standard compliance digitally.</p>
                
                <svg width="80%" viewBox="0 0 400 300" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="shieldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#3b82f6;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#2563eb;stop-opacity:1" />
                        </linearGradient>
                        <linearGradient id="docGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                            <stop offset="0%" style="stop-color:#ffffff;stop-opacity:0.9" />
                            <stop offset="100%" style="stop-color:#e2e8f0;stop-opacity:0.95" />
                        </linearGradient>
                        <filter id="glow" x="-20%" y="-20%" width="140%" height="140%">
                            <feDropShadow dx="0" dy="10" stdDeviation="15" flood-color="#2563eb" flood-opacity="0.3"/>
                        </filter>
                    </defs>

                    <!-- Abstract Background Elements -->
                    <circle cx="200" cy="150" r="120" fill="rgba(255,255,255,0.02)"/>
                    <circle cx="200" cy="150" r="90" fill="rgba(255,255,255,0.03)"/>

                    <!-- 3D-ish Document -->
                    <rect x="130" y="50" width="140" height="180" rx="12" fill="url(#docGrad)" transform="rotate(10 200 140)" filter="url(#glow)"/>
                    <!-- Document Lines (rotated with doc) -->
                    <g transform="rotate(10 200 140)">
                        <rect x="150" y="80" width="60" height="6" rx="3" fill="#cbd5e1"/>
                        <rect x="150" y="100" width="100" height="6" rx="3" fill="#94a3b8"/>
                        <rect x="150" y="120" width="80" height="6" rx="3" fill="#cbd5e1"/>
                        
                        <rect x="150" y="160" width="40" height="40" rx="4" fill="#3b82f6" opacity="0.1"/>
                        <path d="M160 180 l8 8 l14 -14" fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round"/>
                    </g>
                    
                    <!-- Floating Shield (Security / ISO) -->
                    <path d="M230 180 l35 -15 l35 15 v30 c0 20 -15 35 -35 50 c-20 -15 -35 -30 -35 -50 z" fill="url(#shieldGrad)" filter="url(#glow)"/>
                    <path d="M250 205 l10 10 l20 -20" fill="none" stroke="#ffffff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    
                    <!-- Analytics Floating Graph -->
                    <rect x="80" y="150" width="70" height="80" rx="10" fill="#1e293b" opacity="0.9" border="1px solid rgba(255,255,255,0.1)"/>
                    <rect x="95" y="195" width="10" height="20" rx="3" fill="#38bdf8"/>
                    <rect x="110" y="175" width="10" height="40" rx="3" fill="#60a5fa"/>
                    <rect x="125" y="185" width="10" height="30" rx="3" fill="#3b82f6"/>
                </svg>

            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        if(typeof getToken === 'function') {
            getToken('login_form');
        }

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