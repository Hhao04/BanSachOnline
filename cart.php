<?php
global $conn;
require "inc/myconnect.php";
ob_start();
?>
<?php
require "login.php";
if (!isset($_SESSION['txtus'])) // If session is not set then redirect to Login Page
{
    header("Location:giohangchuacodnhap.php");
}
?>
<?php
include "head.php"
?>
<?php
$title = "";
$name = "";
?>
<?php
include "top.php"
?>
<?php
include "header.php"
?>
<?php
include "navigation.php"
?>
<!--//////////////////////////////////////////////////-->
<!--///////////////////Cart Page//////////////////////-->
<!--//////////////////////////////////////////////////-->
<?php
if (is_countable($_SESSION['cart']) == 0) {
    header('Location: baogiohangtrong.php');
}
?>
<div id="page-content" class="single-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <ul class="breadcrumb">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="cart.php">Giỏ hàng</a></li>
                </ul>
            </div>
        </div>
        <div class="cart">
            <p><?php
                $ok = 1;
                if (isset($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $key => $value) {
                        if (isset($key)) {
                            $ok = 2;
                        }
                    }
                }

                if ($ok == 2) {
                    echo "Có " . count($_SESSION['cart']) . " sách trong giỏ hàng ";
                } else {
                    echo   "<p>Không có có Sách nào trong giỏ hàng</p>";
                }

                $sl = count($_SESSION['cart']);
                ?>
            </p>
        </div>
        <?php
        require "inc/myconnect.php";

        if (isset($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $key  => $value) {
                $item[] = $key;
            }
            $str = implode(",", $item);
            $query = "SELECT s.ID,s.Ten,s.date,s.Gia,s.HinhAnh,s.KhuyenMai,s.giakhuyenmai,s.Mota, n.Ten as Tennhasx,s.Manhasx
				from sanpham s 
				LEFT JOIN nhaxuatban n on n.ID = s.Manhasx
				 WHERE  s.id  in ($str)";
            $result = $conn->query($query);
            $total = 0;
            foreach ($result as $s) {
                // Tính thành tiền cho từng sản phẩm
                $subtotal = $s["KhuyenMai"] ? $_SESSION['cart'][$s["ID"]] * $s["giakhuyenmai"] : $_SESSION['cart'][$s["ID"]] * $s["Gia"];
                $total += $subtotal;
                ?>

                <div class="row">
                    <form name="form5" id="ff5" method="POST" action="removecart.php">
                        <div class="product well">
                            <div class="col-md-3">
                                <div class="image">
                                    <img src="images/<?php echo $s["HinhAnh"] ?>" style="width:300px;height:300px" />
                                </div>
                            </div>
                            <div class="col-md-9">
                                <div class="caption">
                                    <div class="name"><h3><a href="product.php?id=<?php echo $s["ID"] ?>"><?php echo $s["Ten"] ?></a></h3></div>
                                    <div class="info">
                                        <ul>
                                            <li>Nhà xuất bản: <?php echo $s["Tennhasx"] ?></li>
                                        </ul>
                                    </div>
                                    <?php
                                    if ($s["KhuyenMai"] == true) {
                                        ?>
                                        <div class="price"><?php echo $s["giakhuyenmai"] ?>.000 VNĐ</div>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    if ($s["KhuyenMai"] == false) {
                                        ?>
                                        <div class="price"><?php echo $s["Gia"] ?>.000 VNĐ</div>
                                        <?php
                                    }
                                    ?>

                                    <label>Số lượng: </label>
                                    <input class="form-inline quantity" style="margin-right: 80px;width:50px" min="1" max="99" type="number" name="qty[<?php echo $s["ID"] ?>]" value="<?php echo $_SESSION['cart'][$s["ID"]] ?>" oninput="updateTotal(this, <?php echo $s["KhuyenMai"] ? $s["giakhuyenmai"] : $s["Gia"] ?>)">
                                    <div>
                                        <input type="submit" name="update" style="margin-top:31px" value="cập nhật số tiền" class="btn btn-2" />
                                    </div>
                                    <hr>
                                    <input type="submit" name="remove" value="xóa Sách này" class="btn btn-default pull-right" />
                                    <input type="hidden" name="idsprm" value="<?php echo $s["ID"] ?>" />

                                    <label style="color:red" id="subtotalLabel_<?php echo $s["ID"] ?>">Thành tiền: <?php echo number_format($subtotal, 0, ',', '.') ?> VNĐ</label>
                                </div>
                            </div>

                            <div class="clear"></div>
                        </div>
                    </form>
                    <?php
                    if ($s["KhuyenMai"] == true) {
                        ?>
                        <?php
                        $total += $_SESSION['cart'][$s["ID"]] * $s["giakhuyenmai"] ?>
                        <?php
                    }
                    ?>
                    <?php
                    if ($s["KhuyenMai"] == false) {
                        ?>
                        <?php
                        $total += $_SESSION['cart'][$s["ID"]] * $s["Gia"] ?>
                        <?php
                    }
                    ?>

                </div>
                <?php
            }
        }
        ?>

        <div class="row">
            <a href="rmcart.php" class="btn btn-2" style="margin-bottom:31px">Xóa hết giỏ hàng</a>
            <div class="col-md-4 col-md-offset-8">
                <center><a href="index.php" class="btn btn-1" style="margin-left:-76px">Chọn những sách khác</a></center>
            </div>
            <div class="row">
                <div class="pricedetails">
                    <div class="col-md-4 col-md-offset-8">
                        <table style="margin-right:31px">
                            <h6>Price Details</h6>
                            <tr>
                                <td>Số lượng sách </td>
                                <td id="totalQuantity"><?php echo $sl ?></td>
                            </tr>
                            <tr style="border-top: 1px solid #333">
                                <td><h5>Tổng cộng</h5></td>
                                <td id="totalAmount"><?php echo number_format($total, 0, ',', '.') ?> VNĐ</td>
                            </tr>
                        </table>
                        <center><a href="dathang.php" class="btn btn-1">Đặt hàng</a></center>
                    </div>
                </div>
            </div>

            <script>
                function updateTotal(input, price) {
                    var quantity = input.value;
                    var subtotal = quantity * price;
                    console.log("updateTotal - Quantity:", quantity, "Price:", price, "Subtotal:", subtotal);

                    // Cập nhật số lượng sách
                    updateTotalQuantity();

                    var subtotalLabelId = "subtotalLabel_" + input.name.split("[")[1].replace("]", "");
                    var subtotalLabel = document.getElementById(subtotalLabelId);
                    subtotalLabel.textContent = "Thành tiền: " + formatCurrency(subtotal) + " VNĐ";

                    // Cập nhật tổng cộng
                    updateTotalAmount();
                }

                function updateTotalAmount() {
                    let totalAmount = 0;

                    const subtotalLabels = document.querySelectorAll('[id^="subtotalLabel_"]');
                    subtotalLabels.forEach(function(subtotalLabel) {
                        const subtotalValue = parseInt(subtotalLabel.textContent.replace(/\D/g, ''));
                        totalAmount += subtotalValue;
                    });

                    console.log("updateTotalAmount - Total Amount:", totalAmount);

                    var totalAmountElement = document.getElementById('totalAmount');
                    totalAmountElement.textContent = formatCurrency(totalAmount) + " VNĐ";
                }

                function updateTotalQuantity() {
                    let totalQuantity = 0;

                    const quantityInputs = document.querySelectorAll('.form-inline.quantity');
                    quantityInputs.forEach(function(quantityInput) {
                        totalQuantity += parseInt(quantityInput.value);
                    });

                    console.log("updateTotalQuantity - Total Quantity:", totalQuantity);

                    var totalQuantityElement = document.getElementById('totalQuantity');
                    totalQuantityElement.textContent = totalQuantity;
                }

                function formatCurrency(amount) {
                    return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
            </script>



        </div>
    </div>
</div>
</div>
<?php
include "footer.php"
?>
</body>
</html>
<?php ob_end_flush(); ?>
