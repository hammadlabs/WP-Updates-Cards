<?php
/**
 * Plugin Name: WP Updates Plugin
 * Description: Create and display searchable update cards grouped with custom categories.
 * Version: 1.2.0
 * License: GPL v2 or later
 * Text Domain: wp-updates-plugin
 */
if (!defined('ABSPATH')) { exit; }
define('CDBM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CDBM_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('CDBM_VERSION', '1.2.0');
define('CDBM_CARD_POST_TYPE', 'cdbm_card');
class WP_Updates_Plugin {
    public function __construct() { add_action('plugins_loaded', array($this, 'init')); register_activation_hook(__FILE__, array($this, 'activate')); }
    public function init() {
        load_plugin_textdomain('wp-updates-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');
        require_once CDBM_PLUGIN_PATH . 'includes/class-database.php'; require_once CDBM_PLUGIN_PATH . 'includes/class-admin.php'; require_once CDBM_PLUGIN_PATH . 'includes/class-frontend.php';
        add_action('init', array($this, 'register_card_post_type'));
        add_action('init', array('CDBM_Database', 'ensure_search_index'), 20);
        add_action('pre_get_posts', array($this, 'include_cards_in_search'), 99);
        new CDBM_Admin(); new CDBM_Frontend(); add_action('admin_enqueue_scripts', array($this, 'admin_assets')); add_action('wp_enqueue_scripts', array($this, 'frontend_assets'));
    }
    public function register_card_post_type() {
        register_post_type(CDBM_CARD_POST_TYPE, array(
            'labels' => array(
                'name' => __('Update cards', 'wp-updates-plugin'),
                'singular_name' => __('Update card', 'wp-updates-plugin'),
                'search_items' => __('Search cards', 'wp-updates-plugin'),
            ),
            'public' => true,
            'publicly_queryable' => true,
            'exclude_from_search' => false,
            'show_ui' => false,
            'show_in_menu' => false,
            'show_in_rest' => false,
            'has_archive' => false,
            'rewrite' => array('slug' => 'update-card'),
            'supports' => array('title', 'thumbnail'),
        ));
    }
    public function include_cards_in_search($query) {
        if (is_admin() || !$query->is_main_query() || !$query->is_search()) { return; }
        $post_type = $query->get('post_type');
        if (empty($post_type) || $post_type === 'any') { return; }
        $post_types = is_array($post_type) ? $post_type : array($post_type);
        if (in_array('post', $post_types, true) && !in_array(CDBM_CARD_POST_TYPE, $post_types, true)) {
            $post_types[] = CDBM_CARD_POST_TYPE;
            $query->set('post_type', $post_types);
        }
    }
    public function admin_assets($hook) {
        if (strpos($hook, 'wp-updates-plugin') === false) { return; }
        wp_enqueue_media(); wp_enqueue_style('cdbm-admin-style', CDBM_PLUGIN_URL . 'assets/css/admin.css', array(), CDBM_VERSION); wp_enqueue_script('cdbm-admin-script', CDBM_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), CDBM_VERSION, true);
        wp_localize_script('cdbm-admin-script', 'cdbmAdmin', array('selectImage' => __('Select image', 'wp-updates-plugin'), 'useImage' => __('Use image', 'wp-updates-plugin')));
    }
    public function frontend_assets() {
        wp_enqueue_style('cdbm-frontend-style', CDBM_PLUGIN_URL . 'assets/css/frontend.css', array(), CDBM_VERSION);
        wp_enqueue_script('cdbm-frontend-script', CDBM_PLUGIN_URL . 'assets/js/frontend.js', array('jquery'), CDBM_VERSION, true);
    }
    public function activate() {
        require_once CDBM_PLUGIN_PATH . 'includes/class-database.php';
        if (get_option('cdbm_div_boxes', null) === null) { add_option('cdbm_div_boxes', array()); }
        if (get_option('cdbm_categories', null) === null) { add_option('cdbm_categories', array()); }
        if (get_option('cdbm_settings', null) === null) { add_option('cdbm_settings', array('default_button_text' => 'Read more')); }
        $this->register_card_post_type();
        CDBM_Database::ensure_search_index();
        flush_rewrite_rules();
    }
}
new WP_Updates_Plugin();
