<?php
global $conn;
require "inc/myconnect.php";

if (isset($_POST['Dat'])) {
    if (isset($_SESSION['cart'])) {

        // Tính tổng số lượng sản phẩm trong giỏ hàng
        $totalQuantity = array_sum($_SESSION['cart']);

        foreach ($_SESSION['cart'] as $key => $value) {
            $item[] = $key;
        }
        $str = implode(",", $item);
        $query = "SELECT s.ID,s.Ten,s.date,s.Gia,s.HinhAnh,s.KhuyenMai,s.giakhuyenmai,s.Mota, n.Ten as Tennhasx,s.Manhasx
				from sanpham s 
				LEFT JOIN nhaxuatban n on n.ID = s.Manhasx
				 WHERE  s.id  in ($str)";
        $result = $conn->query($query);

        // Tính tổng giá trị đơn hàng
        $total = $_POST['total'];
        $totalkcodv = $_POST['totalkcodv'];

        $email =  $_SESSION['email'];
        $ngaygiao = $_POST['date'];
        $tenkh = $_SESSION['HoTen'];
        $diachi = $_POST['diachi'];
        $dienthoai =  $_SESSION['dienthoai'];
        $hinhthucthanhtoan = $_POST['hinhthuctt'];
        $ma[] = $_POST["madv"];
        $madv = implode(",", $ma);

        // Nếu có sản phẩm trong giỏ hàng
        if ($totalQuantity > 0) {
            // Tạo hóa đơn
            $sql1 = "INSERT INTO hoadon (emailkh, ngaygiao, tenkh, diachi, dienthoai, hinhthucthanhtoan, thanhtien)
                VALUES ('$email','$ngaygiao','$tenkh','$diachi','$dienthoai','$hinhthucthanhtoan','$total');";

            if ($conn->query($sql1) === TRUE) {
                $sodh = mysqli_insert_id($conn);

                foreach ($result as $s) {
                    $masp = $s["ID"];
                    if ($s["KhuyenMai"] == true) {
                        $dongia = $s["giakhuyenmai"];
                    } else {
                        $dongia = $s["Gia"];
                    }

                    $sl = $_SESSION['cart'][$s["ID"]];
                    $thanhtien = $sl * $dongia;

                    // Tạo chi tiết hóa đơn
                    $sql2 = "INSERT INTO  chitiethoadon (sodh, masp, soluong, dongia, thanhtien, madv) 
                       VALUES ('$sodh','$masp','$sl','$dongia','$thanhtien','$madv');";

                    if ($conn->query($sql2) !== TRUE) {
                        echo "Error: " . $sql2 . "<br>" . $conn->error;
                    }
                }

                // Đặt hàng thành công, chuyển hướng đến trang xác nhận đơn hàng
                header('Location: xacnhandonhang.php');

                // Xóa giỏ hàng
                unset($_SESSION['cart']);
                exit();
            } else {
                echo "Error: " . $sql1 . "<br>" . $conn->error;
            }
        }
    }
}
?>
