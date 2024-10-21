<?php

require_once ABSPATH . 'wp-content/plugins/blizzard-api-wp/includes/raiderio/class-blizzard-api-raiderio.php';

// ALC: Raid Leader Card
vc_map(array(
    'name'        => esc_html__('ALC: Raid Leader Card', 'alchemists'),
    'base'        => 'alc_RL_bio_card',
    'icon'        => get_template_directory_uri() . '/admin/images/js_composer/alc_staff_bio_card.png',
    'category'    => esc_html__('ALC', 'alchemists'),
    'description' => esc_html__('Dynamically add the Raid Leader info.', 'alchemists'),
    'params'      => array(
        array(
            'type'        => 'textfield',
            'heading'     => esc_html__('Widget title', 'alchemists'),
            'param_name'  => 'title',
            'description' => esc_html__('Enter text used as widget title (Note: located above content element).', 'alchemists'),
        ),
        array(
            'type'        => 'textarea',
            'heading'     => esc_html__('Sobre el Raid Leader', 'alchemists'),
            'param_name'  => 'sobre_rl',
            'description' => esc_html__('Enter the Raid Leader Description.', 'alchemists'),
        ),
        vc_map_add_css_animation(),
        array(
            'type'        => 'el_id',
            'heading'     => esc_html__('Element ID', 'alchemists'),
            'param_name'  => 'el_id',
            'description' => esc_html__('Enter element ID (Note: make sure it is unique and valid.', 'alchemists'),
        ),
        array(
            'type'        => 'textfield',
            'heading'     => esc_html__('Extra class name', 'alchemists'),
            'param_name'  => 'el_class',
            'description' => esc_html__('Style particular content element differently - add a class name and refer to it in custom CSS.', 'alchemists'),
        ),
        array(
            'type'        => 'css_editor',
            'heading'     => esc_html__('CSS box', 'alchemists'),
            'param_name'  => 'css',
            'group'       => esc_html__('Design Options', 'alchemists'),
        ),
    )
));

if (class_exists('WPBakeryShortCode')) {
    class WPBakeryShortCode_Alc_RL_Bio_Card extends WPBakeryShortCode {
        protected function content($atts, $content = null) {
            // Atributos del shortcode
            $atts = shortcode_atts(array(
                'title'        => '',
                'sobre_rl'     => '',
                'el_id'        => '',
                'el_class'     => '',
                'css'          => '',
                'css_animation'=> '',
            ), $atts);

            // Definir variables
            $title = $atts['title'];
            $sobre_rl = $atts['sobre_rl'];
            $el_id = $atts['el_id'];
            $el_class = $atts['el_class'];
            $css = $atts['css'];
            $css_animation = $atts['css_animation'];

            // Obtener datos de RaiderIO desde los datos almacenados en la opción
            $roster_data = get_option('blizzard_guild_members');

            if (empty($roster_data)) {
                return '<p>' . esc_html__('No data found for Raid Leader.', 'alchemists') . '</p>';
            }

            // Filtrar por el rango de Raid Leader (1)
            $raid_leaders = array_filter($roster_data, function ($member) {
                return isset($member['rank']) && $member['rank'] === 1;
            });

            if (empty($raid_leaders)) {
                return '<p>' . esc_html__('No Raid Leader found.', 'alchemists') . '</p>';
            }

            $raid_leader_info = reset($raid_leaders); // Obtener el primer Raid Leader

            // Definir la URL del avatar si está disponible, o usar un placeholder
            $avatar_url = isset($raid_leader_info['thumbnail_url'])
                ? esc_url($raid_leader_info['thumbnail_url'])
                : get_stylesheet_directory_uri() . '/assets/images/placeholder-140x210.jpg';

            // Construir clases CSS para el contenedor
            $class_to_filter = 'card card--clean alc-staff';
            $class_to_filter .= vc_shortcode_custom_css_class($css, ' ') . (!empty($el_class) ? ' ' . $el_class : '') . ($css_animation ? ' ' . $css_animation : '');
            $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);

            // Atributos del wrapper
            $wrapper_attributes = array();
            if (!empty($el_id)) {
                $wrapper_attributes[] = 'id="' . esc_attr($el_id) . '"';
            }

            // Construir la URL de la raza y clase
            $class_name = isset($raid_leader_info['class']) ? sanitize_title($raid_leader_info['class']) : '';
            $race_name = isset($raid_leader_info['race']) ? sanitize_title($raid_leader_info['race']) : '';
            $region = get_option("blizzard_api_region");
            $raid_leader_name = $raid_leader_info['name'];
            $raid_leader_realm = sanitize_title($raid_leader_info['realm']);

            // URL de iconos
            $base_path = get_stylesheet_directory_uri() . '/assets/images';
            $race_icon_url = "{$base_path}/race_{$race_name}.jpg";
            $class_icon_url = "{$base_path}/{$class_name}.jpg";

            // URLs externas
            $raiderio_url = "https://raider.io/characters/{$region}/{$raid_leader_realm}/{$raid_leader_name}";
            $warcraftlogs_url = "https://www.warcraftlogs.com/character/{$region}/{$raid_leader_realm}/{$raid_leader_name}";
            $wow_url = "https://worldofwarcraft.blizzard.com/es-es/character/{$region}/{$raid_leader_realm}/{$raid_leader_name}";

            ob_start();
            ?>
            <!-- Raid Leader Card -->
            <div <?php echo implode(' ', $wrapper_attributes); ?> class="<?php echo esc_attr($css_class); ?>">
                <div class="card__content">
                    <div class="card">
                        <div class="card__content">
                            <div class="row">
                                <div class="col-lg-4">
                                    <div class="alc-staff__photo">
                                        <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($raid_leader_name); ?>" />
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="alc-staff-inner">
                                        <header class="alc-staff__header">
                                            <h1 class="alc-staff__header-name">
                                                <?php echo esc_html($raid_leader_name); ?>
                                            </h1>
                                            <span class="alc-staff__header-role">
                                                <?php echo esc_html($title); ?>
                                            </span>
                                        </header>
                                        <p><?php echo esc_html($sobre_rl); ?></p>
                                        <div class="player-icons">
                                            <div class="race-class">
                                                <?php if ($race_name) : ?>
                                                    <figure class="team-roster__race-icon">
                                                        <img src="<?php echo esc_url($race_icon_url); ?>" alt="<?php echo esc_attr($race_name); ?>">
                                                    </figure>
                                                <?php endif; ?>
                                                <?php if ($class_name) : ?>
                                                    <figure class="team-roster__class-icon">
                                                        <img src="<?php echo esc_url($class_icon_url); ?>" alt="<?php echo esc_attr($class_name); ?>">
                                                    </figure>
                                                <?php endif; ?>
                                            </div>
                                            <div class="social-icons">
                                                <a href="<?php echo esc_url($raiderio_url); ?>" target="_blank">
                                                    <img decoding="async" src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/raiderio.png'); ?>" alt="Raider.IO">
                                                </a>
                                                <a href="<?php echo esc_url($warcraftlogs_url); ?>" target="_blank">
                                                    <img decoding="async" src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/warcraftlogs.png'); ?>" alt="Warcraft Logs">
                                                </a>
                                                <a href="<?php echo esc_url($wow_url); ?>" target="_blank">
                                                    <img decoding="async" src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/wow.png'); ?>" alt="World of Warcraft">
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Raid Leader Card / End -->
            <?php
            return ob_get_clean();
        }
    }
}
