<?php

require_once ABSPATH . 'wp-content/plugins/blizzard-api-wp/includes/raiderio/class-blizzard-api-raiderio.php';

if ( function_exists( 'vc_map' ) ) {
    vc_map( array(
        'name'        => esc_html__( 'ALC: Roster RaiderIO Blocks', 'alchemists' ),
        'base'        => 'alc_roster_raiderio_blocks',
        'icon'        => get_stylesheet_directory_uri() . '/admin/images/js_composer/alc_roster_raiderio_blocks.png',
        'category'    => esc_html__( 'ALC', 'alchemists' ),
        'description' => esc_html__( 'Dynamically displays roster data from RaiderIO.', 'alchemists' ),
        'params'      => array(
            array(
                'type'        => 'textfield',
                'heading'     => esc_html__( 'Number of Players', 'alchemists' ),
                'param_name'  => 'number',
                'value'       => '-1',
                'description' => esc_html__( 'Number of players to display. Set to -1 to display all.', 'alchemists' ),
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => esc_html__( 'Number of Columns', 'alchemists' ),
                'param_name'  => 'columns',
                'value'       => array(
                    esc_html__( '3 columns', 'alchemists' ) => '3',
                    esc_html__( '2 columns', 'alchemists' ) => '2',
                ),
                'description' => esc_html__( 'Select the number of columns for the player display.', 'alchemists' ),
            ),
            array(
                'type'        => 'checkbox',
                'heading'     => esc_html__( 'Show Player Number?', 'alchemists' ),
                'param_name'  => 'squad_number',
                'value'       => array(
                    esc_html__( 'Yes', 'alchemists' ) => '1'
                ),
                'std'         => '1',
                'description' => esc_html__( 'If checked, player numbers will be displayed.', 'alchemists' ),
            ),
            array(
                'type'        => 'css_editor',
                'heading'     => esc_html__( 'CSS box', 'alchemists' ),
                'param_name'  => 'css',
                'group'       => esc_html__( 'Design Options', 'alchemists' ),
            ),
        )
    ) );
}

