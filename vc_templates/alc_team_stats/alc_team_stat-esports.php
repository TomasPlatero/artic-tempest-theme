<?php
/**
 * The template for displaying ALC: Team Stats cell (eSports)
 *
 * @author    Dan Fisher
 * @package   Alchemists
 * @since     4.0.0
 * @version   4.0.0
 */

$stat_icon_type   = 'default';
$svg_icon_color   = ! empty( $team_color_primary ) ? 'style="border-color: ' . $team_color_primary . ';"' : '';

switch ( $stat_icon ) {
	case 'icon_2':
		$stat_icon_output = '<div class="team-stats__icon team-stats__icon--circle-outline team-stats__icon--size-sm team-stats__icon--centered" ' . $svg_icon_color . '>';
			$stat_icon_output .= '<svg role="img" class="df-icon df-icon--skull"><use xlink:href="' . get_template_directory_uri() . '/assets/images/esports/icons-esports.svg#skull"/></svg>';
		$stat_icon_output .= '</div>';
		break;
	case 'icon_3':
		$stat_icon_output = '<div class="team-stats__icon team-stats__icon--circle-outline team-stats__icon--size-sm team-stats__icon--centered" ' . $svg_icon_color . '>';
			$stat_icon_output .= '<svg role="img" class="df-icon df-icon--crosshair"><use xlink:href="' . get_template_directory_uri() . '/assets/images/esports/icons-esports.svg#crosshair"/></svg>';
		$stat_icon_output .= '</div>';
		break;
	case 'icon_custom':
		// icon symbol
		$stat_icon_symbol = $stat_item['stat_icon_symbol'];
		$stat_icon_output = '<div class="team-stats__icon team-stats__icon--circle-outline team-stats__icon--size-sm team-stats__icon--centered" ' . $svg_icon_color . '>';
			$stat_icon_output .= '<svg role="img" class="df-icon df-icon--coin"><use xlink:href="' . get_template_directory_uri() . '/assets/images/esports/icons-esports.svg#coin"/></svg>';
			$stat_icon_output .= '<span class="team-stats__indicator">' . esc_html( $stat_icon_symbol ) . '</span>';
		$stat_icon_output .= '</div>';
		break;
	case 'icon_custom_font':
		$stat_icon_type   = 'icon-font';
		$icon_style          = array();
		$icon_styles_output  = array();

		if ( $icon_color ) {
			$icon_style[] = 'color: ' . $icon_color . ';';
		} elseif ( $team_color_primary ) {
			$icon_style[] = 'color: ' . $team_color_primary . ';';
		}
		if ( $icon_style ) {
			$icon_styles_output[] = 'style="' . implode( ' ', $icon_style ). '"';
		}
		$stat_icon_output = '<i class="' . $iconClass . '" ' . implode( ' ', $icon_styles_output ) . '></i>';
		break;
	case 'icon_custom_img':
		$stat_icon_type   = 'img';
		$icon_img_title   = get_the_title( $icon_img_id );
		$stat_icon_output = '<img src="' . $iconImgUrl . '" class="team-stats__icon-img" alt="' . esc_attr( $icon_img_title ) . '">';
		break;
	default:
		$stat_icon_output = '<div class="team-stats__icon team-stats__icon--circle-outline team-stats__icon--size-sm team-stats__icon--centered" ' . $svg_icon_color . '>';
			$stat_icon_output .= '<svg role="img" class="df-icon df-icon--trophy"><use xlink:href="' . get_template_directory_uri() . '/assets/images/esports/icons-esports.svg#trophy"/></svg>';
		$stat_icon_output .= '</div>';
}
?>

<li class="team-stats__item team-stats__item--clean">
	<div class="team-stats__icon team-stats__icon--<?php echo esc_attr( $stat_icon_type ); ?>">
		<?php echo $stat_icon_output; ?>
	</div>
	<div class="team-stats__value"><?php echo esc_html( $stat_value ); ?></div>
	<div class="team-stats__label"><?php echo esc_html( $stat_label ); ?></div>
</li>
