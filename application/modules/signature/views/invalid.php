<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invalid Signature</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --danger: #EF4444;
            --dark: #1F2937;
            --gray: #6B7280;
            --light: #F3F4F6;
            --white: #FFFFFF;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: var(--dark);
        }

        .verify-card {
            background: var(--white);
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(239, 68, 68, 0.15);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
            text-align: center;
            transform: translateY(20px);
            opacity: 0;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .card-header {
            padding: 50px 30px;
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
            color: var(--white);
        }

        .icon-circle {
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 45px;
            backdrop-filter: blur(4px);
            animation: shake 0.5s cubic-bezier(.36,.07,.19,.97) both;
            animation-delay: 0.5s;
        }

        @keyframes shake {
            10%, 90% { transform: translate3d(-1px, 0, 0); }
            20%, 80% { transform: translate3d(2px, 0, 0); }
            30%, 50%, 70% { transform: translate3d(-4px, 0, 0); }
            40%, 60% { transform: translate3d(4px, 0, 0); }
        }

        .header-title {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .card-body {
            padding: 40px 30px;
        }

        .message {
            font-size: 16px;
            color: var(--gray);
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .btn-home {
            display: inline-block;
            background: var(--light);
            color: var(--dark);
            text-decoration: none;
            padding: 12px 25px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .btn-home:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }

        .card-footer {
            background: var(--light);
            padding: 20px;
            font-size: 13px;
            color: var(--gray);
            border-top: 1px solid rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<div class="verify-card">
    <div class="card-header">
        <div class="icon-circle">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h1 class="header-title">Invalid Signature</h1>
    </div>

    <div class="card-body">
        <p class="message">The digital signature you scanned is <b>not recognized</b> or the document does not exist in our system.</p>
        <a href="<?= base_url() ?>" class="btn-home">Return to System</a>
    </div>

    <div class="card-footer">
        Verified securely by <b>Askara</b>
    </div>
</div>

</body>
</html>