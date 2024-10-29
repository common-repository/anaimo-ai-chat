<?php

/*
 * Plugin Name:       Anaimo AI Chat
 * Description:       Adds a chat interface to your WordPress site using an iframe.
 * Version:           1.0.6
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Author:            Anaimo
 * Author URI:        https://anaimo.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent access to plugin files
if (!defined('ABSPATH')) exit; // Exit if accessed directly

add_action('wp_enqueue_scripts', 'anaimoai_chat_enqueue_scripts');
add_shortcode('anaimoai_chat', 'anaimoai_chat_output');
add_action( 'admin_menu', 'anaimoai_chat_add_admin_menu' );
add_action( 'admin_init', 'anaimoai_chat_settings_init' );

function anaimoai_chat_enqueue_scripts() {
    // Enqueue your custom styles and scripts
    wp_enqueue_style('anaimoai-chat-style', plugins_url('css/style.css?v=2', __FILE__), array(), '1.0.0', 'all' );
    wp_enqueue_script('anaimoai-chat-script', plugins_url('js/script.js', __FILE__), array('jquery'), '1.0', true);    
}

function anaimoai_chat_output($atts) {
    $options = get_option('anaimoai_chat_settings');
    $selected_option_0 = isset($options['anaimoai_chat_select_field_0']) ? $options['anaimoai_chat_select_field_0'] : '3'; 
    $selected_option_1 = isset($options['anaimoai_chat_select_field_1']) ? $options['anaimoai_chat_select_field_1'] : '1';
    $icon_color = isset($options['anaimoai_chat_icon_color']) ? $options['anaimoai_chat_icon_color'] : '#000000'; 
    // Replaced with animated divs
    //$svg_content = file_get_contents(plugin_dir_path(__FILE__) . 'css/img/help-chat-negro.svg');
    //$svg_content = str_replace('%ICON_COLOR%', esc_attr($icon_color), $svg_content);
    
    $svg_content = '<div id="anim-container-element" class="anim-container-' . esc_html($selected_option_1) . '" style="background-color:' . esc_attr($icon_color) . '"><div class="anim-right"><img src="' . plugins_url('css/img/help-chat-right.svg', __FILE__) . '"/></div><div class="anim-left"><img src="' . plugins_url('css/img/help-chat-left.svg', __FILE__) . '"/></div></div>';

    // Use the stored values for usca, clie, and hash
    $usca = isset($options['anaimoai_chat_usca']) ? $options['anaimoai_chat_usca'] : '';
    $clie = isset($options['anaimoai_chat_clie']) ? $options['anaimoai_chat_clie'] : '';
    $hash = isset($options['anaimoai_chat_hash']) ? $options['anaimoai_chat_hash'] : '';
    $params = [
        'usca' => $usca,
        'clie' => $clie,
        'hash' => $hash
    ];

    $jsonParams = json_encode($params);

    $encodedParams = base64_encode($jsonParams);

    $anaimoai_src = "https://ai.anaimo.com/assistant/chat.php?data={$encodedParams}";
    ob_start();
    ?>
    <div id="anaimoai-help-chat-container">
        <div class="anaimoai-chat-btn-container_<?php echo esc_html($selected_option_1) ?>">
            <div onclick="anaimoai_toggleChat()">
                <?php echo $svg_content; ?>
            </div>
        </div>
        <div id="anaimoai-chatOverlay">
            <div id="anaimoai-chatContainer_<?php echo esc_html($selected_option_1) ?>">
                <span id="anaimoai-closeChat" onclick="anaimoai_toggleChat()">&#10005;</span>
                <iframe id="anaimoai-chatIframe"
                src="<?php echo esc_url($anaimoai_src); ?>"
                frameborder="0" width="100%" height="100%" scrolling="no"></iframe>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

function anaimoai_chat_add_admin_menu(  ) { 
    add_options_page( 'Anaimo AI Chat', 'Anaimo AI Chat', 'manage_options', 'anaimoai_chat', 'anaimoai_chat_options_page' );
}

function anaimoai_chat_settings_init() {
    register_setting('pluginPage', 'anaimoai_chat_settings');

    add_settings_section(
        'anaimoai_chat_pluginPage_section',
        __('Appearance', 'anaimoai'),
        'anaimoai_chat_settings_section_callback',
        'pluginPage'
    );

    add_settings_field(
        'anaimoai_chat_select_field_1',
        __('Chat button position', 'anaimoai'),
        'anaimoai_chat_select_field_1_render',
        'pluginPage',
        'anaimoai_chat_pluginPage_section'
    );

    add_settings_field(
        'anaimoai_chat_icon_color',
        __('Icon Color', 'anaimoai'),
        'anaimoai_chat_icon_color_render',
        'pluginPage',
        'anaimoai_chat_pluginPage_section'
    );

    add_settings_section(
        'anaimoai_chat_pluginPage_section',
        __('From the Anaimo AI Assistant, preview your use case and, from the generated URL, copy the values of the following fields and paste them here.', 'anaimoai'),
        'anaimoai_usca_clie_hash',
        'pluginPage'
    );

    // Add new fields for usca, clie, and hash
    add_settings_field(
        'anaimoai_chat_usca',
        __('USCA Value', 'anaimoai'),
        'anaimoai_chat_usca_render',
        'pluginPage',
        'anaimoai_chat_pluginPage_section'
    );

    add_settings_field(
        'anaimoai_chat_clie',
        __('CLIE Value', 'anaimoai'),
        'anaimoai_chat_clie_render',
        'pluginPage',
        'anaimoai_chat_pluginPage_section'
    );

    add_settings_field(
        'anaimoai_chat_hash',
        __('Hash Value', 'anaimoai'),
        'anaimoai_chat_hash_render',
        'pluginPage',
        'anaimoai_chat_pluginPage_section'
    );
}

function anaimoai_chat_usca_render() {
    $options = get_option('anaimoai_chat_settings');
    $usca = isset($options['anaimoai_chat_usca']) ? $options['anaimoai_chat_usca'] : '';
    ?>
    <input type='text' name='anaimoai_chat_settings[anaimoai_chat_usca]' value='<?php echo esc_attr($usca); ?>' />
    <?php
}

function anaimoai_chat_clie_render() {
    $options = get_option('anaimoai_chat_settings');
    $clie = isset($options['anaimoai_chat_clie']) ? $options['anaimoai_chat_clie'] : '';
    ?>
    <input type='text' name='anaimoai_chat_settings[anaimoai_chat_clie]' value='<?php echo esc_attr($clie); ?>' />
    <?php
}

function anaimoai_chat_hash_render() {
    $options = get_option('anaimoai_chat_settings');
    $hash = isset($options['anaimoai_chat_hash']) ? $options['anaimoai_chat_hash'] : '';
    ?>
    <input type='text' name='anaimoai_chat_settings[anaimoai_chat_hash]' value='<?php echo esc_attr($hash); ?>' />
    <?php
}

function anaimoai_chat_icon_color_render() {
    $options = get_option('anaimoai_chat_settings');
    $icon_color = isset($options['anaimoai_chat_icon_color']) ? $options['anaimoai_chat_icon_color'] : '#000000'; 
    ?>
    <input type='color' name='anaimoai_chat_settings[anaimoai_chat_icon_color]' value='<?php echo esc_attr($icon_color); ?>' />
    <?php
}

function anaimoai_chat_select_field_1_render(  ) { 
    $options = get_option( 'anaimoai_chat_settings' );
    ?>
    <select name='anaimoai_chat_settings[anaimoai_chat_select_field_1]'>
        <option value='1' <?php selected( $options['anaimoai_chat_select_field_1'], 1 ); ?>>Right</option>
        <option value='2' <?php selected( $options['anaimoai_chat_select_field_1'], 2 ); ?>>Left</option>
    </select>
    <?php
}

function anaimoai_chat_settings_section_callback(  ) { 
    echo esc_html(__( 'Change some appearance settings', 'anaimoai' ));
    
}

function anaimoai_usca_clie_hash(  ) { 
    // echo esc_html(__( 'From the Anaimo AI Assistant, preview your use case and, from the generated URL, copy the values of the following fields and paste them here.', 'anaimoai' ));
    ?>
    <!-- Add a button to redirect the user to obtain values -->
    <a class="button button-primary" href="https://ai.anaimo.com/assistant/" target="_blank">Open Anaimo AI Assistant</a>
    <?php
}


function anaimoai_chat_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>Anaimo AI Chat</h2>
        <?php
        settings_fields('pluginPage');
        do_settings_sections('pluginPage');
        submit_button();
        ?>
    </form>
    <?php
}

