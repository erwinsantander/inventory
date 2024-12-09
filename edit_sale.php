<?php
  $page_title = 'Edit sale';
  require_once('includes/load.php');
  // Checkin What level user has permission to view this page
  page_require_level(3);
?>

<?php
$sale = find_by_id('sales', (int)$_GET['id']);
if (!$sale) {
  $session->msg("d", "Missing product id.");
  redirect('sales.php');
}
?>
<?php $product = find_by_id('products', $sale['product_id']); ?>

<?php
if (isset($_POST['update_sale'])) {
    $req_fields = array('title', 'quantity', 'price', 'total', 'date');
    validate_fields($req_fields);
    if (empty($errors)) {
        $p_id = $db->escape((int)$product['id']);
        $s_qty = $db->escape((int)$_POST['quantity']);
        $s_total = $db->escape($_POST['total']);
        $date = $db->escape($_POST['date']);
        $s_date = date("Y-m-d", strtotime($date));
        
        // Check stock availability
        $available_qty = (int)$product['quantity'];
        if ($s_qty > $available_qty) {
            $session->msg("d", "Quantity exceeds available stock!");
            redirect('edit_sale.php?id=' . (int)$sale['id'], false);
        }

        // Update sale record
        $sql = "UPDATE sales SET";
        $sql .= " product_id= '{$p_id}', qty={$s_qty}, price='{$s_total}', date='{$s_date}'";
        $sql .= " WHERE id ='{$sale['id']}'";
        $result = $db->query($sql);
        
        if ($result && $db->affected_rows() === 1) {
            update_product_qty($s_qty, $p_id); // Ensure this function handles stock properly
            $session->msg('s', "Sale updated.");
            redirect('edit_sale.php?id=' . (int)$sale['id'], false);
        } else {
            $session->msg('d', ' Sorry failed to update!');
            redirect('sales.php', false);
        }
    } else {
        $session->msg("d", $errors);
        redirect('edit_sale.php?id=' . (int)$sale['id'], false);
    }
}
?>
<?php include_once('layouts/header.php'); 
$request = $_SERVER['REQUEST_URI'];
if (substr($request, -4) == '.php') {
    $new_url = substr($request, 0, -4);
    header("Location: $new_url", true, 301);
    exit();
}
?>
<div class="row" style="margin-left: 250px; margin-top: 24px; margin-right: 10px;">
</div>
<div class="row" style="margin-left: 250px; margin-top: 24px; margin-right: 10px;">
  <div class="col-md-12">
    <div class="panel">
      <div class="panel-heading clearfix">
        <strong>
          <span class="glyphicon glyphicon-th"></span>
          <span>All Sales</span>
        </strong>
        <div class="pull-right">
          <a href="sales.php" class="btn btn-primary">Show all sales</a>
        </div>
      </div>
      <div class="panel-body">
        <table class="table table-bordered">
          <thead>
            <th> Product title </th>
            <th> Qty </th>
            <th> Price </th>
            <th> Total </th>
            <th> Date</th>
            <th> Action</th>
          </thead>
          <tbody id="product_info">
            <tr>
              <form method="post" action="edit_sale.php?id=<?php echo (int)$sale['id']; ?>">
                <td id="s_name">
                  <input type="text" class="form-control" id="sug_input" name="title" value="<?php echo remove_junk($product['name']); ?>">
                  <div id="result" class="list-group"></div>
                </td>
                <td id="s_qty">
                  <input type="number" class="form-control" name="quantity" value="<?php echo (int)$sale['qty']; ?>" min="0">
                </td>
                <td id="s_price">
                  <input type="text" class="form-control" name="price" value="<?php echo remove_junk($product['sale_price']); ?>" >
                </td>
                <td>
                  <input type="text" class="form-control" name="total" value="<?php echo remove_junk($sale['price']); ?>">
                </td>
                <td id="s_date">
                  <input type="date" class="form-control datepicker" name="date" value="<?php echo remove_junk($sale['date']); ?>">
                </td>
                <td>
                  <button type="submit" name="update_sale" class="btn btn-primary">Buy sale</button>
                </td>
              </form>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php if ($msg): ?>
<script>
    Swal.fire({
        icon: '<?php echo $msg['type']; ?>',
        title: '<?php echo $msg['message']; ?>',
        position: 'center',
        showConfirmButton: true
    });
</script>
<?php endif; ?>

<?php include_once('layouts/footer.php'); ?>
