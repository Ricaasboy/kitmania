<?php
// Iniciar sessão se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

include("../back/conn.php");

// Definir idioma (padrão português)
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'pt';

// Cache de nomes de categorias
$categories_cache = [];
function get_category_name($id, $lang, $conn)
{
	global $categories_cache;
	if (isset($categories_cache[$id])) return $categories_cache[$id];

	$stmt = $conn->prepare("SELECT name FROM categories WHERE id = ?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$res = $stmt->get_result();
	$name = '—';
	if ($row = $res->fetch_assoc()) {
		$name_json = json_decode($row['name'], true);
		$name = $name_json[$lang] ?? $name_json['pt'] ?? 'Sem nome';
	}
	$categories_cache[$id] = $name;
	$stmt->close();
	return $name;
}

// Base URL para links (ajuste conforme necessário)
$base_url = ""; // Deixe vazio se for raiz do servidor
$current_page = "home";
?>

<!DOCTYPE HTML>
<html lang="en-US">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Casa - KitMania</title>
	<link href="https://fonts.googleapis.com/css?family=Lato:300,400,500,600,700,800" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Handlee" rel="stylesheet">
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

	<?php include("header.php"); ?>

	<!-- Start Slider Area -->
	<section id="slider_area" class="text-center">
		<div class="slider_active owl-carousel">
			<div class="single_slide"
				style="background-image: url(../assets/img/slider/1.jpg); background-size: cover; background-position: center;">
				<div class="container">
					<div class="single-slide-item-table">
						<div class="single-slide-item-tablecell">
							<div class="slider_content text-left slider-animated-1">
								<p class="animated">New Year 2024</p>
								<h1 class="animated">best shopping</h1>
								<h4 class="animated">Big Sale of This Week 50% off</h4>
								<a href="#" class="btn main_btn animated">shop now</a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="single_slide"
				style="background-image: url(../assets/img/slider/2.jpg); background-size: cover; background-position: center ;">
				<div class="container">
					<div class="single-slide-item-table">
						<div class="single-slide-item-tablecell">
							<div class="slider_content text-center slider-animated-2">
								<p class="animated">Women fashion</p>
								<h1 class="animated">popular style</h1>
								<h4 class="animated">Big Sale of This Week 50% off</h4>
								<a href="#" class="btn main_btn animated">shop now</a>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="single_slide"
				style="background-image: url(../assets/img/slider/3.jpg); background-size: cover; background-position: center ;">
				<div class="container">
					<div class="single-slide-item-table">
						<div class="single-slide-item-tablecell">
							<div class="slider_content text-right slider-animated-3">
								<p class="animated">Men Collection</p>
								<h1 class="animated">popular style</h1>
								<h4 class="animated">Big Sale of This Week 50% off</h4>
								<a href="#" class="btn main_btn animated">shop now</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!-- End Slider Area -->

	<!--  Promo ITEM STRAT  -->
	<section id="promo_area" class="section_padding">
		<div class="container">
			<div class="row">
				<div class="col-lg-4 col-md-6 col-sm-12">
					<a href="#">
						<div class="single_promo">
							<img src="../assets/img/promo/1.jpg" alt="">
							<div class="box-content">
								<h3 class="title">Men</h3>
								<span class="post">2024 Collection</span>
							</div>
						</div>
					</a>
				</div><!--  End Col -->

				<div class="col-lg-4 col-md-6 col-sm-12">
					<a href="#">
						<div class="single_promo">
							<img src="../assets/img/promo/2.jpg" alt="">
							<div class="box-content">
								<h3 class="title">Shoe</h3>
								<span class="post">2024 Collection</span>
							</div>
						</div>
					</a>

					<a href="#">
						<div class="single_promo">
							<img src="../assets/img/promo/4.jpg" alt="">
							<div class="box-content">
								<h3 class="title">Watch</h3>
								<span class="post">2024 Collection</span>
							</div>
						</div>
					</a>

				</div><!--  End Col -->

				<div class="col-lg-4 col-md-6 col-sm-12">
					<a href="#">
						<div class="single_promo">
							<img src="../assets/img/promo/3.jpg" alt="">
							<div class="box-content">
								<h3 class="title">Women</h3>
								<span class="post">2024 Collection</span>
							</div>
						</div>
					</a>
				</div><!--  End Col -->

			</div>
		</div>
	</section>
	<!--  Promo ITEM END -->

	<?php include("products.php"); ?>

	<!-- Special Offer Area -->
	<div class="special_offer_area gray_section">
		<div class="container">
			<div class="row">
				<div class="col-md-5">
					<div class="special_img text-left">
						<img src="../assets/img/special.png" width="370" alt="" class="img-responsive">
						<span class="off_baudge text-center">30% <br /> Off</span>
					</div>
				</div>

				<div class="col-md-7 text-left">
					<div class="special_info">
						<h3>Men Collection 2024</h3>
						<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has
							been the industry's standard dummy Lorem Ipsum</p>
						<a href="#" class="btn main_btn">Shop Now</a>
					</div>
				</div>
			</div>

		</div>
	</div> <!-- End Special Offer Area -->

	<!-- Start Featured product Area -->
	<section id="featured_product" class="featured_product_area section_padding">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<div class="section_title">
						<h2>Featured <span> Products</span></h2>
						<div class="divider"></div>
					</div>
				</div>
			</div>

			<div class="row text-center">
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
				</div> <!-- End Col -->

				<div class="col-lg-3 col-md-4 col-sm-6">
					<div class="single_product">
						<div class="product_image">
							<img src="../assets/img/product/2.jpg" alt="" />
							<div class="box-content">
								<a href="#"><i class="fa fa-heart-o"></i></a>
								<a href="#"><i class="fa fa-cart-plus"></i></a>
								<a href="#"><i class="fa fa-search"></i></a>
							</div>
						</div>

						<div class="product_btm_text">
							<h4><a href="#">Product Title</a></h4>
							<div class="p_rating">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
							</div>
							<span class="price">$123.00</span>
						</div>
					</div>
				</div> <!-- End Col -->

				<div class="col-lg-3 col-md-4 col-sm-6">
					<div class="single_product">
						<div class="product_image">
							<img src="../assets/img/product/3.jpg" alt="" />
							<div class="box-content">
								<a href="#"><i class="fa fa-heart-o"></i></a>
								<a href="#"><i class="fa fa-cart-plus"></i></a>
								<a href="#"><i class="fa fa-search"></i></a>
							</div>
						</div>

						<div class="product_btm_text">
							<h4><a href="#">Product Title</a></h4>
							<div class="p_rating">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
							</div>
							<span class="price">$123.00</span>
						</div>
					</div>
				</div> <!-- End Col -->

				<div class="col-lg-3 col-md-4 col-sm-6">
					<div class="single_product">
						<div class="product_image">
							<img src="../assets/img/product/4.jpg" alt="" />
							<div class="box-content">
								<a href="#"><i class="fa fa-heart-o"></i></a>
								<a href="#"><i class="fa fa-cart-plus"></i></a>
								<a href="#"><i class="fa fa-search"></i></a>
							</div>
						</div>

						<div class="product_btm_text">
							<h4><a href="#">Product Title</a></h4>
							<div class="p_rating">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
							</div>
							<span class="price">$123.00</span>
						</div>
					</div>
				</div> <!-- End Col -->

				<div class="col-lg-3 col-md-4 col-sm-6">
					<div class="single_product">
						<div class="product_image">
							<img src="../assets/img/product/5.jpg" alt="" />
							<div class="box-content">
								<a href="#"><i class="fa fa-heart-o"></i></a>
								<a href="#"><i class="fa fa-cart-plus"></i></a>
								<a href="#"><i class="fa fa-search"></i></a>
							</div>
						</div>

						<div class="product_btm_text">
							<h4><a href="#">Product Title</a></h4>
							<div class="p_rating">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
							</div>
							<span class="price">$123.00</span>
						</div>
					</div>
				</div> <!-- End Col -->

				<div class="col-lg-3 col-md-4 col-sm-6">
					<div class="single_product">
						<div class="product_image">
							<img src="../assets/img/product/6.jpg" alt="" />
							<div class="box-content">
								<a href="#"><i class="fa fa-heart-o"></i></a>
								<a href="#"><i class="fa fa-cart-plus"></i></a>
								<a href="#"><i class="fa fa-search"></i></a>
							</div>
						</div>

						<div class="product_btm_text">
							<h4><a href="#">Product Title</a></h4>
							<div class="p_rating">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
							</div>
							<span class="price">$123.00</span>
						</div>
					</div>
				</div> <!-- End Col -->

				<div class="col-lg-3 col-md-4 col-sm-6">
					<div class="single_product">
						<div class="product_image">
							<img src="../assets/img/product/7.jpg" alt="" />
							<div class="box-content">
								<a href="#"><i class="fa fa-heart-o"></i></a>
								<a href="#"><i class="fa fa-cart-plus"></i></a>
								<a href="#"><i class="fa fa-search"></i></a>
							</div>
						</div>

						<div class="product_btm_text">
							<h4><a href="#">Product Title</a></h4>
							<div class="p_rating">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
							</div>
							<span class="price">$123.00</span>
						</div>
					</div>
				</div> <!-- End Col -->

				<div class="col-lg-3 col-md-4 col-sm-6">
					<div class="single_product">
						<div class="product_image">
							<img src="../assets/img/product/8.jpg" alt="" />
							<div class="box-content">
								<a href="#"><i class="fa fa-heart-o"></i></a>
								<a href="#"><i class="fa fa-cart-plus"></i></a>
								<a href="#"><i class="fa fa-search"></i></a>
							</div>
						</div>

						<div class="product_btm_text">
							<h4><a href="#">Product Title</a></h4>
							<div class="p_rating">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
							</div>
							<span class="price">$123.00</span>
						</div>
					</div>
				</div> <!-- End Col -->
			</div>
		</div>
	</section>
	<!-- End Featured Products Area -->

	<!-- Testimonials Area -->
	<section id="testimonials" class="testimonials_area section_padding"
		style="background: url(../assets/img/testimonial-bg.jpg); background-size: cover; background-attachment: fixed;">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<div id="testimonial-slider" class="owl-carousel">
						<div class="testimonial">
							<div class="pic">
								<img src="../assets/img/testimonial/1.jpg" alt="">
							</div>
							<div class="testimonial-content">
								<p class="description">
									Lorem ipsum dolor sit amet, consectetur adipiscing elit.
									Vivamus sed accumsan diam. Suspendisse molestie nibh at
									tempor mollis. Integer aliquet facilisis
								</p>
								<h3 class="testimonial-title">williamson</h3>
								<small class="post"> - Themesvila</small>
							</div>
						</div>

						<div class="testimonial">
							<div class="pic">
								<img src="../assets/img/testimonial/2.jpg" alt="">
							</div>
							<div class="testimonial-content">
								<p class="description">
									Lorem ipsum dolor sit amet, consectetur adipiscing elit.
									Vivamus sed accumsan diam. Suspendisse molestie nibh at
									tempor mollis. Integer aliquet facilisis
								</p>
								<h3 class="testimonial-title">kristiana</h3>
								<small class="post"> - Themesvila</small>
							</div>
						</div>


						<div class="testimonial">
							<div class="pic">
								<img src="../assets/img/testimonial/3.jpg" alt="">
							</div>
							<div class="testimonial-content">
								<p class="description">
									Lorem ipsum dolor sit amet, consectetur adipiscing elit.
									Vivamus sed accumsan diam. Suspendisse molestie nibh at
									tempor mollis. Integer aliquet facilisis
								</p>
								<h3 class="testimonial-title">williamson</h3>
								<small class="post"> - Themesvila</small>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section> <!-- End STestimonials Area -->

	<!--  Blog -->
	<section id="blog_area" class="section_padding">
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<div class="section_title">
						<h2>latest <span>Blog</span></h2>
						<div class="divider"></div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-4 col-md-6 col-sm-6">
					<div class="single_blog">
						<div class="single_blog_img">
							<img src="../assets/img/blog/1.jpg" alt="">
							<div class="blog_date text-center">
								<div class="bd_day"> 25</div>
								<div class="bd_month">Aug</div>
							</div>
						</div>

						<div class="blog_content">
							<h4 class="post_title"><a href="#">Integer euismod dui non auctor</a> </h4>
							<ul class="post-bar">
								<li><i class="fa fa-user"></i> Admin</li>
								<li><i class="fa fa-comments-o"></i> <a href="#">2 comments</a></li>
								<li><i class="fa fa-heart-o"></i> <a href="#">4 like</a></li>
							</ul>
							<p>Proin in blandit lacus. Nam pellentesque tortor eget dui feugiat venenatis ....</p>
						</div>
					</div>
				</div> <!--  End Col -->

				<div class="col-lg-4 col-md-6 col-sm-6">
					<div class="single_blog">
						<div class="single_blog_img">
							<img src="../assets/img/blog/2.jpg" alt="">
							<div class="blog_date text-center">
								<div class="bd_day"> 25</div>
								<div class="bd_month">Aug</div>
							</div>
						</div>

						<div class="blog_content">
							<h4 class="post_title"><a href="#">Integer tempor urna a condimentum</a> </h4>
							<ul class="post-bar">
								<li><i class="fa fa-user"></i> Admin</li>
								<li><i class="fa fa-comments-o"></i> <a href="#">2 comments</a></li>
								<li><i class="fa fa-heart-o"></i> <a href="#">4 like</a></li>
							</ul>

							<p>Proin in blandit lacus. Nam pellentesque tortor eget dui feugiat venenatis ....</p>
						</div>
					</div>
				</div> <!--  End Col -->

				<div class="col-lg-4 col-md-6 col-sm-6">
					<div class="single_blog">
						<div class="single_blog_img">
							<img src="../assets/img/blog/3.jpg" alt="">
							<div class="blog_date text-center">
								<div class="bd_day"> 25</div>
								<div class="bd_month">Aug</div>
							</div>
						</div>

						<div class="blog_content">

							<h4 class="post_title"><a href="#">Vivamus velit est iaculis id tempus</a> </h4>
							<ul class="post-bar">
								<li><i class="fa fa-user"></i> Admin</li>
								<li><i class="fa fa-comments-o"></i> <a href="#">2 comments</a></li>
								<li><i class="fa fa-heart-o"></i> <a href="#">4 like</a></li>
							</ul>
							<p>Proin in blandit lacus. Nam pellentesque tortor eget dui feugiat venenatis ....</p>
						</div>
					</div>
				</div> <!--  End Col -->

			</div>
		</div>
	</section>
	<!--  Blog end -->


	<!--  Process -->
	<section class="process_area section_padding gradient_section">
		<div class="container">
			<div class="row text-center">
				<div class="col-lg-3 col-md-6 col-sm-6">
					<div class="single-process">
						<!-- process Icon -->
						<div class="picon"><i class="fa fa-truck"></i></div>
						<!-- process Content -->
						<div class="process_content">
							<h3>free shipping</h3>
							<p>Lorem ipsum dummy</p>
						</div>
					</div>
				</div> <!-- End Col -->

				<div class="col-lg-3 col-md-6 col-sm-6">
					<div class="single-process">
						<!-- process Icon -->
						<div class="picon"><i class="fa fa-money"></i></div>
						<!-- process Content -->
						<div class="process_content">
							<h3>Cash On Delivery</h3>
							<p>Lorem ipsum dummy</p>
						</div>
					</div>
				</div> <!-- End Col -->

				<div class="col-lg-3 col-md-6 col-sm-6">
					<div class="single-process">
						<!-- process Icon -->
						<div class="picon"><i class="fa fa-headphones "></i></div>
						<!-- process Content -->
						<div class="process_content">
							<h3>Support 24/7</h3>
							<p>Lorem ipsum dummy</p>
						</div>
					</div>
				</div> <!-- End Col -->

				<div class="col-lg-3 col-md-6 col-sm-6">
					<div class="single-process">
						<!-- process Icon -->
						<div class="picon"><i class="fa fa-clock-o"></i></div>
						<!-- process Content -->
						<div class="process_content">
							<h3>Opening All Week</h3>
							<p>Lorem ipsum dummy</p>
						</div>
					</div>
				</div> <!-- End Col -->

			</div>
		</div>
	</section>
	<!--  End Process -->

	<!--  Brand -->
	<section id="brand_area" class="text-center">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
					<div class="brand_slide owl-carousel">
						<div class="item text-center"> <a href="#"><img src="../assets/img/brand/1.png" alt=""
									class="img-responsive" /></a> </div>
						<div class="item text-center"> <a href="#"><img src="../assets/img/brand/2.png" alt=""
									class="img-responsive" /></a> </div>
						<div class="item text-center"> <a href="#"><img src="../assets/img/brand/3.png" alt=""
									class="img-responsive" /></a> </div>
						<div class="item text-center"> <a href="#"><img src="../assets/img/brand/4.png" alt=""
									class="img-responsive" /></a> </div>
						<div class="item text-center"> <a href="#"><img src="../assets/img/brand/5.png" alt=""
									class="img-responsive" /></a> </div>
						<div class="item text-center"> <a href="#"><img src="../assets/img/brand/6.png" alt=""
									class="img-responsive" /></a> </div>
						<div class="item text-center"> <a href="#"><img src="../assets/img/brand/7.png" alt=""
									class="img-responsive" /></a> </div>
						<div class="item text-center"> <a href="#"><img src="../assets/img/brand/8.png" alt=""
									class="img-responsive" /></a> </div>
						<div class="item text-center"> <a href="#"><img src="../assets/img/brand/9.png" alt=""
									class="img-responsive" /></a> </div>
					</div>
				</div>
			</div>
		</div>
	</section>
	<!--   Brand end  -->

	<!--  FOOTER START  -->
	<footer class="footer_area">
		<div class="container">
			<div class="row">
				<div class="col-md-3 col-sm-6">
					<div class="single_ftr">
						<h4 class="sf_title">Contacts</h4>
						<ul>
							<li>4060 Reppert Coal Road Jackson, MS 39201 USA</li>
							<li>(+123) 685 78 <br> (+064) 987 245</li>
							<li>Contact@yourcompany.com</li>
						</ul>
					</div>
				</div> <!--  End Col -->

				<div class="col-md-3 col-sm-6">
					<div class="single_ftr">
						<h4 class="sf_title">Information</h4>
						<ul>
							<li><a href="#">About Us</a></li>
							<li><a href="#">Delivery Information</a></li>
							<li><a href="#">Privacy Policy</a></li>
							<li><a href="#">Terms & Conditions</a></li>
							<li><a href="#">Contact Us</a></li>
						</ul>
					</div>
				</div> <!--  End Col -->

				<div class="col-md-3 col-sm-6">
					<div class="single_ftr">
						<h4 class="sf_title">Services</h4>
						<ul>
							<li><a href="#">Returns</a></li>
							<li><a href="#">Site Map</a></li>
							<li><a href="#">Wish List</a></li>
							<li><a href="#">My Account</a></li>
							<li><a href="#">Order History</a></li>
						</ul>
					</div>
				</div> <!--  End Col -->

				<div class="col-md-3 col-sm-6">
					<div class="single_ftr">
						<h4 class="sf_title">Newsletter</h4>
						<div class="newsletter_form">
							<p>There are many variations of passages of Lorem Ipsum available, but the majority have
							</p>
							<form method="post" class="form-inline">
								<input name="EMAIL" id="email" placeholder="Enter Your Email" class="form-control"
									type="email">
								<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
							</form>
						</div>
					</div>
				</div> <!--  End Col -->

			</div>
		</div>


		<div class="ftr_btm_area">
			<div class="container">
				<div class="row">
					<div class="col-sm-4">
						<div class="ftr_social_icon">
							<ul>
								<li><a href="#"><i class="fa fa-facebook"></i></a></li>
								<li><a href="#"><i class="fa fa-google"></i></a></li>
								<li><a href="#"><i class="fa fa-linkedin"></i></a></li>
								<li><a href="#"><i class="fa fa-twitter"></i></a></li>
								<li><a href="#"><i class="fa fa-rss"></i></a></li>
							</ul>
						</div>
					</div>
					<div class="col-sm-4">
						<p class="copyright_text text-center">&copy; 2024 All Rights Reserved FancyShop</p>
					</div>

					<div class="col-sm-4">
						<div class="payment_mthd_icon text-right">
							<ul>
								<li><i class="fa fa-cc-paypal"></i></li>
								<li><i class="fa fa-cc-visa"></i></li>
								<li><i class="fa fa-cc-discover"></i></li>
								<li><i class="fa fa-cc-mastercard"></i></li>
								<li><i class="fa fa-cc-amex"></i></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</footer>
	<!--  FOOTER END  -->

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