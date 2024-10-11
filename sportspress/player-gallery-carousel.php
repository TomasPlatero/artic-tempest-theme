<?php
/**
 * Player Gallery - Carousel
 *
 * @author    Dan Fisher
 * @package   Alchemists
 * @since     3.4.0
 * @version   4.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$html5 = current_theme_supports( 'html5', 'gallery' );
$defaults = array(
	'id' => get_the_ID(),
	'title' => false,
	'number' => -1,
	'grouping' => null,
	'orderby' => 'default',
	'order' => 'ASC',
	'itemtag' => 'div',
	'icontag' => 'figure',
	'captiontag' => 'div',
	'grouptag' => 'h4',
	'columns' => 3,
	'size' => 'sportspress-crop-medium',
	'show_all_players_link' => false,
	'link_posts' => get_option( 'sportspress_link_players', 'yes' ) == 'yes' ? true : false,
	'squad_number' => 1,
	'autoplay'       => $autoplay,
	'autoplay_speed' => $autoplay_speed,
	'arrows'         => $arrows
);

extract( $defaults, EXTR_SKIP );

// check if RTL
$is_rtl = is_rtl() ? 'true' : 'false';

// Determine number of players to display
if ( -1 === $number ):
	$number = (int) get_post_meta( $id, 'sp_number', true );
	if ( $number <= 0 ) $number = -1;
endif;

$itemtag = tag_escape( $itemtag );
$captiontag = tag_escape( $captiontag );
$icontag = tag_escape( $icontag );
$valid_tags = wp_kses_allowed_html( 'post' );
if ( ! isset( $valid_tags[ $itemtag ] ) )
	$itemtag = 'div';
if ( ! isset( $valid_tags[ $captiontag ] ) )
	$captiontag = 'div';
if ( ! isset( $valid_tags[ $icontag ] ) )
	$icontag = 'div';

$columns = intval( $columns );
$itemwidth = $columns > 0 ? floor(100/$columns) : 100;
$size = $size;

$selector = 'sp-player-gallery-' . $id;

$uniqid = rand();

$list = new SP_Player_List( $id );
$data = $list->data();

$team_id              = get_post_meta( $id, 'sp_team', true );
$team_color_primary   = get_field( 'team_color_primary', $team_id );

$output = '';

if ( $team_color_primary ) {
	$output .= '<style>';
		$output .= '#' . $selector . ' .team-roster--grid .team-roster__member-last-name {';
			$output .= 'color: ' . $team_color_primary . ';';
		$output .= '}';

		$output .= '#' . $selector . ' .team-roster--grid .team-roster__holder:hover .team-roster__member-number,';
		$output .= '#' . $selector . ' .btn-fab {';
			$output .= 'background-color: ' . $team_color_primary . ';';
		$output .= '}';
	$output .= '</style>';
}

echo $output;

// Remove the first row to leave us with the actual data
unset( $data[0] );

if ( $grouping === null || $grouping === 'default' ):
	$grouping = $list->grouping;
endif;

if ( $orderby == 'default' ):
	$orderby = $list->orderby;
	$order = $list->order;
elseif ( $orderby == 'rand' ):
	uasort( $data, 'sp_sort_random' );
else:
	$list->priorities = array(
		array(
			'key' => $orderby,
			'order' => $order,
		),
	);
	uasort( $data, array( $list, 'sort' ) );
endif;

if ( $title ) {
	echo '<h4 class="sp-table-caption">' . $title . '</h4>';
}

$gallery_style = $gallery_div = '';
$size_class = sanitize_html_class( $size );
$gallery_div = "<div id='$selector' class='gallery galleryid-{$id} gallery-columns-{$columns} gallery-size-{$size_class}'>";
echo apply_filters( 'gallery_style', $gallery_style . "\n\t\t" );
?>
<?php echo wp_kses_post( $gallery_div ); ?>
	<?php
	if ( intval( $number ) > 0 )
		$limit = $number;

	if ( $grouping === 'position' ):
		$groups = get_terms( 'sp_position', array(
			'orderby' => 'meta_value_num',
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => 'sp_order',
					'compare' => 'NOT EXISTS'
				),
				array(
					'key' => 'sp_order',
					'compare' => 'EXISTS'
				),
			),
		) );
	else:
		$group = new stdClass();
		$group->term_id = null;
		$group->name = null;
		$group->slug = null;
		$groups = array( $group );
	endif;

	$j = 0;

	foreach ( $groups as $group ):
		$i = 0;

		$gallery = '';

		foreach( $data as $player_id => $performance ): if ( empty( $group->term_id ) || has_term( $group->term_id, 'sp_position', $player_id ) ):

			if ( isset( $limit ) && $i >= $limit ) continue;

			$caption = get_the_title( $player_id );
			$caption = trim( $caption );

			ob_start();

				sp_get_template( 'player-gallery-thumbnail.php', array(
					'id' => $player_id,
					'itemtag' => $itemtag,
					'icontag' => $icontag,
					'captiontag' => $captiontag,
					'caption' => $caption,
					'size' => $size,
					'link_posts' => $link_posts,
					'team_color_primary' => $team_color_primary,
					'squad_number' => $squad_number,
				) );

			$gallery .= ob_get_clean();

			$i++;

		endif; endforeach;

		$gallery .= '</div>';

		$j++;

		if ( $i === 0 ) continue;
			?>

			<div
				id="slick-<?php echo esc_attr( $uniqid ); ?>"
				class="team-roster team-roster--grid team-roster--carousel team-roster--grid-col-<?php echo esc_attr( $columns ); ?> sp-player-gallery-wrapper sp-gallery-wrapper"
				data-slick='{
					"slidesToShow": <?php echo esc_js( $columns ); ?>,
					"autoplay": <?php echo esc_js( $autoplay); ?>,
					"autoplaySpeed": <?php echo esc_js( $autoplay_speed ); ?>,
					"arrows": <?php echo esc_js( $arrows ); ?>,
					"dots": false,
					"infinite": false,
					"rtl": <?php echo esc_js( $is_rtl ); ?>,
					"rows": 0,
					"responsive": [
						{
							"breakpoint": 1200,
							"settings": {
								"slidesToShow": <?php echo esc_js( $columns ); ?>
							}
						},
						{
							"breakpoint": 992,
							"settings": {
								"arrows": false,
								"slidesToShow": 2
							}
						},
						{
							"breakpoint": 769,
							"settings": {
								"arrows": false,
								"slidesToShow": 1
							}
						},
						{
							"breakpoint": 480,
							"settings": {
								"arrows": false,
								"slidesToShow": 1
							}
						}
					]
				}'>

			<?php
		echo $gallery;
		echo '</div>';

	endforeach;

echo "</div>\n";
