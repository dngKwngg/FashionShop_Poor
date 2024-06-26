<?php require_once('header.php'); ?>

<?php
$error_message = '';
if (isset($_POST['form1'])) {
    $valid = 1;
    if (empty($_POST['subject_text'])) {
        $valid = 0;
        $error_message .= 'Subject can not be empty\n';
    }
    if (empty($_POST['message_text'])) {
        $valid = 0;
        $error_message .= 'Subject can not be empty\n';
    }
    if ($valid == 1) {

        $subject_text = strip_tags($_POST['subject_text']);
        $message_text = strip_tags($_POST['message_text']);

        // Getting Customer Email Address
        $statement = $pdo->prepare("SELECT cust_email FROM tbl_customer WHERE cust_id=?");
        $statement->execute(array($_POST['cust_id']));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $cust_email = $row['cust_email'];
        }

        // Getting Admin Email Address
        $statement = $pdo->prepare("SELECT contact_email FROM tbl_settings WHERE id=1");
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $admin_email = $row['contact_email'];
        }

        $order_detail = '';
        $statement = $pdo->prepare("SELECT * FROM tbl_payment WHERE payment_id=?");
        $statement->execute(array($_POST['payment_id']));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {

            if ($row['payment_method'] == 'PayPal'):
                $payment_details = '
Transaction Id: ' . $row['txnid'] . '<br>
        		';
            elseif ($row['payment_method'] == 'Stripe'):
                $payment_details = '
Transaction Id: ' . $row['txnid'] . '<br>
Card number: ' . $row['card_number'] . '<br>
Card CVV: ' . $row['card_cvv'] . '<br>
Card Month: ' . $row['card_month'] . '<br>
Card Year: ' . $row['card_year'] . '<br>
        		';
            elseif ($row['payment_method'] == 'Bank Deposit'):
                $payment_details = '
Transaction Details: <br>' . $row['bank_transaction_info'];
            endif;

            $order_detail .= '
Customer Name: ' . $row['customer_name'] . '<br>
Customer Email: ' . $row['customer_email'] . '<br>
Payment Method: ' . $row['payment_method'] . '<br>
Payment Date: ' . $row['payment_date'] . '<br>
Payment Details: <br>' . $payment_details . '<br>
Paid Amount: ' . $row['paid_amount'] . '<br>
Payment Status: ' . $row['payment_status'] . '<br>
Shipping Status: ' . $row['shipping_status'] . '<br>
Payment Id: ' . $row['payment_id'] . '<br>
            ';
        }

        $i = 0;
        $statement = $pdo->prepare("SELECT * FROM tbl_order WHERE payment_id=?");
        $statement->execute(array($_POST['payment_id']));
        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $i++;
            $order_detail .= '
<br><b><u>Product Item ' . $i . '</u></b><br>
Product Name: ' . $row['product_name'] . '<br>
Size: ' . $row['size'] . '<br>
Color: ' . $row['color'] . '<br>
Quantity: ' . $row['quantity'] . '<br>
Unit Price: ' . $row['unit_price'] . '<br>
            ';
        }

        $statement = $pdo->prepare("INSERT INTO tbl_customer_message (subject,message,order_detail,cust_id) VALUES (?,?,?,?)");
        $statement->execute(array($subject_text, $message_text, $order_detail, $_POST['cust_id']));

        // sending email
        $to_customer = $cust_email;
        $message = '
<html><body>
<h3>Message: </h3>
' . $message_text . '
<h3>Order Details: </h3>
' . $order_detail . '
</body></html>
';
        $headers = 'From: ' . $admin_email . "\r\n" .
            'Reply-To: ' . $admin_email . "\r\n" .
            'X-Mailer: PHP/' . phpversion() . "\r\n" .
            "MIME-Version: 1.0\r\n" .
            "Content-Type: text/html; charset=ISO-8859-1\r\n";

        // Sending email to admin                  
        mail($to_customer, $subject_text, $message, $headers);

        $success_message = 'Your email to customer is sent successfully.';

    }
}
?>
<?php
if ($error_message != '') {
    echo "<script>alert('" . $error_message . "')</script>";
}
if ($success_message != '') {
    echo "<script>alert('" . $success_message . "')</script>";
}
?>

