<?php
if (!defined('ABSPATH')) { exit; }
class CDBM_Frontend {
    public function __construct() { add_shortcode('div_box', array($this, 'shortcode')); }
    public function shortcode($atts) {
        $atts = shortcode_atts(array('columns' => 3, 'show_description' => 'true', 'limit' => 0, 'categories' => '', 'category' => '', 'categoris' => ''), $atts, 'div_box');
        $columns = min(3, max(1, absint($atts['columns']))); $show_description = filter_var($atts['show_description'], FILTER_VALIDATE_BOOLEAN); $limit = absint($atts['limit']);
        $requested = $atts['categories'] !== '' ? $atts['categories'] : $atts['category']; $categories = array_filter(array_map('sanitize_title', preg_split('/[\s,]+/', (string) $requested)));
        $boxes = CDBM_Database::get_div_boxes();
        if ($categories) { $boxes = array_values(array_filter($boxes, function ($box) use ($categories) { return (bool) array_intersect($categories, $box['categories']); })); }
        if ($limit) { $boxes = array_slice($boxes, 0, $limit); }
        if (!$boxes) { return ''; }
        $html = '<section class="cdbm-container cdbm-columns-' . esc_attr($columns) . '" aria-label="' . esc_attr__('Updates', 'wp-updates-plugin') . '">';
        foreach ($boxes as $box) { $html .= $this->card($box, $show_description); }
        return $html . '</section>';
    }
    private function card($box, $show_description) {
        $html = '<article class="cdbm-box-item">';
        if ($box['image_url']) { $html .= '<div class="cdbm-box-image"><img src="' . esc_url($box['image_url']) . '" alt="' . esc_attr($box['title']) . '" loading="lazy"></div>'; }
        $html .= '<div class="cdbm-box-content"><h2 class="cdbm-box-title">' . esc_html($box['title']) . '</h2>';
        if ($show_description) { $html .= '<div class="cdbm-box-description">' . wp_kses_post(wpautop($box['description'])) . '</div>'; }
        if ($box['button_url']) { $settings = CDBM_Database::get_settings(); $text = $box['button_text'] ? $box['button_text'] : $settings['default_button_text']; $target = !empty($settings['button_new_tab']) ? ' target="_blank" rel="noopener noreferrer"' : ''; $html .= '<p class="cdbm-box-action"><a class="cdbm-button" href="' . esc_url($box['button_url']) . '"' . $target . '>' . esc_html($text) . '</a></p>'; }
        return $html . '</div></article>';
    }
}
