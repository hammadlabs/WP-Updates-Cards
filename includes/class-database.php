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
        return wp_parse_args(get_option('cdbm_settings', array()), array('default_button_text' => 'Read more', 'button_new_tab' => false));
    }
    public static function save_settings($data) {
        update_option('cdbm_settings', array('default_button_text' => sanitize_text_field($data['default_button_text'] ?? 'Read more'), 'button_new_tab' => !empty($data['button_new_tab'])));
    }
    public static function get_div_boxes() {
        $boxes = get_option('cdbm_div_boxes', array()); $boxes = is_array($boxes) ? $boxes : array(); $boxes = array_map(array(__CLASS__, 'normalise_box'), $boxes);
        usort($boxes, function ($a, $b) { return strtotime($b['created_at']) <=> strtotime($a['created_at']); }); return $boxes;
    }
    public static function get_div_box($id) { foreach (self::get_div_boxes() as $box) { if (hash_equals($box['id'], (string) $id)) { return $box; } } return null; }
    public static function save_div_box($data) {
        $boxes = self::get_div_boxes(); $existing = !empty($data['id']) ? self::get_div_box($data['id']) : null; $data['id'] = $existing ? $existing['id'] : wp_generate_uuid4(); $data['post_id'] = $existing ? $existing['post_id'] : 0; $data['created_at'] = $existing ? $existing['created_at'] : current_time('mysql'); $data['updated_at'] = current_time('mysql'); $box = self::normalise_box($data);
        $description_length = function_exists('mb_strlen') ? mb_strlen($box['description']) : strlen($box['description']);
        if ($box['title'] === '' || $box['description'] === '' || $description_length > 500) { return false; }
        $box['post_id'] = self::upsert_search_post($box);
        $replaced = false; foreach ($boxes as $i => $stored) { if ($stored['id'] === $box['id']) { $boxes[$i] = $box; $replaced = true; break; } } if (!$replaced) { $boxes[] = $box; } update_option('cdbm_div_boxes', $boxes); return $box['id'];
    }
    public static function duplicate_div_box($id) {
        $box = self::get_div_box($id);
        if (!$box) { return false; }
        unset($box['id'], $box['post_id'], $box['created_at'], $box['updated_at']);
        $box['title'] = sprintf(__('Copy of %s', 'wp-updates-plugin'), $box['title']);
        return self::save_div_box($box);
    }
    public static function delete_div_box($id) {
        $boxes = self::get_div_boxes();
        $deleted = null;
        $remaining = array();
        foreach ($boxes as $box) {
            if (hash_equals($box['id'], (string) $id)) { $deleted = $box; continue; }
            $remaining[] = $box;
        }
        if (!$deleted) { return false; }
        self::delete_search_post($deleted);
        update_option('cdbm_div_boxes', $remaining);
        return true;
    }
    public static function get_div_box_by_post_id($post_id) {
        $post_id = absint($post_id);
        if (!$post_id) { return null; }
        foreach (self::get_div_boxes() as $box) {
            if ((int) $box['post_id'] === $post_id) { return $box; }
        }
        return null;
    }
    public static function search_div_boxes_by_title($term, $boxes = null) {
        $boxes = is_array($boxes) ? $boxes : self::get_div_boxes();
        $term = trim((string) $term);
        if ($term === '') { return $boxes; }
        $lower = function_exists('mb_strtolower') ? 'mb_strtolower' : 'strtolower';
        $needle = $lower($term);
        $words = array_filter(preg_split('/\s+/', $needle));
        if (!$words) { return $boxes; }
        return array_values(array_filter($boxes, function ($box) use ($words, $lower) {
            $title = $lower($box['title']);
            foreach ($words as $word) {
                if ($word === '' || strpos($title, $word) === false) { return false; }
            }
            return true;
        }));
    }
    public static function ensure_search_index() {
        if (get_option('cdbm_search_index_version') === '2') { return; }
        if (!post_type_exists(CDBM_CARD_POST_TYPE)) { return; }
        $boxes = self::get_div_boxes();
        foreach ($boxes as $i => $box) {
            $boxes[$i]['post_id'] = self::upsert_search_post($box);
        }
        update_option('cdbm_div_boxes', $boxes);
        update_option('cdbm_search_index_version', '2');
    }
    private static function upsert_search_post($box) {
        $postarr = array(
            'post_type' => CDBM_CARD_POST_TYPE,
            'post_status' => 'publish',
            'post_title' => $box['title'],
            'post_content' => $box['description'],
            'post_excerpt' => $box['description'],
        );
        $existing_id = absint($box['post_id']);
        if ($existing_id && get_post_type($existing_id) === CDBM_CARD_POST_TYPE) {
            $postarr['ID'] = $existing_id;
            $post_id = wp_update_post($postarr, true);
        } else {
            $post_id = wp_insert_post($postarr, true);
        }
        if (is_wp_error($post_id) || !$post_id) { return $existing_id; }
        update_post_meta($post_id, '_cdbm_box_id', $box['id']);
        if (!empty($box['image_id'])) { set_post_thumbnail($post_id, (int) $box['image_id']); } else { delete_post_thumbnail($post_id); }
        return (int) $post_id;
    }
    private static function delete_search_post($box) {
        $post_id = absint($box['post_id'] ?? 0);
        if ($post_id && get_post_type($post_id) === CDBM_CARD_POST_TYPE) { wp_delete_post($post_id, true); }
    }
    private static function normalise_box($data) {
        $categories = isset($data['categories']) ? $data['categories'] : ''; if (is_array($categories)) { $categories = implode(',', $categories); } $categories = array_values(array_unique(array_filter(array_map('sanitize_title', preg_split('/[\s,]+/', (string) $categories)))));
        return array('id' => isset($data['id']) ? sanitize_text_field($data['id']) : '', 'title' => isset($data['title']) ? sanitize_text_field($data['title']) : '', 'description' => isset($data['description']) ? sanitize_textarea_field($data['description']) : '', 'image_id' => isset($data['image_id']) ? absint($data['image_id']) : 0, 'image_url' => isset($data['image_url']) ? esc_url_raw($data['image_url']) : '', 'button_url' => isset($data['button_url']) ? esc_url_raw($data['button_url']) : '', 'button_text' => isset($data['button_text']) ? sanitize_text_field($data['button_text']) : '', 'categories' => $categories, 'post_id' => isset($data['post_id']) ? absint($data['post_id']) : 0, 'created_at' => !empty($data['created_at']) ? sanitize_text_field($data['created_at']) : current_time('mysql'), 'updated_at' => !empty($data['updated_at']) ? sanitize_text_field($data['updated_at']) : '');
    }
}
