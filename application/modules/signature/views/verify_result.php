<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signature Verification</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4F46E5;
            --success: #10B981;
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
            background: linear-gradient(135deg, #f0f4f8 0%, #d9e2ec 100%);
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
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
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
            padding: 40px 30px;
            text-align: center;
            color: var(--white);
            position: relative;
        }

        .status-valid .card-header {
            background: linear-gradient(135deg, #10B981 0%, #059669 100%);
        }

        .status-invalid .card-header {
            background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
        }

        .icon-circle {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            backdrop-filter: blur(4px);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.4); }
            70% { box-shadow: 0 0 0 20px rgba(255, 255, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .header-subtitle {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 300;
        }

        .card-body {
            padding: 30px;
        }

        .section-title {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--gray);
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--light);
        }

        .info-group {
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 15px;
            transition: transform 0.2s ease;
        }
        
        .info-group:hover {
            transform: translateX(5px);
        }

        .info-icon {
            width: 40px;
            height: 40px;
            background: var(--light);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            font-size: 18px;
            flex-shrink: 0;
        }

        .status-invalid .info-icon {
            color: var(--danger);
        }

        .info-content {
            flex: 1;
        }

        .info-label {
            font-size: 12px;
            color: var(--gray);
            margin-bottom: 4px;
            font-weight: 500;
        }

        .info-value {
            font-size: 15px;
            color: var(--dark);
            font-weight: 600;
            line-height: 1.4;
        }

        .card-footer {
            background: var(--light);
            padding: 20px;
            text-align: center;
            font-size: 13px;
            color: var(--gray);
            border-top: 1px solid rgba(0,0,0,0.05);
        }

        .brand-logo {
            font-weight: 700;
            color: var(--dark);
            letter-spacing: -0.5px;
        }
        .btn-primary {
            display: inline-block;
            background: var(--primary);
            color: var(--white);
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.3);
            width: 100%;
            text-align: center;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(79, 70, 229, 0.4);
        }
    </style>
</head>

<body>

<div class="verify-card <?= ($status == 'VALID') ? 'status-valid' : 'status-invalid' ?>">
    <!-- Header -->
    <div class="card-header">
        <div class="icon-circle">
            <?php if ($status == 'VALID'): ?>
                <i class="fa-solid fa-check"></i>
            <?php else: ?>
                <i class="fa-solid fa-xmark"></i>
            <?php endif; ?>
        </div>
        <h1 class="header-title">Signature <?= $status ?></h1>
        <p class="header-subtitle">Digital Signature Verification System</p>
    </div>

    <!-- Body -->
    <div class="card-body">
        
        <div class="section-title">Signer Information</div>

        <?php
            $actionLabel = 'Signed Document';
            if ($signature->sign_type == 'prepare') $actionLabel = 'Prepared Document';
            elseif ($signature->sign_type == 'review') $actionLabel = 'Reviewed Document';
            elseif ($signature->sign_type == 'approve') $actionLabel = 'Approved Document';
        ?>
        <div class="info-group">
            <div class="info-icon"><i class="fa-solid fa-stamp"></i></div>
            <div class="info-content">
                <div class="info-label">Signature Action</div>
                <div class="info-value"><?= $actionLabel ?> <span style="color:var(--primary); font-size:11px; font-weight:700; padding: 3px 8px; background: rgba(79, 70, 229, 0.1); border-radius: 12px; margin-left: 5px; text-transform: uppercase; vertical-align: middle;"><?= $signature->sign_type ?></span></div>
            </div>
        </div>
        
        <div class="info-group">
            <div class="info-icon"><i class="fa-solid fa-user-pen"></i></div>
            <div class="info-content">
                <div class="info-label">Signed By</div>
                <div class="info-value"><?= $signature->sign_by_name ?></div>
            </div>
        </div>
        
        <div class="info-group">
            <div class="info-icon"><i class="fa-solid fa-briefcase"></i></div>
            <div class="info-content">
                <div class="info-label">Position / Role</div>
                <div class="info-value"><?= $signature->position_name ?></div>
            </div>
        </div>

        <div class="info-group">
            <div class="info-icon"><i class="fa-solid fa-clock"></i></div>
            <div class="info-content">
                <div class="info-label">Timestamp</div>
                <div class="info-value"><?= date("d M Y, H:i", strtotime($signature->sign_at)) ?></div>
            </div>
        </div>

        <div class="section-title" style="margin-top: 30px;">Document Details</div>

        <div class="info-group">
            <div class="info-icon"><i class="fa-solid fa-file-lines"></i></div>
            <div class="info-content">
                <div class="info-label">Document Title</div>
                <div class="info-value"><?= $document->name ?></div>
            </div>
        </div>

        <div class="info-group">
            <div class="info-icon"><i class="fa-solid fa-hashtag"></i></div>
            <div class="info-content">
                <div class="info-label">Document Number</div>
                <div class="info-value"><?= $document->nomor ?> <span style="color:var(--gray); font-size:12px; font-weight:400; margin-left:5px;">(Rev. <?= $document->revision ?>)</span></div>
            </div>
        </div>

        <?php if (!empty($document->file_path)): ?>
        <div style="margin-top: 35px;">
            <a href="<?= site_url('signature/view_document?token=' . $signature->token) ?>" target="_blank" class="btn-primary">
                <i class="fa-solid fa-arrow-up-right-from-square" style="margin-right: 8px;"></i> View Original Document
            </a>
        </div>
        <?php endif; ?>

    </div>

    <!-- Footer -->
    <div class="card-footer">
        Verified securely by <span class="brand-logo">Askara</span>
    </div>
</div>

</body>
</html>