// Registrar el shortcode para el bloque
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Alc_Roster_Raiderio_Blocks extends WPBakeryShortCode {
        protected function content( $atts, $content = null ) {
            // Atributos del shortcode
            $atts = shortcode_atts( array(
                'number'        => '-1',
                'columns'       => '3',
                'squad_number'  => '1',
            ), $atts );

            // Obtener datos de RaiderIO
            $roster_data = get_transient( 'blizzard_guild_roster_data' );

            if ( empty( $roster_data ) || !isset($roster_data['members']) ) {
                // Si no hay datos, mostrar el diseño de precarga
                return $this->render_loading_skeleton( $atts['columns'] );
            }

            // Definir los rangos
            $rank_names = array(
                0 => 'Guild Master',
                2 => 'Officer',
                4 => 'Raider Core',
                5 => 'Raider',
                6 => 'Trial',
            );

            // Filtrar y ordenar los miembros por rango
            $filtered_members = array_filter( $roster_data['members'], function( $member ) {
                return in_array( $member['rank'], array( 0, 2, 4, 5, 6 ) );
            });

            usort( $filtered_members, function( $a, $b ) {
                return $a['rank'] - $b['rank'];
            });

            ob_start();

            echo '<div class="sp-template sp-template-player-gallery sp-template-gallery">';

            // Iterar sobre cada rango y agrupar a los miembros correspondientes
            foreach ( $rank_names as $rank => $rank_name ) {
                $members_for_rank = array_filter( $filtered_members, function( $member ) use ( $rank ) {
                    return $member['rank'] === $rank;
                });

                if ( ! empty( $members_for_rank ) ) {
                    echo '<div class="card__header card__header--single"><h4 class="sp-gallery-group-name player-group-name player-gallery-group-name">' . esc_html( $rank_name ) . '</h4></div>';
                    echo '<div class="team-roster team-roster--grid-sm team-roster--grid-col-' . esc_attr( $atts['columns'] ) . '">';

                    foreach ( $members_for_rank as $member ) {
                        if ( $atts['number'] !== '-1' && --$atts['number'] < 0 ) {
                            break;
                        }

                        // Obtener la información del miembro, incluyendo la URL de la imagen
                        $character_info = get_transient( "raiderio_member_". $member['character']['name']);
                        if (empty($character_info)) {
                            // Si falta la información del miembro, mostrar una precarga
                            echo $this->render_loading_skeleton_item();
                            continue;
                        }

                        $class_name = isset($character_info['class']) ? sanitize_title($character_info['class']) : '';
                        $race_name = isset($character_info['race']) ? sanitize_title($character_info['race']) : '';
                        $gender = isset($character_info['gender']) && strtolower($character_info['gender']) === 'female' ? 'female' : 'male';
                        $region = get_option( "blizzard_api_region");

                        // Construir la URL del icono de la raza y clase
                        $base_path = get_stylesheet_directory_uri() . '/assets/images';
                        $race_icon_url = "{$base_path}/race_{$race_name}_{$gender}.jpg";
                        $class_icon_url = "{$base_path}/{$class_name}.jpg";

                        $raiderio_url = "https://raider.io/characters/".$region."/".$member['character']['realm']."/".$member['character']['name'];
                        $warcraftlogs_url = "https://www.warcraftlogs.com/character/".$region."/".$member['character']['realm']."/".$member['character']['name'];
                        $wow_url = "https://worldofwarcraft.blizzard.com/es-es/character/".$region."/".$member['character']['realm']."/".$member['character']['name'];

                        // Obtener la URL de la imagen del personaje o usar una imagen predeterminada
                        $avatar_url = isset($character_info['thumbnail_url']) ? esc_url($character_info['thumbnail_url']) : "{$base_path}/placeholder-140x210.jpg";

                        echo '<div class="team-roster__item">';
                            echo '<div class="team-roster__holder">';
                            echo '<figure class="team-roster__img"><img decoding="async" src="' . $avatar_url . '" alt="' . esc_attr( $character_info['name'] ) . '"></figure>';
                            echo '<div class="team-roster__content">';
                                echo '<h4 class="team-roster__name">' . esc_html( $character_info['name'] ) . '</h4>';
                                echo '<div class="player-icons">';
                                    echo '<div class="race-class">';
                                    // Mostrar el icono de la raza
                                    if ($race_name) {
                                        echo '<figure class="team-roster__race-icon"><img src="' . esc_url($race_icon_url) . '" alt="' . esc_attr($race_name) . '"></figure>';
                                    }
                                    // Mostrar el icono de la clase
                                    if ($class_name) {
                                        echo '<figure class="team-roster__class-icon"><img src="' . esc_url($class_icon_url) . '" alt="' . esc_attr($class_name) . '"></figure>';
                                    }
                                    echo '</div>';

                                    echo '<div class="social-icons">';
                                    echo '<a href="' . $raiderio_url . '" target="_blank"><img decoding="async" src="'.get_stylesheet_directory_uri().'/assets/images/raiderio.png" alt="' . esc_attr( $character_info['name'] ) . '"></a>';
                                    echo '<a href="' . $warcraftlogs_url . '" target="_blank"><img decoding="async" src="'.get_stylesheet_directory_uri().'/assets/images/warcraftlogs.png" alt="' . esc_attr( $character_info['name'] ) . '"></a>';
                                    echo '<a href="' . $wow_url . '" target="_blank"><img decoding="async" src="'.get_stylesheet_directory_uri().'/assets/images/wow.png" alt="' . esc_attr( $character_info['name'] ) . '"></a>';
                                    echo '</div>';
                                echo '</div>';
                            echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }

                    echo '</div>'; // Cerrar el contenedor del grupo de rango
                }
            }

            echo '</div>'; // Cerrar sp-template-player-gallery
            return ob_get_clean();
        }

        // Función para renderizar placeholders (skeleton) si no hay datos
        protected function render_loading_skeleton( $columns ) {
            ob_start();
            echo '<div class="sp-template sp-template-player-gallery sp-template-gallery">';
            echo '<div class="team-roster team-roster--grid-sm team-roster--grid-col-' . esc_attr( $columns ) . '">';
            for ( $i = 0; $i < 6; $i++ ) {
                echo $this->render_loading_skeleton_item();
            }
            echo '</div>';
            echo '</div>';
            return ob_get_clean();
        }

        // Función para renderizar un item de placeholder
        protected function render_loading_skeleton_item() {
            ob_start();
            echo '<div class="team-roster__item loading-skeleton">';
                echo '<div class="team-roster__holder">';
                    echo '<figure class="team-roster__img"><div class="skeleton-avatar"></div></figure>';
                    echo '<div class="team-roster__content">';
                        echo '<h4 class="team-roster__name"><div class="skeleton-name"></div></h4>';
                        echo '<div class="skeleton-icons">';
                            echo '<div class="skeleton-race-class"></div>';
                            echo '<div class="skeleton-social-icons"></div>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
            return ob_get_clean();
        }
    }
}