<section class="content-header">
    <div class="content-header-left">
        <h1>Đơn hàng</h1>
    </div>
</section>


<section class="content">

    <div class="row">
        <div class="col-md-12">


            <div class="box box-info">

                <div class="box-body table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Chi tiết khách hàng</th>
                                <th>Thông tin chi tiết sản phẩm</th>
                                <th>Thông tin thanh toán</th>
                                <th>Số tiền thanh toán</th>
                                <th>Tình trạng thanh toán</th>
                                <th>Tình trạng giao hàng</th>
                                <th>Thay đổi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 0;
                            $statement = $pdo->prepare("SELECT * FROM tbl_payment ORDER by id DESC");
                            $statement->execute();
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($result as $row) {
                                $i++;
                                ?>
                                <tr class="<?php if ($row['payment_status'] == 'Pending') {
                                    echo 'bg-r';
                                } else {
                                    echo 'bg-g';
                                } ?>">
                                    <td>
                                        <?php echo $i; ?>
                                    </td>
                                    <td>
                                        <b>Id:</b>
                                        <?php echo $row['customer_id']; ?><br>
                                        <b>Họ tên:</b><br>
                                        <?php echo $row['customer_name']; ?><br>
                                        <b>Email:</b><br>
                                        <?php echo $row['customer_email']; ?><br><br>
                                    </td>
                                    <td>
                                        <?php
                                        $statement1 = $pdo->prepare("SELECT * FROM tbl_order WHERE payment_id=?");
                                        $statement1->execute(array($row['payment_id']));
                                        $result1 = $statement1->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result1 as $row1) {
                                            echo '<b>Tên sản phẩm:</b> ' . $row1['product_name'];
                                            echo '<br>(<b>Size:</b> ' . $row1['size'];
                                            echo ', <b>Màu:</b> ' . $row1['color'] . ')';
                                            echo '<br>(<b>Số lượng:</b> ' . $row1['quantity'];
                                            echo ', <b>Đơn giá:</b> ' . $row1['unit_price'] . ')';
                                            echo '<br><br>';
                                        }
                                        ?>
                                    </td>
                                    <td>

                                        <b>Phương thức thanh toán:</b>
                                        <?php echo '<span style="color:red;"><b>' . $row['payment_method'] . '</b></span>'; ?><br>
                                        <b>Mã đơn hàng:</b>
                                        <?php echo $row['payment_id']; ?><br>
                                        <b>Ngày:</b>
                                        <?php echo $row['payment_date']; ?><br>
                                        <b>Thông tin giao dịch:</b> <br>
                                        <?php echo $row['bank_transaction_info']; ?><br>
                                    </td>
                                    <td>
                                        <?php echo $row['paid_amount']; ?>
                                    </td>
                                    <td>
                                        <?php echo $row['payment_status']; ?>
                                        <br><br>
                                        <?php
                                        if ($row['payment_status'] == 'Chưa giải quyết') {
                                            ?>
                                            <a href="order-change-status.php?id=<?php echo $row['id']; ?>&task=Hoàn thành"
                                                class="btn btn-warning btn-xs" style="width:100%;margin-bottom:4px;">Hoàn thành</a>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo $row['shipping_status']; ?>
                                        <br><br>
                                        <?php
                                        if ($row['payment_status'] == 'Hoàn thành') {
                                            if ($row['shipping_status'] == 'Chưa giải quyết') {
                                                ?>
                                                <a href="shipping-change-status.php?id=<?php echo $row['id']; ?>&task=Hoàn thành"
                                                    class="btn btn-warning btn-xs" style="width:100%;margin-bottom:4px;">Hoàn thành</a>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-danger btn-xs"
                                            data-href="order-delete.php?id=<?php echo $row['id']; ?>" data-toggle="modal"
                                            data-target="#confirm-delete" style="width:100%;">Xóa</a>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>


</section>


<div class="modal fade" id="confirm-delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                Bạn có chắc chắn muốn xóa mục này?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Hủy</button>
                <a class="btn btn-danger btn-ok">Xóa</a>
            </div>
        </div>
    </div>
</div>


<?php require_once('footer.php'); ?>