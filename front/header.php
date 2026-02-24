<?php
include("../back/conn.php");

// Verificar se o utilizador está logado
$isLoggedIn = isset($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KitMania</title>
</head>

<body>

    <!--  Preloader  -->
    <div class="preloader">
        <div class="status-mes">
            <div class="bigSqr">
                <div class="square first"></div>
                <div class="square second"></div>
                <div class="square third"></div>
                <div class="square fourth"></div>
            </div>
            <div class="text_loading text-center">loading</div>
        </div>
    </div>

    <!--  Start Header  -->
    <header id="header_area">
        <div class="header_top_area">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="hdr_tp_left">
                            <div class="call_area">
                                <span class="single_con_add"><i class="fa fa-phone"></i> +0123456789</span>
                                <span class="single_con_add"><i class="fa fa-envelope"></i> example@gmail.com</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-xs-12 col-sm-6">
                        <ul class="hdr_tp_right text-right">
                            <?php if ($isLoggedIn): ?>

                                <?php if (!empty($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
                                    <li class="account_area profile-dropdown">
                                        <a href="admin/index.html">
                                            <i class="bi bi-gear-fill"></i>
                                            <?php echo $lang === 'en' ? 'Admin Panel' : 'Painel de Admin'; ?>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <li class="account_area profile-dropdown">
                                    <a href="profile/">
                                        <i class="fa fa-user"></i>
                                        <?php echo $lang === 'en' ? 'Profile' : 'Perfil'; ?>
                                    </a>
                                </li>

                            <?php else: ?>

                                <li class="account_area">
                                    <a href="login.php">
                                        <i class="fa fa-lock"></i>
                                        <?php echo t('Login / Register'); ?>
                                    </a>
                                </li>

                            <?php endif; ?>

                            <li class="lan_area">
                                <a href="#">
                                    <i class="fa fa-language"></i>
                                    <?php echo t('Language'); ?>
                                    <i class="fa fa-caret-down"></i>
                                </a>
                                <ul class="csub-menu">
                                    <li>
                                        <a href="?lang=en" class="<?= $lang === 'en' ? 'active' : '' ?>">
                                            <?= t('English') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="?lang=pt" class="<?= $lang === 'pt' ? 'active' : '' ?>">
                                            <?= t('Português') ?>
                                        </a>
                                    </li>
                                </ul>
                            </li>

                            <li class="currency_area">
                                <a href="#">
                                    <i class="fa fa-gg"></i> $USD <i class="fa fa-caret-down"></i>
                                </a>
                                <ul class="csub-menu">
                                    <li><a href="#">€Euro</a></li>
                                    <li><a href="#">৳BDT</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> <!--  HEADER START  -->

        <div class="header_btm_area">
            <div class="container">
                <div class="row">
                    <div class="col-xs-12 col-sm-12 col-md-3">
                        <a class="logo" href="home.php">
                            <img alt="" src="../assets/img/logo.png">
                        </a>
                    </div><!--  End Col -->

                    <div class="col-xs-12 col-sm-12 col-md-9 text-right">
                        <div class="menu_wrap">
                            <div class="main-menu">
                                <nav>
                                    <ul>
                                        <li>
                                            <a href="home.php">
                                                <?php echo t('Home'); ?>
                                            </a>
                                        </li>

                                        <li>
                                            <a href="shop.html">
                                                Shop <i class="fa fa-angle-down"></i>
                                            </a>
                                            <!-- Sub Menu -->
                                            <ul class="sub-menu">
                                                <li><a href="product-details.html">Product Details</a></li>
                                                <li><a href="cart.html">Cart</a></li>
                                                <li><a href="checkout.html">Checkout</a></li>
                                                <li><a href="wishlist.html">Wishlist</a></li>
                                            </ul>
                                        </li>

                                        <li>
                                            <a href="shop.html">
                                                Men <i class="fa fa-angle-down"></i>
                                            </a>
                                            <!-- Mega Menu -->
                                            <div class="mega-menu mm-4-column mm-left">
                                                <div class="mm-column mm-column-link float-left">
                                                    <h3>Men</h3>
                                                    <a href="#">Blazers</a>
                                                    <a href="#">Jackets</a>
                                                    <a href="#">Collections</a>
                                                    <a href="#">T-Shirts</a>
                                                    <a href="#">jens pant's</a>
                                                    <a href="#">sports shoes</a>
                                                </div>

                                                <div class="mm-column mm-column-link float-left">
                                                    <h3>Women</h3>
                                                    <a href="#">Blazers</a>
                                                    <a href="#">Jackets</a>
                                                    <a href="#">Collections</a>
                                                    <a href="#">T-Shirts</a>
                                                    <a href="#">jens pant's</a>
                                                    <a href="#">sports shoes</a>
                                                </div>

                                                <div class="mm-column mm-column-link float-left">
                                                    <h3>Jackets</h3>
                                                    <a href="#">Blazers</a>
                                                    <a href="#">Jackets</a>
                                                    <a href="#">Collections</a>
                                                    <a href="#">T-Shirts</a>
                                                    <a href="#">jens pant's</a>
                                                    <a href="#">sports shoes</a>
                                                </div>

                                                <div class="mm-column mm-column-link float-left">
                                                    <h3>jens pant's</h3>
                                                    <a href="#">Blazers</a>
                                                    <a href="#">Jackets</a>
                                                    <a href="#">Collections</a>
                                                    <a href="#">T-Shirts</a>
                                                    <a href="#">jens pant's</a>
                                                    <a href="#">sports shoes</a>
                                                </div>
                                            </div>
                                        </li>

                                        <li>
                                            <a href="#">
                                                Women <i class="fa fa-angle-down"></i>
                                            </a>
                                            <!-- Mega Menu -->
                                            <div class="mega-menu mm-3-column mm-left">
                                                <div class="mm-column mm-column-link float-left">
                                                    <h3>Women</h3>
                                                    <a href="#">Blazers</a>
                                                    <a href="#">Jackets</a>
                                                    <a href="#">Collections</a>
                                                    <a href="#">T-Shirts</a>
                                                    <a href="#">jens pant's</a>
                                                    <a href="#">sports shoes</a>
                                                </div>

                                                <div class="mm-column mm-column-link float-left">
                                                    <h3>T-Shirts</h3>
                                                    <a href="#">Blazers</a>
                                                    <a href="#">Jackets</a>
                                                    <a href="#">Collections</a>
                                                    <a href="#">T-Shirts</a>
                                                    <a href="#">jens pant's</a>
                                                    <a href="#">sports shoes</a>
                                                </div>

                                                <div class="mm-column mm-column-link float-left">
                                                    <h3>Jackets</h3>
                                                    <a href="#">Blazers</a>
                                                    <a href="#">Jackets</a>
                                                    <a href="#">Collections</a>
                                                    <a href="#">T-Shirts</a>
                                                    <a href="#">jens pant's</a>
                                                    <a href="#">sports shoes</a>
                                                </div>
                                            </div>
                                        </li>

                                        <li>
                                            <a href="#">
                                                pages <i class="fa fa-angle-down"></i>
                                            </a>
                                            <!-- Sub Menu -->
                                            <ul class="sub-menu">
                                                <li><a href="left-sidebar-blog.html">Left Sidebar Blog</a></li>
                                                <li><a href="right-sidebar-blog.html">Right Sidebar Blog</a></li>
                                                <li><a href="full-width-blog.html">Full Width Blog</a></li>
                                                <li><a href="blog-details.html">Blog Details</a></li>
                                                <li><a href="about-us.html">About Us</a></li>
                                                <li><a href="contact.html">Contact Us</a></li>
                                                <li><a href="404.html">404 Page</a></li>
                                            </ul>
                                        </li>

                                        <li>
                                            <a href="contact.html">contact</a>
                                        </li>
                                    </ul>
                                </nav>
                            </div> <!--  End Main Menu -->

                            <div class="mobile-menu text-right">
                                <nav>
                                    <ul>
                                        <li><a href="home.php">home</a></li>
                                        <li><a href="#">Shop</a>
                                            <!-- Sub Menu -->
                                            <ul>
                                                <li><a href="product-details.html">Product Details</a></li>
                                                <li><a href="cart.html">Cart</a></li>
                                                <li><a href="checkout.html">Checkout</a></li>
                                                <li><a href="wishlist.html">Wishlist</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#">Men</a>
                                            <ul>
                                                <li><a href="#">Blazers</a></li>
                                                <li><a href="#">Jackets</a></li>
                                                <li><a href="#">Collections</a></li>
                                                <li><a href="#">T-Shirts</a></li>
                                                <li><a href="#">jens pant's</a></li>
                                                <li><a href="#">sports shoes</a></li>
                                            </ul>
                                        </li>

                                        <li><a href="#">Women</a>
                                            <ul>
                                                <li><a href="#">gagets</a></li>
                                                <li><a href="#">laptop</a></li>
                                                <li><a href="#">mobile</a></li>
                                                <li><a href="#">lifestyle</a></li>
                                                <li><a href="#">jens pant's</a></li>
                                                <li><a href="#">sports items</a></li>
                                            </ul>
                                        </li>

                                        <li><a href="#">pages</a>
                                            <ul>
                                                <li><a href="blog.html">Blog</a></li>
                                                <li><a href="blog-details.html">Blog Details</a></li>
                                                <li><a href="about-us.html">About Us</a></li>
                                                <li><a href="contact.html">Contact Us</a></li>
                                                <li><a href="404.html">404 Page</a></li>
                                            </ul>
                                        </li>
                                        <li><a href="#">contact</a></li>
                                    </ul>
                                </nav>
                            </div> <!--  End mobile-menu -->

                            <div class="right_menu">
                                <ul class="nav justify-content-end">
                                    <li>
                                        <div class="search_icon">
                                            <i class="fa fa-search search_btn" aria-hidden="true"></i>
                                            <div class="search-box">
                                                <form action="#" method="get">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" placeholder="enter keyword" />
                                                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </li>

                                    <li>
                                        <div class="cart_menu_area">
                                            <div class="cart_icon">
                                                <a href="#"><i class="fa fa-shopping-bag " aria-hidden="true"></i></a>
                                                <span class="cart_number">2</span>
                                            </div>

                                            <!-- Mini Cart Wrapper -->
                                            <div class="mini-cart-wrapper">
                                                <!-- Product List -->
                                                <div class="mc-pro-list fix">
                                                    <div class="mc-sin-pro fix">
                                                        <a href="#" class="mc-pro-image float-left">
                                                            <img src="../assets/img/mini-cart/1.jpg" alt="" />
                                                        </a>
                                                        <div class="mc-pro-details fix">
                                                            <a href="#">This is Product Name</a>
                                                            <span>1x$25.00</span>
                                                            <a class="pro-del" href="#">
                                                                <i class="fa fa-times-circle"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="mc-sin-pro fix">
                                                        <a href="#" class="mc-pro-image float-left">
                                                            <img src="../assets/img/mini-cart/2.jpg" alt="" />
                                                        </a>
                                                        <div class="mc-pro-details fix">
                                                            <a href="#">This is Product Name</a>
                                                            <span>1x$25.00</span>
                                                            <a class="pro-del" href="#">
                                                                <i class="fa fa-times-circle"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Sub Total -->
                                                <div class="mc-subtotal fix">
                                                    <h4>Subtotal <span>$50.00</span></h4>
                                                </div>
                                                <!-- Cart Button -->
                                                <div class="mc-button">
                                                    <a href="#" class="checkout_btn">checkout</a>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div><!--  End Col -->
                </div>
            </div>
        </div>
    </header>
    <!--  End Header  -->
</body>

</html>