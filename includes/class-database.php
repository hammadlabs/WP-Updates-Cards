<?php
if (!defined('ABSPATH')) { exit; }
class CDBM_Database {
    public static function get_categories() {
        $saved = get_option('cdbm_categories', array());
        $from_boxes = array();
        foreach (self::get_div_boxes() as $box) { $from_boxes = array_merge($from_boxes, $box['categories']); }
        $categories = array_unique(array_merge(is_array($saved) ? $saved : array(), $from_boxes));
        $categories = array_values(array_filter(array_map('sanitize_title', $categories)));
        sort($categories, SORT_NATURAL | SORT_FLAG_CASE);
        return $categories;
    }
    public static function add_category($category) {
        $category = sanitize_title($category);
        if ($category === '') { return false; }
        $categories = get_option('cdbm_categories', array());
        $categories = is_array($categories) ? $categories : array();
        if (!in_array($category, $categories, true)) { $categories[] = $category; update_option('cdbm_categories', $categories); }
        return $category;
    }
    public static function delete_category($category) {
        $category = sanitize_title($category);
        $categories = get_option('cdbm_categories', array());
        $categories = is_array($categories) ? $categories : array();
        update_option('cdbm_categories', array_values(array_filter($categories, function ($item) use ($category) { return $item !== $category; })));
        $boxes = self::get_div_boxes();
        foreach ($boxes as &$box) { $box['categories'] = array_values(array_filter($box['categories'], function ($item) use ($category) { return $item !== $category; })); }
        unset($box);
        update_option('cdbm_div_boxes', $boxes);
    }
    public static function get_settings() {
        return wp_parse_args(get_option('cdbm_settings', array()), array('default_button_text' => 'Read more'));
    }
    public static function save_settings($data) {
        update_option('cdbm_settings', array('default_button_text' => sanitize_text_field($data['default_button_text'] ?? 'Read more')));
    }
    public static function get_div_boxes() {
        $boxes = get_option('cdbm_div_boxes', array()); $boxes = is_array($boxes) ? $boxes : array(); $boxes = array_map(array(__CLASS__, 'normalise_box'), $boxes);
        usort($boxes, function ($a, $b) { return strtotime($b['created_at']) <=> strtotime($a['created_at']); }); return $boxes;
    }
    public static function get_div_box($id) { foreach (self::get_div_boxes() as $box) { if (hash_equals($box['id'], (string) $id)) { return $box; } } return null; }
    public static function save_div_box($data) {
        $boxes = self::get_div_boxes(); $existing = !empty($data['id']) ? self::get_div_box($data['id']) : null; $data['id'] = $existing ? $existing['id'] : wp_generate_uuid4(); $data['created_at'] = $existing ? $existing['created_at'] : current_time('mysql'); $data['updated_at'] = current_time('mysql'); $box = self::normalise_box($data);
        if ($box['title'] === '' || $box['description'] === '') { return false; } $replaced = false; foreach ($boxes as $i => $stored) { if ($stored['id'] === $box['id']) { $boxes[$i] = $box; $replaced = true; break; } } if (!$replaced) { $boxes[] = $box; } update_option('cdbm_div_boxes', $boxes); return $box['id'];
    }
    public static function delete_div_box($id) { $boxes = self::get_div_boxes(); $remaining = array_values(array_filter($boxes, function ($box) use ($id) { return !hash_equals($box['id'], (string) $id); })); if (count($remaining) === count($boxes)) { return false; } update_option('cdbm_div_boxes', $remaining); return true; }
    private static function normalise_box($data) {
        $categories = isset($data['categories']) ? $data['categories'] : ''; if (is_array($categories)) { $categories = implode(',', $categories); } $categories = array_values(array_unique(array_filter(array_map('sanitize_title', preg_split('/[\s,]+/', (string) $categories)))));
        return array('id' => isset($data['id']) ? sanitize_text_field($data['id']) : '', 'title' => isset($data['title']) ? sanitize_text_field($data['title']) : '', 'description' => isset($data['description']) ? sanitize_textarea_field($data['description']) : '', 'image_id' => isset($data['image_id']) ? absint($data['image_id']) : 0, 'image_url' => isset($data['image_url']) ? esc_url_raw($data['image_url']) : '', 'button_url' => isset($data['button_url']) ? esc_url_raw($data['button_url']) : '', 'button_text' => isset($data['button_text']) ? sanitize_text_field($data['button_text']) : '', 'categories' => $categories, 'created_at' => !empty($data['created_at']) ? sanitize_text_field($data['created_at']) : current_time('mysql'), 'updated_at' => !empty($data['updated_at']) ? sanitize_text_field($data['updated_at']) : '');
    }
}
