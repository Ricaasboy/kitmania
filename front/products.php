<?php
// Inclui conexão + $lang
include("../back/conn.php");

// Função auxiliar para traduzir campos JSON
function traduzir_campo($json_string, $lang, $fallback_lang = 'pt')
{
    if (empty($json_string)) {
        return '—';
    }

    $data = json_decode($json_string, true);

    if (!is_array($data)) {
        return htmlspecialchars($json_string);
    }

    if (isset($data[$lang]) && trim($data[$lang]) !== '') {
        return htmlspecialchars($data[$lang]);
    }

    if (isset($data[$fallback_lang]) && trim($data[$fallback_lang]) !== '') {
        return htmlspecialchars($data[$fallback_lang]);
    }

    foreach ($data as $value) {
        if (trim($value) !== '') {
            return htmlspecialchars($value);
        }
    }

    return '—';
}

// Cache de categorias
$categories_cache = [];
?>

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
                    <li class="active filter" data-filter="all">ALL</li>
                    <li class="filter" data-filter=".sale">Sale</li>
                    <li class="filter" data-filter=".bslr">Bestseller</li>
                    <li class="filter" data-filter=".ftrd">Featured</li>
                </ul>
            </div>

            <div class="product_item">
                <div class="row">
                    <?php
                    // Consulta todos os produtos
                    $sql = "SELECT p.id, p.name, p.description, p.price, p.category_id, p.subcategory_id, p.thumbnail 
                            FROM products p 
                            ORDER BY p.id DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                            // Nome completo traduzido
                            $product_name_full = traduzir_campo($row['name'], $lang);

                            // Nome limitado a 30 caracteres + "..." se necessário
                            $product_name = mb_strlen($product_name_full) > 30
                                ? mb_substr($product_name_full, 0, 30) . '...'
                                : $product_name_full;

                            $product_description = traduzir_campo($row['description'], $lang);

                            // Categoria e subcategoria
                            $cat_name  = get_category_name($row['category_id'], $lang, $conn);
                            $sub_name  = $row['subcategory_id'] ? get_category_name($row['subcategory_id'], $lang, $conn) : '';
                            $categories_text = $cat_name . ($sub_name ? ' / ' . $sub_name : '');

                            // Preço formatado
                            $price_formatted = number_format($row['price'], 2, '.', '') . ' €';

                            // Imagem
                            $img_src = ($row['thumbnail'] && $row['thumbnail'] !== '0')
                                ? htmlspecialchars('../' . $row['thumbnail'])
                                : '/assets/img/default-product.jpg';

                            $detail_link = "product_details.php?id=" . $row['id'];
                    ?>
                            <div class="col-lg-3 col-md-4 col-sm-6 mix">
                                <div class="single_product">
                                    <div class="product_image">
                                        <a href="<?= $detail_link ?>">
                                            <img src="<?= $img_src ?>" alt="<?= htmlspecialchars($product_name_full) ?>" />
                                        </a>
                                        <div class="new_badge">New</div>
                                        <div class="box-content">
                                            <a href="#"><i class="fa fa-heart-o"></i></a>
                                            <a href="#"><i class="fa fa-cart-plus"></i></a>
                                            <a href="#"><i class="fa fa-search"></i></a>
                                        </div>
                                    </div>

                                    <div class="product_btm_text">
                                        <h4 style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;"
                                            title="<?= htmlspecialchars($product_name_full) ?>">
                                            <a href="<?= $detail_link ?>"><?= htmlspecialchars($product_name) ?></a>
                                        </h4>
                                        <div class="p_rating">
                                            <?= htmlspecialchars($categories_text) ?>
                                        </div>
                                        <span class="price"><?= $price_formatted ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php
                        endwhile;
                    else:
                        ?>
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