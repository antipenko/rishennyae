<?php
/**
 * Child theme functions
 *
 * Functions file for child theme, enqueues parent and child stylesheets by default.
 *
 * @since	1.0.0
 * @package aa
 */
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if ( ! function_exists( 'aa_enqueue_styles' ) ) {
    // Add enqueue function to the desired action.
    add_action( 'wp_enqueue_scripts', 'aa_enqueue_styles' );
    /**
     * Enqueue Styles.
     *
     * Enqueue parent style and child styles where parent are the dependency
     * for child styles so that parent styles always get enqueued first.
     *
     * @since 1.0.0
     */
    function aa_enqueue_styles() {
        // Parent style variable.
        $parent_style = 'parent-style';
        // Enqueue Parent theme's stylesheet.
        wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
        // Enqueue Child theme's stylesheet.
        // Setting 'parent-style' as a dependency will ensure that the child theme stylesheet loads after it.
        wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( $parent_style ) );
    }
}
?>

<?php 
function wc_show_advocat_button(){  ?>
    <div class="advocat-button">
        <button class="button" id="advocat-free-conslutation">Получить бесплатную консультацию</button>
        <button class="button" id="advocat-comments">Оставить отзыв</button>
    </div>

    <div class="advocat-modal closed" id="modal-conslutation">
        <button class="close-button" id="close-button-consl">x</button>
            <?php 
            echo do_shortcode( '[contact-form-7 id="6527" title="consultation"]' );
            ?>
        </div>
    <div class="advocat-modal closed" id="modal-comments">
        <button class="close-button" id="close-button-comment">x</button>
        <?php 
        echo do_shortcode( '[contact-form-7 id="6527" title="consultation"]' );
        ?>
    </div>
    <div class="modal-overlay closed" id="modal-overlay">
    </div>
<?php } ?>

<?php 
    /**
   * Для термина  - product_cat
   */
  add_filter( 'request', 'change_requerst_vars_for_product_cat' );
  add_filter( 'term_link', 'term_link_filter', 10, 3 );

  /**
   * Для типа постов - product
   */
  add_filter( 'post_type_link', 'wpp_remove_slug', 10, 3 );
  add_action( 'pre_get_posts', 'wpp_change_request' );

  function change_requerst_vars_for_product_cat($vars) {

    global $wpdb;
    if ( ! empty( $vars[ 'pagename' ] ) || ! empty( $vars[ 'category_name' ] ) || ! empty( $vars[ 'name' ] ) || ! empty( $vars[ 'attachment' ] ) ) {
      $slug   = ! empty( $vars[ 'pagename' ] ) ? $vars[ 'pagename' ] : ( ! empty( $vars[ 'name' ] ) ? $vars[ 'name' ] : ( ! empty( $vars[ 'category_name' ] ) ? $vars[ 'category_name' ] : $vars[ 'attachment' ] ) );
      $exists = $wpdb->get_var( $wpdb->prepare( "SELECT t.term_id FROM $wpdb->terms t LEFT JOIN $wpdb->term_taxonomy tt ON tt.term_id = t.term_id WHERE tt.taxonomy = 'product_cat' AND t.slug = %s", array( $slug ) ) );
      if ( $exists ) {
        $old_vars = $vars;
        $vars     = array( 'product_cat' => $slug );
        if ( ! empty( $old_vars[ 'paged' ] ) || ! empty( $old_vars[ 'page' ] ) ) {
          $vars[ 'paged' ] = ! empty( $old_vars[ 'paged' ] ) ? $old_vars[ 'paged' ] : $old_vars[ 'page' ];
        }
        if ( ! empty( $old_vars[ 'orderby' ] ) ) {
          $vars[ 'orderby' ] = $old_vars[ 'orderby' ];
        }
        if ( ! empty( $old_vars[ 'order' ] ) ) {
          $vars[ 'order' ] = $old_vars[ 'order' ];
        }
      }
    }

    return $vars;

  }
  
  function term_link_filter( $url, $term, $taxonomy ) {

    $url = str_replace( "/product-category/", "/", $url );
    return $url;

  }

  function wpp_remove_slug( $post_link, $post, $name ) {

    if ( 'product' != $post->post_type || 'publish' != $post->post_status ) {
      return $post_link;
    }
    $post_link = str_replace( '/' . $post->post_type . '/', '/', $post_link );

    return $post_link;

  }

  function wpp_change_request( $query ) {

    if ( ! $query->is_main_query() || 2 != count( $query->query ) || ! isset( $query->query[ 'page' ] ) ) {
      return;
    }
    if ( ! empty( $query->query[ 'name' ] ) ) {
      $query->set( 'post_type', array( 'post', 'product', 'page' ) );
    }

  }
 ?>