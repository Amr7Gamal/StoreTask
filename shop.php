<?php


include 'inc/header.php';
require_once 'config/db.php';

// limit , offset ???? 
// limit 
// 1 2 3 4 5 6 

$perPage = 3;

$page = max(1, (int)($_GET['page'] ?? 1));


// unset($_SESSION['numberPage']);
// count pages  4 / 3 = 1.75 ceil(1.01111) => 2 page 
$countSql = "select count(*) as total from products where is_active = 1";
$countResult = mysqli_query($conn, $countSql);
$totalProducts = mysqli_fetch_assoc($countResult)['total'];

$totalPages = ceil($totalProducts / $perPage);


$numbers = [];
$numbersItems = 3;
$count = 1;

for ($i = 1; $i <= $totalPages; $i = $i + $numbersItems) {
    for ($j = $i; $j < ($i + $numbersItems); $j++) {
        $numbers[$count][] = $j;
    }
    ++$count;
}

if (!isset($_SESSION['numberPage'])) {
    $_SESSION['numberPage'] = 1;
}



if (isset($_POST['nextNumberPage'])) {
    if (count($numbers) > $_SESSION['numberPage']) {
        $_SESSION['numberPage']++;
        $page = $numbers[$_SESSION['numberPage']][0];
    }
}

if (isset($_POST['previousNumberPage'])) {
    $_SESSION['numberPage']--;
    $page = $numbers[$_SESSION['numberPage']][count($numbers[$_SESSION['numberPage']]) - 1];
}


// print($totalPages);
// Ajax (javascript) 
// offset 
// = 3 - 1 = 2  * 3 = 6 (ignore 6 products)
$offset = ($page - 1) * $perPage;
// query 
$sql = "select * from products where is_active = 1 limit $perPage offset $offset";

$products = mysqli_query($conn, $sql);
?>

<section id="page-header">
    <h2>Super value deals</h2>
    <p>Save more woth coupons & up to 70% off!</p>
</section>


<section id="product1" class="section-p1">
    <h2>Featured Products</h2>
    <p>Summer Collection New Modren Desgin</p>
    <?php include 'inc/success.php' ?>
    <div class="pro-container">

        <?php if (mysqli_num_rows($products) > 0) : ?>
            <?php while ($product = mysqli_fetch_assoc($products)): ?>
                <div class="pro">
                    <img src="admin/upload/products/<?= $product['image'] ?>" alt="proudcts">
                    <div class="des">
                        <span><?= $product['title'] ?></span>
                        <h5></h5>
                        <div class="star ">
                            <i class="fas fa-star "></i>
                            <i class="fas fa-star "></i>
                            <i class="fas fa-star "></i>
                            <i class="fas fa-star "></i>
                            <i class="fas fa-star "></i>
                        </div>
                        <h4> <?= $product['price'] ?> EGP</h4>
                        <form action="shop.php" method="get">


                            <a href="product.php?id=<?= $product['id'] ?>" class="cart">
                                <i class="fas fa-eye"></i>
                            </a>
                        </form>

                    </div>
                </div>
            <?php endwhile; ?>
        <?php else : ?>


            <h1>No Product exits</h1>
        <?php endif; ?>

    </div>
</section>

<section id="pagenation" class="section-p1 d-flex justify-content-center align-items-center gap-2">
    <?php if ($_SESSION['numberPage']  > 1) : ?>
        <form action="shop.php?page=<?= --$_SESSION['idPage'] ?? 1 ?>" method="POST" class="d-inline-flex m-0">
            <button type="submit" name="previousNumberPage" class="pagination-btn" style="background-color: #088178; color: #fff; border: none; border-radius: 4px; padding: 15px 20px; font-size: 20px; cursor: pointer;" title="رجوع للوضع الأول"><i class="fas fa-long-arrow-alt-left"></i></button>
        </form>
    <?php endif; ?>

    <? foreach ($numbers[$_SESSION['numberPage']] as $itemNembers) : ?>
        <a href="shop.php?page=<?php
                                echo $itemNembers;
                                $_SESSION['idPage'] = $itemNembers; ?>" class="<?= ($page == $itemNembers) ? 'active' : '' ?>"><?= $itemNembers ?></a>
    <?php endforeach; ?>

    <?php if (count($numbers) > $_SESSION['numberPage']) : ?>
        <form action="shop.php?page=<?= $_SESSION['idPage'] ?? 1 ?>" method="POST" class="d-inline-flex m-0">
            <button type="submit" name="nextNumberPage" class="pagination-btn" style="background-color: #088178; color: #fff; border: none; border-radius: 4px; padding: 15px 20px; font-size: 20px; cursor: pointer;" title="عرض المزيد"><i class="fas fa-long-arrow-alt-right"></i></button>
        </form>
    <?php endif; ?>
</section>

<section id="newsletter" class="section-p1 section-m1">
    <div class="newstext ">
        <h4>Sign Up For Newletters</h4>
        <p>Get E-mail Updates about our latest shop and <span class="text-warning ">Special Offers.</span></p>
    </div>
    <div class="form ">
        <input type="text " placeholder="Enter Your E-mail... ">
        <button class="normal ">Sign Up</button>
    </div>
</section>

<?php include 'inc/footer.php' ?>