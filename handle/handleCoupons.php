<?php
session_start();
require_once __DIR__ . '/../config/db.php';

// if (
//     isset($_POST['coupon']) && !empty($_POST['coupon'])
// ) {
$couponCode = $_POST['coupon_code'];


$errors = [];
if (empty($couponCode)) {
    $errors['coupon_code'] = "Coupon code is required";
    header("Location: ../cart.php");
    exit();
}

$sql = "select * from coupons where code = ? limit 1";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $couponCode);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$coupon = mysqli_fetch_assoc($result);
if (!$coupon) {
    $errors['coupon_code'] = "Coupon code is invalid";
    header("Location: ../cart.php");
    exit();
}
$_SESSION['coupon'] = $coupon;
$_SESSION['success'] = ['Coupon applied successfully'];
header("Location: ../cart.php");
exit();
// } else {
//     $errors['coupon_code'] = "Coupon code is required";
//     header("Location: ../cart.php");
//     exit();
// }
