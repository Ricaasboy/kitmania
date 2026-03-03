<!-- Start product Area -->
<section id="product_area" class="section_padding">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="section_title">
                    <h2>Our <span>Products</span></h2>
                    <div class="divider"></div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <div class="product_filter">
                <ul>
                    <li class=" active filter" data-filter="all">ALL</li>
                    <li class="filter" data-filter=".sale">Sale</li>
                    <li class="filter" data-filter=".bslr">Bestseller</li>
                    <li class="filter" data-filter=".ftrd">Featured</li>
                </ul>
            </div>

            <div class="product_item">
                <div class="row">
                    <?php
                    // Garantir que $lang está definida
                    if (!isset($lang)) {
                        $lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'pt';
                    }

                    // Consulta todos os produtos
                    $sql = "SELECT p.id, p.name, p.price, p.category_id, p.subcategory_id, p.thumbnail 
                FROM products p 
                ORDER BY p.id DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            // Nome traduzido
                            $name_json = json_decode($row['name'], true);
                            $product_name = $name_json[$lang] ?? $name_json['pt'] ?? 'Sem nome';

                            // Categorias
                            $cat_name = get_category_name($row['category_id'], $lang, $conn);
                            $sub_name = $row['subcategory_id'] ? get_category_name($row['subcategory_id'], $lang, $conn) : '';
                            $categories_text = $cat_name . ($sub_name ? ' / ' . $sub_name : '');

                            // Preço
                            $price_formatted = number_format($row['price'], 2, '.', '') . ' €';

                            // Imagem (placeholder se '0')
                            $img_src = ($row['thumbnail'] && $row['thumbnail'] !== '0')
                                ? htmlspecialchars('../' . $row['thumbnail'])
                                : '/assets/img/default-product.jpg';

                            // Link correto para detalhes - AGORA APONTA PARA O MESMO DIRETÓRIO
                            $detail_link = "product_details.php?id=" . $row['id'];
                    ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 mix">
                                <div class="single_product">
                                    <div class="product_image">
                                        <a href="<?= $detail_link ?>">
                                            <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($product_name) ?>" />
                                        </a>
                                        <div class="new_badge">New</div>
                                        <div class="box-content">
                                            <a href="#"><i class="fa fa-heart-o"></i></a>
                                            <a href="#"><i class="fa fa-cart-plus"></i></a>
                                            <a href="#"><i class="fa fa-search"></i></a>
                                        </div>
                                    </div>

                                    <div class="product_btm_text">
                                        <h4><a href="<?= $detail_link ?>"><?= htmlspecialchars($product_name) ?></a></h4>
                                        <div class="p_rating">
                                            <?= htmlspecialchars($categories_text) ?>
                                        </div>
                                        <span class="price"><?= $price_formatted ?></span>
                                    </div>
                                </div>
                            </div> <!-- End Col -->
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12 text-center py-5">
                            <p class="text-muted"><?= $lang === 'en' ? 'No products found' : 'Nenhum produto encontrado' ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- End product Area -->