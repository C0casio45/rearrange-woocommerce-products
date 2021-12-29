<?php
$args = array(
  'post_type'         => array('product'),
  'posts_per_page'    => '-1',
  'post_status'       => array('publish'),
);

if (isset($_GET['term_id']) && !empty($_GET['term_id'])) {
  $term_id = sanitize_text_field($_GET['term_id']);

  $meta_key = 'rwpp_sortorder_' . $term_id;

  // store products sortorder inside post_meta (use menu_order by default)
  $this->update_products_meta($term_id);

  $args['tax_query'] = array(
    array(
      'taxonomy' => 'product_cat',
      'terms' => [$term_id],
      'field' => 'id',
      'operator' => 'IN'
    ),
  );

  if($this->meta_field_exists($meta_key)){
    $args['meta_key'] = $meta_key;
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
    while ($products->have_posts()) : $products->the_post();
      global $post;
      $product = wc_get_product($post->ID);
      include("_product.php");
      $serial_no++;
    endwhile;
    ?>
  </div>

  <button id="rwpp-save-orders" class="button-primary"><?php _e('Save Changes', 'rwpp'); ?></button>

  <div id="rwpp-response"></div>
<?php
endif;

wp_reset_postdata();
