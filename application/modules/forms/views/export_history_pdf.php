<!DOCTYPE html>
<html>
<head>
    <title>Status History - <?= htmlspecialchars($dataForm->number); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            padding: 0;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table th {
            text-align: left;
            width: 150px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        .data-table th, .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .data-table th {
            background-color: #f2f2f2;
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>STATUS HISTORY FORM</h2>
    </div>

    <table class="info-table">
        <tr>
            <th>Form Number</th>
            <td>: <?= htmlspecialchars($dataForm->number); ?></td>
        </tr>
        <tr>
            <th>Form Name</th>
            <td>: <?= htmlspecialchars($dataForm->name); ?></td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="30" style="text-align: center;">No</th>
                <th>Status Change</th>
                <th>By</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($status_logs)): ?>
                <?php $n = 0; foreach ($status_logs as $log): $n++; ?>
                    <tr>
                        <td style="text-align: center;"><?= $n; ?></td>
                        <td>
                            <?php 
                                $old_status = isset($sts[$log->old_status]) ? $sts[$log->old_status] : htmlspecialchars($log->old_status);
                                $new_status = isset($sts[$log->new_status]) ? $sts[$log->new_status] : htmlspecialchars($log->new_status);
                                echo $old_status . ' &rarr; ' . $new_status;
                            ?>
                            <?php if (!empty($log->note)): ?>
                                <br><small>Note: <?= htmlspecialchars($log->note); ?></small>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($log->action_by_name ? $log->action_by_name : $log->action_by); ?></td>
                        <td><?= date('d M Y H:i', strtotime($log->action_at)); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align: center;">No status history available.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
