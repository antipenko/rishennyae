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
//   function wpse_178112_permastruct_html( $post_type, $args ) {
//     if ( $post_type === 'product' )
//         add_permastruct( $post_type, "{$args->rewrite['slug']}/%$post_type%.html", $args->rewrite );
//         add_permastruct( $post_type, "/product-%$post_type%.html", $args->rewrite );
// }
 
// add_action( 'registered_post_type', 'wpse_178112_permastruct_html', 10, 2 );
 ?>
