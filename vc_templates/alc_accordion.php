<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * @author    Dan Fisher
 * @package   Alchemists
 * @since     4.0.0
 * @version   4.0.0
 *
 * Shortcode attributes
 * @var $atts
 * @var $title
 * @var $el_class
 * @var $el_id
 * @var $css
 * @var $css_animation
 * Shortcode class
 * @var $this WPBakeryShortCode_Alc_Newslog
 */

$title = $el_class = $el_id = $css = $css_animation = '';

$output = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$class_to_filter = 'accordion accordion--space-between';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$wrapper_attributes = array();
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}

// Visual Composer helper function
if ( function_exists( 'wpb_js_remove_wpautop' ) ) {
	$content = wpb_js_remove_wpautop( $content, false );
} else {
	$content = do_shortcode(shortcode_unautop( $content ) );
}

// Ensure HTML tags get closed
$content = force_balance_tags( $content );
?>

<!-- Accordion -->
<div <?php echo implode( ' ', $wrapper_attributes ); ?> class="<?php echo esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_class, $this->settings['base'], $atts ) ); ?>">
	<?php
	if ( function_exists( 'wpb_js_remove_wpautop' ) ) {
		echo wpb_js_remove_wpautop( $content, false );
	} else {
		echo do_shortcode( shortcode_unautop( $content ) );
	}
	?>
</div>
<!-- Accordion / End -->
