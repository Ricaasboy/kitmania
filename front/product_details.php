<?php
// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

include("../back/conn.php");

// Força refresh sem cache
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Definir idioma
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'pt';

// Pega o ID do produto da URL (ex: product_details.php?id=5)
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
	$_SESSION['form_errors'][] = $lang === 'en' ? "Invalid product ID" : "ID de produto inválido";
	header("Location: home.php");
	exit;
}

$product_id = intval($_GET['id']);

// Consulta o produto
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
	$_SESSION['form_errors'][] = $lang === 'en' ? "Product not found" : "Produto não encontrado";
	header("Location: home.php");
	exit;
}

$row = $result->fetch_assoc();
$stmt->close();

// Nome traduzido
$name_json = json_decode($row['name'], true);
$product_name = $name_json[$lang] ?? $name_json['pt'] ?? 'Sem nome';

// Descrição traduzida (para o overview)
$desc_json = json_decode($row['description'], true);
$product_desc = $desc_json[$lang] ?? $desc_json['pt'] ?? 'No description available.';

// Preço formatado
$price_formatted = number_format($row['price'], 2, '.', '') . ' €';

// Imagem (placeholder se '0')
$img_src = ($row['thumbnail'] && $row['thumbnail'] !== '0')
	? htmlspecialchars('../' . $row['thumbnail'])
	: '../assets/img/default-product.jpg';
?>

<!DOCTYPE HTML>
<html lang="en-US">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>FancyShop - <?= htmlspecialchars($product_name) ?></title>
	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,500,600,700,800" rel="stylesheet">
	<link rel="stylesheet" href="../assets/css/animate.css" />
	<link rel="stylesheet" href="../assets/css/owl.theme.default.min.css" />
	<link rel="stylesheet" href="../assets/css/owl.carousel.min.css" />
	<link rel="stylesheet" href="../assets/css/meanmenu.min.css" />
	<link rel="stylesheet" href="../assets/css/venobox.css" />
	<link rel="stylesheet" href="../assets/css/font-awesome.css" />
	<link rel="stylesheet" href="../assets/css/bootstrap.min.css" />
	<link rel="stylesheet" href="../assets/css/style.css" />
	<link rel="stylesheet" href="../assets/css/responsive.css" />
	<link rel="stylesheet" href="../assets/icons/bootstrap-icons.css" />
</head>

