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
            font-size: 12pt; /* Slightly larger font for readability */
            margin: 0;
            padding: 10px;
            width: 80mm; /* Standard POS receipt width */
            color: #000; /* Dark text */
        }
        .sale-head {
            text-align: center;
            margin-bottom: 15px; /* More space between header and content */
        }
        .sale-head h1 {
            margin: 0;
            font-size: 14pt; /* Larger, bold header for store name */
            text-transform: uppercase;
            font-weight: bold;
            color: #000; /* Darker header text */
        }
        .sale-head p {
            margin: 5px 0;
            font-size: 10.5pt; /* Slightly larger contact details */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            padding: 8px 5px; /* Increased padding for height */
            text-align: left;
            font-size: 11pt; /* Clear and readable text size */
            color: #000; /* Darker table text */
        }
        th {
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 2px solid #000; /* Darker table header border */
        }
        td {
            word-wrap: break-word;
        }
        .text-right {
            text-align: right;
        }
        tfoot td {
            font-size: 11pt;
            font-weight: bold;
            border-top: 2px solid #000; /* Dark border for total section */
            padding-top: 8px;
        }
        @media print {
            body {
                margin: 0;
                padding: 10px;
                width: 80mm; /* Receipt width for printing */
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
            <img src="libs/images/icon.png" alt="ANC Mini Mart Logo" style="width:60px; height:auto; margin-bottom:10px;">
            <h1>ANC Mini Mart</h1>
            <p>Kabangbang, Bantayan, Cebu</p>
            <p>Contact: 09086062594</p>
            <p>Email: ancminimartbantayan@yahoo.com</p>
            <p><strong>Date Range: <?php echo isset($start_date) ? $start_date : ''; ?> to <?php echo isset($end_date) ? $end_date : ''; ?></strong></p>
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
                        <td class="text-right">₱<?php echo number_format((float)$result['total_saleing_price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">Total Sales</td>
                    <td colspan="2" class="text-right">₱<?php echo number_format(total_selling_price($results), 2); ?></td>
                </tr>
                <tr>
                    <td colspan="2">Profit</td>
                    <td colspan="2" class="text-right">₱<?php echo number_format(total_profit($results), 2); ?></td>
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
