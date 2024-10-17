<?php

require_once ABSPATH . 'wp-content/plugins/blizzard-api-wp/includes/raiderio/class-blizzard-api-raiderio.php';
require_once ABSPATH . 'wp-content/plugins/blizzard-api-wp/includes/world-of-warcraft/class-blizzard-api-wow.php';

// ALC: Raid Progress Block
vc_map(array(
    'name'        => esc_html__('ALC: Raid Progress Timeline', 'alchemists'),
    'base'        => 'alc_raid_progress_timeline',
    'icon'        => get_template_directory_uri() . '/admin/images/js_composer/alc_raid_progress.png',
    'category'    => esc_html__('ALC', 'alchemists'),
    'description' => esc_html__('Shows raid progress with a timeline and boss images.', 'alchemists'),
    'params'      => array(
        array(
            'type'        => 'textfield',
            'heading'     => esc_html__('Widget title', 'alchemists'),
            'param_name'  => 'title',
            'description' => esc_html__('Enter widget title.', 'alchemists'),
        ),
        array(
            'type'        => 'textfield',
            'heading'     => esc_html__('Raid Slug in English', 'alchemists'),
            'param_name'  => 'raid_slug',
            'description' => esc_html__('Enter the slug of the target raid (nerubar-palace).', 'alchemists'),
        ),
        array(
            'type'        => 'dropdown',
            'heading'     => esc_html__('Progress Difficulty', 'alchemists'),
            'param_name'  => 'progress_difficulty',
            'value'       => array(
                esc_html__('Ninguna', 'alchemists') => '',
                esc_html__('Normal', 'alchemists') => 'normal',
                esc_html__('Heroic', 'alchemists') => 'heroic',
                esc_html__('Mythic', 'alchemists') => 'mythic'
            ),
            'description' => esc_html__('Select the difficulty for progress display.', 'alchemists'),
        ),
        vc_map_add_css_animation(),
        array(
            'type'        => 'el_id',
            'heading'     => esc_html__('Element ID', 'alchemists'),
            'param_name'  => 'el_id',
            'description' => esc_html__('Enter element ID.', 'alchemists'),
        ),
        array(
            'type'        => 'textfield',
            'heading'     => esc_html__('Extra class name', 'alchemists'),
            'param_name'  => 'el_class',
            'description' => esc_html__('Style element with custom class name.', 'alchemists'),
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
    class WPBakeryShortCode_Alc_Raid_Progress_Timeline extends WPBakeryShortCode {
        protected function content($atts, $content = null) {
            // Atributos del shortcode
            $atts = shortcode_atts(array(
                'title'             => '',
                'raid_slug'         => '',
                'progress_difficulty'   => '',
                'el_id'             => '',
                'el_class'          => '',
                'css'               => '',
                'css_animation'     => '',
            ), $atts);

            // Definir variables
            $title = $atts['title'];
            $el_id = $atts['el_id'];
            $el_class = $atts['el_class'];
            $css = $atts['css'];
            $css_animation = $atts['css_animation'];
            $difficulty = $atts['progress_difficulty'];

            // Obtener datos de la base de datos
            $region = get_option('blizzard_api_region');
            $realm = get_option('blizzard_api_realm');
            $guild = rawurlencode(get_option('blizzard_api_guild_original'));

            // Obtener datos de progreso de Raider.IO
            $raid_progress_data = Blizzard_Api_RaiderIO::get_guild_raid_progress($region, $realm, $guild);

            // Procesar los datos de progreso según la dificultad seleccionada
            $raid_name = $atts['raid_slug'];
            $raid_progress = $raid_progress_data['raid_progression'][$raid_name];

            $total_bosses = $raid_progress['total_bosses'];

            // Variables de progreso según la dificultad seleccionada
            switch ($difficulty) {
                case 'normal':
                    $defeated_bosses = $raid_progress['normal_bosses_killed'];
                    break;
                case 'heroic':
                    $defeated_bosses = $raid_progress['heroic_bosses_killed'];
                    break;
                case 'mythic':
                    $defeated_bosses = $raid_progress['mythic_bosses_killed'];
                    break;
                default:
                    $defeated_bosses = $raid_progress['heroic_bosses_killed']; // Heroico por defecto
                    break;
            }

            // Lista de jefes y sus imágenes en tu tema hijo
            $bosses = array(
                array('id' => 1, 'name' => 'Ulgrax the Devourer', 'image' => 'ulgrax.png'),
                array('id' => 2, 'name' => 'The Bloodbound Horror', 'image' => 'bloodbound.png'),
                array('id' => 3, 'name' => 'Sikran, Captain of the Sureki', 'image' => 'sikran.png'),
                array('id' => 4, 'name' => 'Rasha\'nan', 'image' => 'rashanan.png'),
                array('id' => 5, 'name' => 'Broodtwister Ovi\'nax', 'image' => 'broodtwister.png'),
                array('id' => 6, 'name' => 'Nexus-Princess Ky\'veza', 'image' => 'kyveza.png'),
                array('id' => 7, 'name' => 'The Silken Court', 'image' => 'silkencourt.png'),
                array('id' => 8, 'name' => 'Queen Ansurek', 'image' => 'queenansurek.png')
            );

            // Construir clases CSS para el contenedor
            $class_to_filter = 'alc-raid-progress-timeline';
            $class_to_filter .= vc_shortcode_custom_css_class($css, ' ') . (!empty($el_class) ? ' ' . $el_class : '') . ($css_animation ? ' ' . $css_animation : '');
            $css_class = apply_filters(VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, $class_to_filter, $this->settings['base'], $atts);

            // Atributos del wrapper
            $wrapper_attributes = array();
            if (!empty($el_id)) {
                $wrapper_attributes[] = 'id="' . esc_attr($el_id) . '"';
            }

            ob_start();
            ?>
            <!-- Raid Progress Timeline -->
            <div <?php echo implode(' ', $wrapper_attributes); ?> class="<?php echo esc_attr($css_class); ?>">
                <div class="raid-timeline">
                    <?php if (!empty($title)) : ?>
                        <h3><?php echo esc_html($title); ?></h3>
                    <?php endif; ?>
                        
                    <div class="timeline-wrapper">
                        <div class="timeline-item-up">
                            <?php foreach ($bosses as $boss) : 
                                // Verificar si el jefe está derrotado en las distintas dificultades
                                $is_defeated = $defeated_bosses >= $boss['id']; 
                            ?>
                            <div class="timeline-boss">
                                <div class="timeline-img">
                                    <img class="<?php echo ($is_defeated ? 'blurred' : ''); ?>" src="<?php echo get_stylesheet_directory_uri(); ?>/assets/images/<?php echo esc_attr($boss['image']); ?>" alt="<?php echo esc_attr($boss['name']); ?>" title="<?php echo esc_attr($boss['name']); ?>" />
                                </div>
                                <div class="timeline-content">
                                    <h5><?php echo esc_html($boss['name']); ?></h5>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Barra de tiempo -->
                        <div class="timeline-bar">
                            <div class="timeline-fill" style="width: 0%;"></div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- jQuery Script to Update Progress Bar -->
            <script>
                jQuery(document).ready(function($) {
                    var totalBosses = <?php echo $total_bosses; ?>;
                    var defeated_bosses = <?php echo $defeated_bosses; ?>;
                    var widthComponent = document.querySelector(".alc-raid-progress-timeline").offsetWidth;
                    var bossboxComponent = document.querySelector(".timeline-boss").offsetWidth / 2;
                    var progressPercentage = ((widthComponent/totalBosses)*defeated_bosses) - bossboxComponent;

                        if(defeated_bosses == totalBosses){

                            var progressPercentage = widthComponent;

                        }
                    // Animate the timeline-fill to match the defeated bosses
                    $('.timeline-fill').animate({
                        width: progressPercentage + 'px'
                    }, 2000); // Adjust the animation time as needed
                });
            </script>
            <?php
            return ob_get_clean();
        }
    }
}

