<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}
/**
 * @author    Dan Fisher
 * @package   Alchemists
 * @since     1.0.0
 * @version   4.2.10
 *
 * Shortcode attributes
 * @var $atts
 * @var $player_lists_id
 * @var $player_lists_id_on
 * @var $player_lists_id_num
 * @var $autoplay
 * @var $autoplay_speed
 * @var $el_class
 * @var $el_id
 * @var $css
 * @var $css_animation
 * Shortcode class
 * @var $this WPBakeryShortCode_Alc_Roster_Slider
 */

// Theme Options
$alchemists_data = get_option( 'alchemists_data' );
$color_primary = isset( $alchemists_data['color-primary'] ) ? $alchemists_data['color-primary'] : '#ffdc11';

$player_lists_id = $player_lists_id_on = $player_lists_id_num = $autoplay = $autoplay_speed = $el_class = $el_id = $css = $css_animation = '';

$output = '';
$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$class_to_filter = 'team-roster--slider-wrapper';
$class_to_filter .= vc_shortcode_custom_css_class( $css, ' ' ) . $this->getExtraClass( $el_class ) . $this->getCSSAnimation( $css_animation );
$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts );

$wrapper_attributes = array();
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . esc_attr( $el_id ) . '"';
}

if ( $player_lists_id_on == 1 && ! empty( $player_lists_id_num ) ) {
	$player_lists_id = $player_lists_id_num;
}
?>

<!-- Roster Slider -->
<div <?php echo implode( ' ', $wrapper_attributes ); ?> class="<?php echo esc_attr( apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $css_class, $this->settings['base'], $atts ) ); ?>">

	<?php
		sp_get_template( 'player-slider.php', array(
			'id' => $player_lists_id,
			'autoplay' => $autoplay,
			'autoplay_speed' => $autoplay_speed,
		) );
	?>

</div>
<!-- Roster Slider / End -->
