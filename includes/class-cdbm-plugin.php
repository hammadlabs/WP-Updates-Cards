<?php
class CDBM_Plugin {
    private static $instance = null;
    private $version;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->version = CDBM_VERSION;
        $this->init_hooks();
    }

    private function init_hooks() {
        add_action('init', array($this, 'register_post_type'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style(
            'cdbm-frontend',
            CDBM_PLUGIN_URL . 'assets/css/frontend.css',
            array(),
            $this->version
        );

        // Add inline CSS for theme compatibility
        $custom_css = "
            .cdbm-box {
                background-color: inherit;
            }
        ";
        wp_add_inline_style('cdbm-frontend', $custom_css);
    }
}