<?php
include 'inc/header.php';
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('location:shop.php');
    exit();
}


if (isset($_POST['confirm_order'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $postalCode = trim($_POST['postal_code']);
    $phone = trim($_POST['phone']);
    $paymentType = $_POST['payment_type'];


    $errors = [];

    if (
        empty($name) ||
        empty($email) ||
        empty($address) ||
        empty($city) ||
        empty($postalCode) ||
        empty($phone)
    ) {
        $errors[] = "All fields are required";
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid Email format";
    }

    if (empty($errors)) {

        $total = 0;
        foreach ($_SESSION['cart'] as $item) {
            $total += $item['price'] * $item['cart_quantity'];
        }

        if (isset($_SESSION['coupon'])) {
            $total -= $_SESSION['coupon']['value'];
        }

        $query = "insert into orders(user_id , customer_name,customer_email ,customer_phone, shipping_address , city , postal_code , payment_method , total) VALUES(? , ?  , ? , ?  , ? , ? , ? , ? , ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "isssssisi", $_SESSION['logged_user']['id'], $name, $email, $phone, $address, $city, $postalCode, $paymentType, $total);
        mysqli_stmt_execute($stmt);
        $orderId = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);


        $sql = "insert into order_items(order_id,product_id,product_title,quantity,unit_price) VALUES(? , ?  , ? , ?  , ?)";
        $stmt = mysqli_prepare($conn, $sql);


        foreach ($_SESSION['cart'] as $productId => $item) {
            mysqli_stmt_bind_param($stmt, "iisii",  $orderId, $productId, $item['title'], $item['cart_quantity'],  $item['price']);
            mysqli_stmt_execute($stmt);
        }
        mysqli_stmt_close($stmt);




        $_SESSION['success'][] = 'order confirmed';

        unset($_SESSION['cart']);
        unset($_SESSION['coupon']);
        header('location:shop.php');
        exit();
    }
}

?>


<section id="cart-add" class="section-p1">

    <div id="coupon">

        <h3>Coupon</h3>

        <input
            type="text"
            name="coupon"
            placeholder="Enter coupon code">

        <button class="normal">
            Apply
        </button>

    </div>


    <div id="subTotal">

        <h3>Confirm Order</h3>


        <form class="col-4" action="confirmOrder.php" method="POST">


            <label>Name</label>
            <input
                type="text"
                name="name" , value="<?= isset($_SESSION['logged_user']['username']) ? $_SESSION['logged_user']['username'] : ''; ?>">


            <label>Email</label>
            <input
                type="email"
                name="email" , value="<?= isset($_SESSION['logged_user']['email']) ? $_SESSION['logged_user']['email'] : ''; ?>">


            <label>Address</label>
            <input
                type="text"
                name="address" , value="<?= isset($_SESSION['logged_user']['address']) ? $_SESSION['logged_user']['address'] : ''; ?>">


            <label>City</label>
            <input
                type="text"
                name="city">


            <label>Postal Code</label>
            <input
                type="number"
                name="postal_code">


            <label>Phone</label>
            <input
                type="text"
                name="phone" , value="<?= isset($_SESSION['logged_user']['phone']) ? $_SESSION['logged_user']['phone'] : ''; ?>">


            <label>Payment Type</label>

            <select name="payment_type">

                <option value="Cash_on_Delivery">
                    Cash on Delivery
                </option>

                <option value="Credit_Card">
                    Credit Card
                </option>

                <option value="Fawry">
                    Fawry
                </option>

            </select>


            <button
                class="normal"
                type="submit"
                name="confirm_order">
                Proceed To Checkout
            </button>


        </form>


    </div>

</section>


<?php include 'inc/footer.php'; ?>