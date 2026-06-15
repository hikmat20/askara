<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cross Reference</title>
</head>
<style>
    body {
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12px;
    }

    table {
        border-collapse: collapse;
    }

    table.bordered tr td,
    table.bordered tr th {
        border: 1px solid #aaa;
    }

    table tr th {
        padding: 5px;
        background-color: #eaeaea;
    }

    table tr td {
        padding: 10px;
    }
</style>

<body>

    <h2>Standard : <?= $crossStd->name; ?></h2>

    <?php if ($lsProcedure) : ?>
        <table class="bordered" style="width: 100%;">
            <thead>
                <tr>
                    <th colspan="3" style="text-align: center;"><h3>CROSS REFERENCE <?= $crossStd->name ?> PROCESSES TO PASAL</h3></th>
                </tr>
                <tr>
                    <th width="40" style="text-align: center;">No.</th>
                    <th width="280" style="text-align: left;">Procedure Name</th>
                    <th style="text-align: left;">Pasal</th>
                </tr>
            </thead>
            <tbody>
                <?php $n = 0;
                foreach ($lsProcedure as $p) : 
                    if (isset($procedures[$p])) : $n++; ?>
                        <tr>
                            <td style="text-align: center; vertical-align: top;"><?= $n; ?></td>
                            <td style="vertical-align: top;"><?= strtoupper($procedures[$p]->name); ?></td>
                            <td style="vertical-align: top;">
                                <?php 
                                if (isset($DataStd[$p]) && $DataStd[$p]) {
                                    foreach ($DataStd[$p] as $dt) {
                                        echo htmlspecialchars($dt->chapter) . "<br>";
                                    }
                                } else {
                                    echo "-";
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <span>~ Not available data ~</span>
    <?php endif; ?>
</body>

</html>