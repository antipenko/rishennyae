<?php
/**
 * @version    1.0
 * @package    WR_Theme
 * @author     WooRockets Team <support@woorockets.com>
 * @copyright  Copyright (C) 2014 WooRockets.com. All Rights Reserved.
 * @license    GNU/GPL v2 or later http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Websites: http://www.woorockets.com
 */

// Define supported content width.
$content_width = 740;

// Initialize WR Nitro.
get_template_part( 'woorockets/core' );

add_shortcode( 'product_search', 'get_product_search_form' );

function add_custom_tags_box() {
	add_meta_box(   'categorydiv', __('Categories'), 'post_categories_meta_box', 
		'page', 'side', 'low'); 
	register_taxonomy_for_object_type('category', 'page');
} 
