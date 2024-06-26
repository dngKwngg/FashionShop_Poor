<?php require_once('header.php'); ?>

<?php
$statement = $pdo->prepare("SELECT banner_checkout FROM tbl_settings WHERE id=1");
$statement->execute();
$result = $statement->fetchAll(PDO::FETCH_ASSOC);                            
foreach ($result as $row) {
    $banner_checkout = $row['banner_checkout'];
}
?>

<?php
// if(!isset($_SESSION['cart_p_id'])) {
//     header('location: cart.php');
//     exit;
// }
?>

<div class="page-banner" style="background-image: url(assets/uploads/<?php echo $banner_checkout; ?>)">
    <div class="overlay"></div>
    <div class="page-banner-inner">
        <h1>Thanh toán</h1>
    </div>
</div>

<div class="page">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                
                <?php if(!isset($_SESSION['customer'])): ?>
                    <p>
                        <a href="login.php" class="btn btn-md btn-danger"><?php echo LANG_VALUE_160; ?></a>
                    </p>
                <?php else: ?>

                <h3 class="special">Thông tin đặt hàng</h3>
                <div class="cart">
                    <table class="table table-responsive">
                        <tr>
                            <th><!--<?php echo LANG_VALUE_7; ?>-->STT</th>
                            <th><!--<?php echo LANG_VALUE_8; ?>-->Hình ảnh</th>
                            <th><!--<?php echo LANG_VALUE_47; ?>-->Tên sản phẩm</th>
                            <th><!--<?php echo LANG_VALUE_157; ?>-->Kích cỡ</th>
                            <th><!--<?php echo LANG_VALUE_158; ?>-->Màu sắc</th>
                            <th><!--<?php echo LANG_VALUE_159; ?>-->Giá</th>
                            <th><!--<?php echo LANG_VALUE_55; ?>-->Số lượng</th>
                            <th class="text-right"><!--<?php echo LANG_VALUE_82; ?>-->Tổng</th>
                        </tr>
                        <tbody>
							<?php
							$i=0;
							$statement = $pdo->prepare("SELECT                           
                                                        t1.p_id,
                                                        t1.p_name,
                                                        t1.p_current_price,
                                                        t1.p_featured_photo,
                                                        t2.c_id,
                                                        t2.p_quantity,
                                                        t2.size,
                                                        t2.color

                                                        FROM tbl_product t1
                                                        INNER JOIN tbl_cart t2
                                                        ON t1.p_id = t2.p_id
                                                        WHERE t2.cust_id = :cust_id;
                                                        ");
                            $statement->bindParam(':cust_id', $_SESSION['customer']['cust_id']);                            
                            $statement->execute();
							$result = $statement->fetchAll(PDO::FETCH_ASSOC);
                            $table_total_price = 0;
							foreach ($result as $row) {
								$i++;
								?>
								<tr>
									<td><?php echo $i; ?></td>
									<td style="width:130px;"><img src="assets/uploads/<?php echo $row['p_featured_photo']; ?>" alt="<?php echo $row['p_name']; ?>" style="width:100px;"></td>
									<td><?php echo $row['p_name']; ?></td>
									<td><?php echo $row['size']; ?></td>
									<td><?php echo $row['color']; ?></td>
									<td><?php echo $row['p_current_price']; ?></td>
									<td><?php echo $row['p_quantity']; ?></td>
									<td class="text-right"><?php echo $row['p_quantity'] * $row['p_current_price']; ?></td>
                                    
								</tr>
								<?php
                                $table_total_price += $row['p_quantity'] * $row['p_current_price'];
							}
							?>							
						</tbody>          
                        <tr>
                            <th colspan="7" class="total-text">Thanh toán</th>
                            <th class="total-amount"><?php echo $table_total_price; ?>đ</th>
                        </tr>
                        <?php
                        $statement = $pdo->prepare("SELECT amount FROM tbl_shipping_cost WHERE country_id=?");
                        $statement->execute(array($_SESSION['customer']['cust_country']));
                        $total = $statement->rowCount();
                        if($total) {
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($result as $row) {
                                $shipping_cost = $row['amount'];
                            }
                        } else {
                            $statement = $pdo->prepare("SELECT amount FROM tbl_shipping_cost_all WHERE sca_id=1");
                            $statement->execute();
                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($result as $row) {
                                $shipping_cost = $row['amount'];
                            }
                        }                        
                        ?>
                        <tr>
                            <td colspan="7" class="total-text">Phí ship</td>
                            <td class="total-amount"><?php echo $shipping_cost; ?>đ</td>
                        </tr>
                        <tr>
                            <th colspan="7" class="total-text">Thành tiền</th>
                            <th class="total-amount">
                                <?php
                                $final_total = $table_total_price+$shipping_cost;
                                ?>
                                <?php echo $final_total; ?>đ
                            </th>
                        </tr>
                    </table> 
                </div>

                

                <div class="billing-address">
                    <div class="row">
                        <div class="col-md-6">
                            <h3 class="special">Địa chỉ hoá đơn</h3>
                            <table class="table table-responsive table-bordered bill-address">
                                <tr>
                                    <td>Họ và tên</td>
                                    <td><?php echo $_SESSION['customer']['cust_b_name']; ?></p></td>
                                </tr>
                                <tr>
                                    <td>Tên công ty</td>
                                    <td><?php echo $_SESSION['customer']['cust_b_cname']; ?></td>
                                </tr>
                                <tr>
                                    <td>Số điện thoại</td>
                                    <td><?php echo $_SESSION['customer']['cust_b_phone']; ?></td>
                                </tr>
                                <tr>
                                    <td>Quốc gia</td>
                                    <td>
                                        <?php
                                        $statement = $pdo->prepare("SELECT country_name FROM tbl_country WHERE country_id=?");
                                        $statement->execute(array($_SESSION['customer']['cust_b_country']));
                                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result as $row) {
                                            echo $row['country_name'];
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Địa chỉ</td>
                                    <td>
                                        <?php echo nl2br($_SESSION['customer']['cust_b_address']); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Thành phố</td>
                                    <td><?php echo $_SESSION['customer']['cust_b_city']; ?></td>
                                </tr>
                                <tr>
                                    <td>Quận</td>
                                    <td><?php echo $_SESSION['customer']['cust_b_state']; ?></td>
                                </tr>
                                <tr>
                                    <td>Mã zip</td>
                                    <td><?php echo $_SESSION['customer']['cust_b_zip']; ?></td>
                                </tr>                                
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h3 class="special">Địa chỉ nhận hàng</h3>
                            <table class="table table-responsive table-bordered bill-address">
                                <tr>
                                    <td>Họ và tên</td>
                                    <td><?php echo $_SESSION['customer']['cust_s_name']; ?></p></td>
                                </tr>
                                <tr>
                                    <td>Tên công ty</td>
                                    <td><?php echo $_SESSION['customer']['cust_s_cname']; ?></td>
                                </tr>
                                <tr>
                                    <td>Số điện thoại</td>
                                    <td><?php echo $_SESSION['customer']['cust_s_phone']; ?></td>
                                </tr>
                                <tr>
                                    <td>Quốc gia</td>
                                    <td>
                                        <?php
                                        $statement = $pdo->prepare("SELECT country_name FROM tbl_country WHERE country_id=?");
                                        $statement->execute(array($_SESSION['customer']['cust_s_country']));
                                        $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                        foreach ($result as $row) {
                                            echo $row['country_name'];
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Địa chỉ</td>
                                    <td>
                                        <?php echo nl2br($_SESSION['customer']['cust_s_address']); ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Thành phố</td>
                                    <td><?php echo $_SESSION['customer']['cust_s_city']; ?></td>
                                </tr>
                                <tr>
                                    <td>Quận</td>
                                    <td><?php echo $_SESSION['customer']['cust_s_state']; ?></td>
                                </tr>
                                <tr>
                                    <td>Mã zip</td>
                                    <td><?php echo $_SESSION['customer']['cust_s_zip']; ?></td>
                                </tr> 
                            </table>
                        </div>
                    </div>                    
                </div>

                

                <div class="cart-buttons">
                    <ul>
                        <li><a href="cart.php" class="btn btn-primary"><!--<?php echo LANG_VALUE_21; ?> -->Quay lại giỏ hàng</a></li>
                    </ul>
                </div>

				<div class="clear"></div>
                <h3 class="special">Nơi thanh toán</h3>
                <div class="row">
                    
                    	<?php
		                $checkout_access = 1;
		                if(
		                    ($_SESSION['customer']['cust_b_name']=='') ||
		                    ($_SESSION['customer']['cust_b_cname']=='') ||
		                    ($_SESSION['customer']['cust_b_phone']=='') ||
		                    ($_SESSION['customer']['cust_b_country']=='') ||
		                    ($_SESSION['customer']['cust_b_address']=='') ||
		                    ($_SESSION['customer']['cust_b_city']=='') ||
		                    ($_SESSION['customer']['cust_b_state']=='') ||
		                    ($_SESSION['customer']['cust_b_zip']=='') ||
		                    ($_SESSION['customer']['cust_s_name']=='') ||
		                    ($_SESSION['customer']['cust_s_cname']=='') ||
		                    ($_SESSION['customer']['cust_s_phone']=='') ||
		                    ($_SESSION['customer']['cust_s_country']=='') ||
		                    ($_SESSION['customer']['cust_s_address']=='') ||
		                    ($_SESSION['customer']['cust_s_city']=='') ||
		                    ($_SESSION['customer']['cust_s_state']=='') ||
		                    ($_SESSION['customer']['cust_s_zip']=='')
		                ) {
		                    $checkout_access = 0;
		                }
		                ?>
		                <?php if($checkout_access == 0): ?>
		                	<div class="col-md-12">
				                <div style="color:red;font-size:22px;margin-bottom:50px;">
			                        <!-- You must have to fill up all the billing and shipping information from your dashboard panel in order to checkout the order. Please fill up the information going to <a href="customer-billing-shipping-update.php" style="color:red;text-decoration:underline;">this link</a>. -->
                                    Bạn phải điền vào tất cả thông tin thanh toán và giao hàng từ bảng điều khiển của mình để thanh toán đơn đặt hàng. Vui lòng điền thông tin vào <a href="customer-billing-shipping-update.php" style="color:red;text-decoration:underline;">đây</a>.
                                </div>
	                    	</div>
	                	<?php else: ?>
		                	<div class="col-md-4">
		                		
	                            <div class="row">

	                                <div class="col-md-12 form-group">
	                                    <label for="">Chọn phương thức thanh toán *</label>
	                                    <select name="payment_method" class="form-control select2" id="advFieldsStatus">
	                                        <option value="">Chọn một phương pháp</option>
	                                        <option value="Bank Deposit">Chuyển khoản</option>
	                                    </select>
	                                </div>

                                    <form action="payment/bank/init.php" method="post" id="bank_form">
                                        <input type="hidden" name="amount" value="<?php echo $final_total; ?>">
                                        <div class="col-md-12 form-group">
                                            <label for="">Gửi đến</span></label><br>
                                            <?php
                                            $statement = $pdo->prepare("SELECT bank_detail FROM tbl_settings WHERE id=1");
                                            $statement->execute();
                                            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
                                            foreach ($result as $row) {
                                                echo nl2br($row['bank_detail']);
                                            }
                                            ?>
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <label for="">Thông tin giao dịch <br><span style="font-size:12px;font-weight:normal;">(Bao gồm id giao dịch và các thông tin khác một cách chính xác)</span></label>
                                            <textarea name="transaction_info" class="form-control" cols="30" rows="10"></textarea>
                                        </div>
                                        <div class="col-md-12 form-group">
                                            <input type="submit" class="btn btn-primary" value="Thanh toán" name="form3">
                                        </div>
                                    </form>
	                                
	                            </div>
		                            
		                        
		                    </div>
		                <?php endif; ?>
                        
                </div>
                

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>


<?php require_once('footer.php'); ?>