<body>
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
							<li class="account_area"><a href="login.html"><i class="fa fa-lock"></i> My Account</a></li>
							<li class="lan_area"><a href="#"><i class="fa fa-language "></i> Language <i class="fa fa-caret-down"></i></a>
								<ul class="csub-menu">
									<li><a href="?lang=en">English</a></li>
									<li><a href="?lang=fr">French</a></li>
									<li><a href="?lang=pt">Português</a></li>
								</ul>
							</li>
							<li class="currency_area"><a href="#"><i class="fa fa-gg "></i> $USD <i class="fa fa-caret-down"></i></a>
								<ul class="csub-menu">
									<li><a href="#">€Euro</a></li>
									<li><a href="#">৳BDT</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>

		<div class="header_btm_area">
			<div class="container">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-3">
						<a class="logo" href="home.php"> <img alt="" src="../assets/img/logo.png"></a>
					</div>

					<div class="col-xs-12 col-sm-12 col-md-9 text-right">
						<div class="menu_wrap">
							<div class="main-menu">
								<nav>
									<ul>
										<li><a href="home.php">home</a></li>
										<li><a href="shop.php">Shop <i class="fa fa-angle-down"></i></a>
											<ul class="sub-menu">
												<li><a href="product-details.php">Product Details</a></li>
												<li><a href="cart.php">Cart</a></li>
												<li><a href="checkout.php">Checkout</a></li>
												<li><a href="wishlist.php">Wishlist</a></li>
											</ul>
										</li>
										<li><a href="shop.php?category=men">Men <i class="fa fa-angle-down"></i></a>
											<div class="mega-menu mm-4-column mm-left">
												<!-- ... mega menu ... -->
											</div>
										</li>
										<li><a href="shop.php?category=women">Women <i class="fa fa-angle-down"></i></a>
											<div class="mega-menu mm-3-column mm-left">
												<!-- ... mega menu ... -->
											</div>
										</li>
										<li><a href="#">pages <i class="fa fa-angle-down"></i></a>
											<ul class="sub-menu">
												<li><a href="left-sidebar-blog.php">Left Sidebar Blog</a></li>
												<li><a href="right-sidebar-blog.php">Right Sidebar Blog</a></li>
												<li><a href="full-width-blog.php">Full Width Blog</a></li>
												<li><a href="blog-details.php">Blog Details</a></li>
												<li><a href="about-us.php">About Us</a></li>
												<li><a href="contact.php">Contact Us</a></li>
												<li><a href="404.php">404 Page</a></li>
											</ul>
										</li>
										<li><a href="contact.php">contact</a></li>
									</ul>
								</nav>
							</div>

							<div class="mobile-menu text-right ">
								<nav>
									<!-- ... mobile menu ... -->
								</nav>
							</div>

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
												<!-- ... mini cart ... -->
											</div>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>
	<!--  End Header  -->

	<!-- Page item Area -->
	<div id="page_item_area">
		<div class="container">
			<div class="row">
				<div class="col-sm-6 text-left">
					<h3>Shop Details</h3>
				</div>

				<div class="col-sm-6 text-right">
					<ul class="p_items">
						<li><a href="home.php">home</a></li>
						<li><a href="#">category</a></li>
						<li><span><?= htmlspecialchars($product_name) ?></span></li>
					</ul>
				</div>
			</div>
		</div>
	</div>

	<!-- Product Details Area  -->
	<div class="prdct_dtls_page_area">
		<div class="container">
			<div class="row">
				<!-- Product Details Image -->
				<div class="col-md-6 col-xs-12">
					<div class="pd_img fix">
						<a class="venobox" href="<?= $img_src ?>"><img src="<?= $img_src ?>" alt="<?= htmlspecialchars($product_name) ?>" /></a>
					</div>
				</div>

				<!-- Product Details Content -->
				<div class="col-md-6 col-xs-12">
					<div class="prdct_dtls_content">
						<a class="pd_title" href="#"><?= htmlspecialchars($product_name) ?></a>
						<div class="pd_price_dtls fix">
							<!-- Product Price -->
							<div class="pd_price">
								<span class="new"><?= $price_formatted ?></span>
							</div>
						</div>

						<div class="pd_text">
							<h4>overview:</h4>
							<p><?= nl2br(htmlspecialchars($product_desc)) ?></p>
						</div>

						<div class="pd_img_size fix">
							<h4>size:</h4>
							<a href="#">s</a>
							<a href="#">m</a>
							<a href="#">l</a>
							<a href="#">xl</a>
							<a href="#">xxl</a>
						</div>

						<div class="pd_clr_qntty_dtls fix">
							<div class="pd_qntty_area">
								<h4>quantity:</h4>
								<div class="pd_qty fix">
									<input value="1" name="qttybutton" class="cart-plus-minus-box" type="number">
								</div>
							</div>
						</div>

						<!-- Product Action -->
						<div class="pd_btn fix">
							<a class="btn btn-default acc_btn">add to bag</a>
							<a class="btn btn-default acc_btn btn_icn"><i class="fa fa-heart"></i></a>
						</div>

						<div class="pd_share_area fix">
							<h4>share this on:</h4>
							<div class="pd_social_icon">
								<a class="facebook" href="#"><i class="fa fa-facebook"></i></a>
								<a class="twitter" href="#"><i class="fa fa-twitter"></i></a>
								<a class="vimeo" href="#"><i class="fa fa-vimeo"></i></a>
								<a class="google_plus" href="#"><i class="fa fa-google-plus"></i></a>
								<a class="tumblr" href="#"><i class="fa fa-tumblr"></i></a>
								<a class="pinterest" href="#"><i class="fa fa-pinterest"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<!-- Related Product Area -->
			<div class="related_prdct_area text-center">
				<div class="container">
					<div class="rp_title text-center">
						<h3>Related products</h3>
					</div>

					<div class="row">
						<!-- Placeholder para futuros produtos relacionados -->
						<div class="col-lg-3 col-md-4 col-sm-6">
							<div class="single_product">
								<div class="product_image">
									<img src="../assets/img/product/1.jpg" alt="" />
									<div class="box-content">
										<a href="#"><i class="fa fa-heart-o"></i></a>
										<a href="#"><i class="fa fa-cart-plus"></i></a>
										<a href="#"><i class="fa fa-search"></i></a>
									</div>
								</div>

								<div class="product_btm_text">
									<h4><a href="#">Product Title</a></h4>
									<span class="price">$123.00</span>
								</div>
							</div>
						</div>
						<!-- ... mais placeholders ... -->
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Footer (igual ao original, encurtado para brevidade) -->
	<footer class="footer_area">
		<!-- ... footer original ... -->
	</footer>

	<script src="../assets/js/vendor/jquery-1.12.4.min.js"></script>
	<script src="../assets/js/bootstrap.min.js"></script>
	<script src="../assets/js/jquery.meanmenu.min.js"></script>
	<script src="../assets/js/jquery.mixitup.js"></script>
	<script src="../assets/js/jquery.counterup.min.js"></script>
	<script src="../assets/js/waypoints.min.js"></script>
	<script src="../assets/js/wow.min.js"></script>
	<script src="../assets/js/venobox.min.js"></script>
	<script src="../assets/js/owl.carousel.min.js"></script>
	<script src="../assets/js/simplePlayer.js"></script>
	<script src="../assets/js/main.js"></script>
</body>

</html>