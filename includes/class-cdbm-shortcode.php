<?php
class CDBM_Shortcode {
    public function render_divbox($atts) {
        $atts = shortcode_atts(array(
            'columns' => 3,
            'show_description' => true,
        ), $atts);

        $columns = absint($atts['columns']);
        if ($columns < 1 || $columns > 3) {
            $columns = 3;
        }

        $args = array(
            'post_type' => 'divbox',
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
        );

        $query = new WP_Query($args);
        
        if (!$query->have_posts()) {
            return '';
        }

        ob_start();
        ?>
        <div class="cdbm-grid" style="display: grid; grid-template-columns: repeat(<?php echo esc_attr($columns); ?>, 1fr); gap: 20px;">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <div class="cdbm-box">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="cdbm-box-image">
                            <?php echo wp_get_attachment_image(get_post_thumbnail_id(), 'medium'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="cdbm-box-content">
                        <h2 class="cdbm-box-title"><?php echo esc_html(get_the_title()); ?></h2>
                        <?php if ($atts['show_description']) : ?>
                            <div class="cdbm-box-description">
                                <?php echo wp_kses_post(get_the_content()); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
}