<?php

/**
 * List all products
 *
 * @package ReWooProducts
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

$args = array(
	'post_type' => array('product'),
	'posts_per_page' => '-1',
	'post_status' => array('publish'),
);

if (isset($_GET['term_id']) && !empty($_GET['term_id'])) { // phpcs:ignore WordPress.Security.NonceVerification
	$term_id = sanitize_text_field(wp_unslash($_GET['term_id'])); // phpcs:ignore WordPress.Security.NonceVerification

	$meta_key = 'rwpp_sortorder_' . $term_id;

	// store products sortorder inside post_meta (use menu_order by default).
	$this->update_products_meta($term_id);

	$args['tax_query'] = array( // phpcs:ignore
		array(
			'taxonomy' => 'product_cat',
			'terms' => array($term_id),
			'field' => 'id',
			'operator' => 'IN',
		),
	);

	if ($this->meta_field_exists($meta_key)) {
		$args['meta_key'] = $meta_key; // phpcs:ignore
		$args['orderby'] = 'meta_value_num menu_order title';
		$args['order'] = 'ASC';
	}
} else {
	$args['orderby'] = 'menu_order title';
	$args['order'] = 'ASC';
}

$products = new WP_Query($args);

if ($products->have_posts()) : ?>
<div id="rwpp-products-list">
    <?php
		$serial_no = 1;
		while ($products->have_posts()) :
			$products->the_post();
			global $post;
			$product = wc_get_product($post->ID); // output escaped via WooCommerce wc_get_product().
			include '-product.php';
			$serial_no++;
		endwhile;
		?>
</div>



<div id="rwpp-response"></div>
<?php
endif;

wp_reset_postdata();