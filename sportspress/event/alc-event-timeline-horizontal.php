<?php
/**
 * @author    Dan Fisher
 * @package   Alchemists
 * @since     3.0.7
 * @version   3.2.5
 */

$float = is_rtl() ? 'right' : 'left';
?>

<!-- Timeline -->
<div class="game-timeline-wrapper game-timeline-wrapper--horizontal">

	<div class="game-timeline-teams">
		<?php foreach ( $timeline as $minutes => $details ) : ?>
			<?php
			$time = sp_array_value( $details, 'time', false );

			if ( false === $time ) continue;

			$icon = sp_array_value( $details, 'icon', '' );
			$side = sp_array_value( $details, 'side', 'home' );

			if ( $time < 0 ) :
				$name = sp_array_value( $details, 'name', esc_html__( 'Team', 'sportspress' ) );
				?>
				<div class="game-timeline__event game-timeline__event--kickoff game-timeline__event--side-<?php echo esc_attr( $side ); ?>" title="<?php esc_attr_e( 'Kick Off', 'sportspress' ); ?>">
					<?php if ( $icon ) : ?>
						<?php if ( $link_teams ) : ?>
							<?php $team = sp_array_value( $details, 'id' ); ?>
							<a href="<?php echo get_post_permalink( $team ); ?>" class="game-timeline__team-logo" title="<?php echo $name; ?>"><?php echo $icon; ?></a>
						<?php else : ?>
							<span class="game-timeline__team-logo" title="<?php echo $name; ?>"><?php echo $icon; ?></span>
						<?php endif; ?>
					<?php endif; ?>
					<div class="game-timeline__time game-timeline__time--kickoff game-timeline__time--kickoff-<?php echo esc_attr( $side ); ?>"><?php esc_html_e( 'KO', 'sportspress' ); ?></div>
				</div>
			<?php endif; ?>

		<?php endforeach; ?>
	</div>

	<div class="game-timeline">

		<?php foreach ( $timeline as $minutes => $details ) : ?>
			<?php
			$time = sp_array_value( $details, 'time', false );

			if ( false === $time ) continue;

			$icon = sp_array_value( $details, 'icon', '' );
			$side = sp_array_value( $details, 'side', 'home' );

			if ( $time >= 0 ) :
				$name = sp_array_value( $details, 'name', esc_html__( 'Player', 'sportspress' ) );
				$number = sp_array_value( $details, 'number', '' );

				if ( '' !== $number ) $name = $number . '. ' . $name;

				$offset = floor( $time / ( $event_minutes + 4 ) * 100 );
				if ( $offset - $previous <= 4 ) $offset = $previous + 4;
				$previous = $offset;
				?>
				<div class="game-timeline__event game-timeline__event--side-<?php echo esc_attr( $side ); ?>" style="<?php echo $float; ?>: <?php echo $offset; ?>%;">
					<div class="game-timeline__event-info game-timeline__event-info--side-<?php echo esc_attr( $side ); ?>">
						<div class="game-timeline__event-name"><?php echo $name; ?></div>
						<div class="game-timeline__icon game-timeline__icon--<?php echo esc_attr( $side ); ?>">
							<?php echo $icon; ?>
						</div>
					</div>
					<div class="game-timeline__time" data-toggle="tooltip" data-placement="top" title="<?php echo esc_attr( strip_tags( $name ) ); ?>"><?php echo $time . "'"; ?></div>
				</div>
			<?php endif; ?>

		<?php endforeach; ?>

	</div>

	<div class="game-timeline-ft">
		<div class="game-timeline__event game-timeline__event--ft" title="<?php esc_attr_e( 'Full Time', 'sportspress' ); ?>">
			<div class="game-timeline__time"><?php esc_html_e( 'FT', 'sportspress' ); ?></div>
		</div>
	</div>

</div>
<!-- Timeline / End -->
