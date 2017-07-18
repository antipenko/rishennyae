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

// Get theme options
$wr_nitro_options = WR_Nitro::get_options();

// Get footer layout
$wr_layout = $wr_nitro_options['footer_layout'];
$wr_text = $wr_nitro_options['footer_bot_text'];

// Get sidebar
$wr_sidebar_1 = $wr_nitro_options['footer_sidebar_1'];
$wr_sidebar_2 = $wr_nitro_options['footer_sidebar_2'];
$wr_sidebar_3 = $wr_nitro_options['footer_sidebar_3'];
$wr_sidebar_4 = $wr_nitro_options['footer_sidebar_4'];
$wr_sidebar_5 = $wr_nitro_options['footer_sidebar_5'];
?>
<section class="container">
	<div id="fb-root"></div>
	<script>(function(d, s, id) {
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/ru_RU/sdk.js#xfbml=1&version=v2.9";
		fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>

	<div class="fb-comments" data-href="http://rishennyae.com" data-numposts="5"></div>

</section>
<footer id="footer" class="<?php
if ( is_customize_preview() )
	echo 'customizable customize-section-footer ';
?>footer">
<?php if ( is_active_sidebar( $wr_sidebar_1 ) || is_active_sidebar( $wr_sidebar_2 ) || is_active_sidebar( $wr_sidebar_3 ) || is_active_sidebar( $wr_sidebar_4 ) || is_active_sidebar( $wr_sidebar_5 ) ) : ?>
	<div class="top">
		<div class="top-inner">
			<div class="row">
				<?php
				switch( $wr_layout ) :
				case 'layout-1' :
				echo '<div class="cm-12">';
				dynamic_sidebar( $wr_sidebar_1 );
				echo '</div>';
				break;

				case 'layout-2' :
				echo '<div class="cm-9 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_1 );
				echo '</div>';
				echo '<div class="cm-3 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_2 );
				echo '</div>';
				break;

				case 'layout-3' :
				echo '<div class="cm-6 w800-4 cxs-12">';
				dynamic_sidebar( $wr_sidebar_1 );
				echo '</div>';
				echo '<div class="cm-3 w800-4 cxs-12">';
				dynamic_sidebar( $wr_sidebar_2 );
				echo '</div>';
				echo '<div class="cm-3 w800-4 cxs-12">';
				dynamic_sidebar( $wr_sidebar_3 );
				echo '</div>';
				break;

				case 'layout-4' :
				echo '<div class="cm-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_1 );
				echo '</div>';
				echo '<div class="cm-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_2 );
				echo '</div>';
				break;

				case 'layout-5' :
				echo '<div class="cm-3 w800-4 cxs-12">';
				dynamic_sidebar( $wr_sidebar_1 );
				echo '</div>';
				echo '<div class="cm-3 w800-4 cxs-12">';
				dynamic_sidebar( $wr_sidebar_2 );
				echo '</div>';
				echo '<div class="cm-6 w800-4 cxs-12">';
				dynamic_sidebar( $wr_sidebar_3 );
				echo '</div>';
				break;

				case 'layout-6' :
				echo '<div class="cm-3 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_1 );
				echo '</div>';
				echo '<div class="cm-9 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_2 );
				echo '</div>';
				break;

				case 'layout-8' :
				echo '<div class="cm-4 cxs-12">';
				dynamic_sidebar( $wr_sidebar_1 );
				echo '</div>';
				echo '<div class="cm-4 cxs-12">';
				dynamic_sidebar( $wr_sidebar_2 );
				echo '</div>';
				echo '<div class="cm-4 cxs-12">';
				dynamic_sidebar( $wr_sidebar_3 );
				echo '</div>';
				break;

				case 'layout-9' :
				echo '<div class="cm-4 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_1 );
				echo '</div>';
				echo '<div class="cm-8 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_2 );
				echo '</div>';
				break;

				case 'layout-10' :
				echo '<div class="cm-3 w800-4 cxs-12">';
				dynamic_sidebar( $wr_sidebar_1 );
				echo '</div>';
				echo '<div class="cm-6 w800-4 cxs-12">';
				dynamic_sidebar( $wr_sidebar_2 );
				echo '</div>';
				echo '<div class="cm-3 w800-4 cxs-12">';
				dynamic_sidebar( $wr_sidebar_3 );
				echo '</div>';
				break;
				case 'layout-11' :
				echo '<div class="cm-4 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_1 );
				echo '</div>';
				echo '<div class="cm-2 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_2 );
				echo '</div>';
				echo '<div class="cm-2 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_3 );
				echo '</div>';
				echo '<div class="cm-2 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_4 );
				echo '</div>';
				echo '<div class="cm-2 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_5 );
				echo '</div>';
				break;
				case 'layout-12' :
				echo '<div class="cm-2 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_1 );
				echo '</div>';
				echo '<div class="cm-2 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_2 );
				echo '</div>';
				echo '<div class="cm-2 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_3 );
				echo '</div>';
				echo '<div class="cm-2 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_4 );
				echo '</div>';
				echo '<div class="cm-4 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_5 );
				echo '</div>';
				break;
				default :
				echo '<div class="cm-3 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_1 );
				echo '</div>';
				echo '<div class="cm-3 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_2 );
				echo '</div>';
				echo '<div class="cm-3 w800-6 w800-clear cxs-12">';
				dynamic_sidebar( $wr_sidebar_3 );
				echo '</div>';
				echo '<div class="cm-3 w800-6 cxs-12">';
				dynamic_sidebar( $wr_sidebar_4 );
				echo '</div>';
				break;
				endswitch;
				?>
			</div><!-- .row -->
		</div><!-- .top-inner -->
	</div><!-- .top -->
<?php endif; ?>
</footer>

<footer id="colophon" class="copyright" style="text-align: center;"role="contentinfo">
	                <div class="site-info container">
	                        <?php  echo $wr_text;  ?>
                </div><!-- .site-info -->
        </footer><!-- #colophon -->

<script type="text/javascript" src="//cdn.callbackhunter.com/cbh.js?hunter_code=25ccd8b6e29e7df5a8aaaf978ac5816a" charset="UTF-8"></script>
<!-- .footer -->
</div></div><!-- .wrapper -->
<?php wp_footer(); ?>

</body>
</html>
