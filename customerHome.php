<?php
    session_start();
    if (!isset($_SESSION["user"])) {
        header("Location: login.php");
        exit;
    }
    require_once "db.php";

    $userLocation = $_SESSION["user"]["city"];

    $products = $db->query("SELECT * FROM products 
                            WHERE productLocation = lower('$userLocation') AND expirationDate > CURDATE() ")->fetchAll(PDO::FETCH_ASSOC);
    $i = 1;
    $len = count($products);
    
    // Add to Cart Operation
    if (isset($_GET["addToCart"]) && isset($_GET["count"])) {
        $id = $_GET["addToCart"];
        $count = $_GET["count"];

        addToCart($id);
        header("Location: customerHome.php");
    }

    // Search Operation
    if (!empty($_POST)) {
        $searchKey = $_POST["searchKey"];
        $userLocation = $_SESSION["user"]["city"];
        if($searchKey == "") {
            $products = $db->query("SELECT * FROM products 
                                    WHERE productLocation = lower('$userLocation') AND expirationDate > CURDATE() ")->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $products = $db->query("SELECT * FROM products 
                                    WHERE title LIKE lower('%$searchKey%') AND productLocation = lower('$userLocation')
                                    AND expirationDate > CURDATE() ")->fetchAll(PDO::FETCH_ASSOC);
        }

        $_SESSION["searchKey"] = $searchKey;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer's Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/49131ae832.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="assets/custHome.css">
    <link rel="stylesheet" href="assets/headers.css">
    <style>
        .col-c {
            flex: 0 0 auto;
            width: 9.5%;
        }
    </style>
</head>
<body>
    <!-- SVG Symbol Packet -->
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="bootstrap" viewBox="0 0 118 94">
            <title>Bootstrap</title>
            <path fill-rule="evenodd" clip-rule="evenodd" d="M24.509 0c-6.733 0-11.715 5.893-11.492 12.284.214 6.14-.064 14.092-2.066 20.577C8.943 39.365 5.547 43.485 0 44.014v5.972c5.547.529 8.943 4.649 10.951 11.153 2.002 6.485 2.28 14.437 2.066 20.577C12.794 88.106 17.776 94 24.51 94H93.5c6.733 0 11.714-5.893 11.491-12.284-.214-6.14.064-14.092 2.066-20.577 2.009-6.504 5.396-10.624 10.943-11.153v-5.972c-5.547-.529-8.934-4.649-10.943-11.153-2.002-6.484-2.28-14.437-2.066-20.577C105.214 5.894 100.233 0 93.5 0H24.508zM80 57.863C80 66.663 73.436 72 62.543 72H44a2 2 0 01-2-2V24a2 2 0 012-2h18.437c9.083 0 15.044 4.92 15.044 12.474 0 5.302-4.01 10.049-9.119 10.88v.277C75.317 46.394 80 51.21 80 57.863zM60.521 28.34H49.948v14.934h8.905c6.884 0 10.68-2.772 10.68-7.727 0-4.643-3.264-7.207-9.012-7.207zM49.948 49.2v16.458H60.91c7.167 0 10.964-2.876 10.964-8.281 0-5.406-3.903-8.178-11.425-8.178H49.948z"></path>
        </symbol>
        <symbol id="people-circle" viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
        </symbol>
    </svg>
    <!-- Header -->
    <header>
        <div class="px-3 py-2 bg-dark text-white">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                    <a href="#" class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-white text-decoration-none">
                        <div class="fas fa-shopping-cart"></div>
                    </a>
                    <ul class="nav col-12 col-lg-auto my-2 justify-content-center my-md-0 text-small">
                        <li>
                            <a href="profile.php" class="nav-link text-white hover">
                                <svg class="bi d-block mx-auto mb-1" width="24" height="24"><use xlink:href="#people-circle"/></svg>
                                Profile
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="container-fluid row px-3 py-2 border-bottom mb-3 d-flex flex-wrap align-items-center justify-content-center mb-2">
            <div class="col-4">
                <form method="post" class="col-8 d-flex">
                    <input type="search" class="form-control" name="searchKey" value="<?= isset($_SESSION["searchKey"]) ? $_SESSION["searchKey"] : "" ?>" placeholder="Search..." aria-label="Search">
                    <button type="submit" class="mx-3 btn btn-dark fas fa-search"></button>
                </form>
            </div>
                <div class="col-4 d-flex justify-content-center align-items-center">
                <h3 class="m-0">PRODUCTS</h3>
            </div>
                <div class="col-4 d-flex align-items-center justify-content-end container-fluid">
                <a href="cart.php"><button type="button" class="btn btn-dark">Shopping Cart</button></a>
            </div>
        </div>
    </header>

    <!-- Products -->
    <section class="section-products">
        <div class="container">
            <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div style="background: url('assets/img/<?= $product["expirationImage"] ?>') no-repeat center; 
                                background-size:contain; height: 75%" class="single-product">
                        <div class="part-1">
                            <ul>
                                <li><a href="?addToCart=<?= $product["id"] ?>&count=1"><i class="fas fa-shopping-cart"></i></a></li>
                            </ul>
                        </div>
                        <div class="part-2">
                            <h3 class="product-title"><?= $product["title"] ?></h3>
                            <?php if(isDiscounted($product["id"])): ?>
                                <h4 class="product-old-price"><?= $product["normalPrice"] ?></h4>
                            <?php endif; ?>
                            <h4 class="product-price"><?= getDiscountedPrice($product["id"]) ?> ₺</h4>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </div>
    </section>
</body>
</html>