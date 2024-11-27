<?php
$page_title = 'Sales Report';
$results = '';
require_once('includes/load.php');

// Check user permission level
page_require_level(3);

if(isset($_POST['submit'])){
    $req_dates = array('start-date', 'end-date');
    validate_fields($req_dates);

    if(empty($errors)):
        $start_date = remove_junk($db->escape($_POST['start-date']));
        $end_date = remove_junk($db->escape($_POST['end-date']));
        $results = find_sale_by_dates($start_date, $end_date);
    else:
        $session->msg("d", $errors);
        redirect('sales_report.php', false);
    endif;

} else {
    $session->msg("d", "Select dates");
    redirect('sales_report.php', false);
}

function total_qty($results) {
    $total_qty = 0;
    foreach ($results as $result) {
        $total_qty += (int)$result['total_sales']; // Assume this field reflects the deducted quantity
    }
    return $total_qty;
}

function total_selling_price($results) {
    $total_selling_price = 0;
    foreach ($results as $result) {
        $total_selling_price += (float)$result['total_saleing_price'];
    }
    return $total_selling_price;
}

function total_buying_price($results) {
    $total_buying_price = 0;
    foreach ($results as $result) {
        $total_buying_price += (float)$result['buy_price'] * (int)$result['total_sales'];
    }
    return $total_buying_price;
}

function total_profit($results) {
    return total_selling_price($results) - total_buying_price($results);
}
?>
<!doctype html>
<html lang="en-US">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Sales Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 10px;
            width: 80mm; /* Standard POS receipt width */
        }
        .sale-head {
            text-align: center;
            margin-bottom: 10px;
        }
        .sale-head h1 {
            margin: 0;
            font-size: 12pt;
            text-transform: uppercase;
        }
        .sale-head p {
            margin: 5px 0;
            font-size: 9pt;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 5px 0;
            text-align: left;
            font-size: 9pt;
        }
        th {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        tfoot {
            border-top: 1px dashed #000;
        }
        tfoot td {
            font-weight: bold;
        }
        @media print {
            body {
                margin: 0;
                width: 80mm; /* Set receipt size for print */
            }
            table {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php if($results): ?>
        <div class="sale-head">
            <img src="libs/images/icon.png" alt="ANC Mini Mart Logo" style="width:60px; height:auto; margin-bottom:5px;">
            <h1>ANC Mini Mart</h1>
            <p>Kabangbang, Bantayan, Cebu</p>
            <p>Contact: 09086062594</p>
            <p>Email: ancminimartbantayan@yahoo.com</p>
            <p><strong><?php echo isset($start_date) ? $start_date : ''; ?> to <?php echo isset($end_date) ? $end_date : ''; ?></strong></p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Item</th>
                    <th>Qty</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($results as $result): ?>
                    <tr>
                        <td><?php echo remove_junk($result['date']); ?></td>
                        <td><?php echo remove_junk(ucfirst($result['name'])); ?></td>
                        <td><?php echo (int)$result['total_sales']; ?></td>
                        <td class="text-right"><?php echo number_format((float)$result['total_saleing_price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total Sales</td>
                    <td colspan="2" class="text-right">₱ <?php echo number_format(total_selling_price($results), 2); ?></td>
                </tr>
                <tr>
                    <td colspan="2">Profit</td>
                    <td colspan="2" class="text-right">₱ <?php echo number_format(total_profit($results), 2); ?></td>
                </tr>
            </tfoot>
        </table>
    <?php else: ?>
        <p>No sales found for the selected period.</p>
    <?php endif; ?>
    <script>
        window.onload = function () {
            window.print();
        };
    </script>
</body>
</html>

<?php if(isset($db)) { $db->db_disconnect(); } ?>