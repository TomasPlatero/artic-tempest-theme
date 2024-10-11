<?php
/**
 * The template for displaying ALC: Team Stats cell (American Footbal)
 *
 * @author    Dan Fisher
 * @package   Alchemists
 * @since     1.0.0
 * @version   3.3.0
 */

// American Football
$stat_icon_type   = 'default';
$svg_icon_color   = ! empty( $team_color_secondary ) ? 'style="stroke: ' . $team_color_secondary . ';"' : '';

switch ( $stat_icon ) {
	case 'icon_2':
		$stat_icon_output = '<svg role="img" class="df-icon df-icon--football-helmet" ' . $svg_icon_color . '><use xlink:href="' . get_template_directory_uri() . '/assets/images/football/icons-football.svg#football-helmet"/></svg>';
		break;
	case 'icon_3':
		$stat_icon_output = '<svg role="img" class="df-icon df-icon--jersey" ' . $svg_icon_color . '><use xlink:href="' . get_template_directory_uri() . '/assets/images/icons-basket.svg#jersey"/></svg>';
		break;
	case 'icon_custom':
		// icon symbol
		$stat_icon_symbol = $stat_item['stat_icon_symbol'];
		$stat_icon_output = '<div class="df-icon-stack df-icon-stack--3pts"><svg role="img" class="df-icon df-icon--basketball" ' . $svg_icon_color . '><use xlink:href="' . get_template_directory_uri() . '/assets/images/football/icons-football.svg#football-ball"></use></svg><div class="df-icon--txt">' . esc_html( $stat_icon_symbol ) . '</div></div>';
		break;
	case 'icon_custom_font':
		$stat_icon_type   = 'icon-font';
		$icon_style          = array();
		$icon_styles_output  = array();

		if ( $icon_color ) {
			$icon_style[] = 'color: ' . $icon_color . ';';
		} elseif ( $team_color_secondary ) {
			$icon_style[] = 'color: ' . $team_color_secondary . ';';
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
		$stat_icon_output = '<svg role="img" class="df-icon df-icon--football-ball" ' . $svg_icon_color . '><use xlink:href="' . get_template_directory_uri() . '/assets/images/football/icons-football.svg#football-ball"/></svg>';
}
?>

<li class="team-stats__item team-stats__item--clean">
	<div class="team-stats__icon team-stats__icon--<?php echo esc_attr( $stat_icon_type ); ?>">
		<?php echo $stat_icon_output; ?>
	</div>
	<div class="team-stats__value"><?php echo esc_html( $stat_value ); ?></div>
	<div class="team-stats__label"><?php echo esc_html( $stat_label ); ?></div>
</li>
