<?php
class VLM_Frontend_Template {
    public static function render_video_grid($args = array()) {
        $default_args = array(
            'post_type' => 'video_lesson',
            'posts_per_page' => 12,
            'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
        );

        $args = wp_parse_args($args, $default_args);
        $query = new WP_Query($args);

        ob_start();
        ?>
        <div class="vlm-video-grid">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <div class="vlm-video-item">
                    <div class="vlm-video-thumbnail">
                        <?php the_post_thumbnail('medium'); ?>
                    </div>
                    <h3 class="vlm-video-title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    <div class="vlm-video-meta">
                        <?php
                        $subject = get_post_meta(get_the_ID(), '_vlm_subject', true);
                        $level = get_post_meta(get_the_ID(), '_vlm_level', true);
                        if ($subject) {
                            echo '<span class="vlm-subject">' . get_the_title($subject) . '</span>';
                        }
                        if ($level) {
                            echo '<span class="vlm-level">' . get_the_title($level) . '</span>';
                        }
                        ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }

    public static function render_video_list($args = array()) {
        $default_args = array(
            'post_type' => 'video_lesson',
            'posts_per_page' => 10,
            'paged' => get_query_var('paged') ? get_query_var('paged') : 1,
        );

        $args = wp_parse_args($args, $default_args);
        $query = new WP_Query($args);

        ob_start();
        ?>
        <div class="vlm-video-list">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <div class="vlm-video-item-list">
                    <div class="vlm-video-thumbnail">
                        <?php the_post_thumbnail('thumbnail'); ?>
                    </div>
                    <div class="vlm-video-content">
                        <h3 class="vlm-video-title">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                        </h3>
                        <div class="vlm-video-excerpt">
                            <?php the_excerpt(); ?>
                        </div>
                        <div class="vlm-video-meta">
                            <?php
                            $subject = get_post_meta(get_the_ID(), '_vlm_subject', true);
                            $level = get_post_meta(get_the_ID(), '_vlm_level', true);
                            if ($subject) {
                                echo '<span class="vlm-subject">' . get_the_title($subject) . '</span>';
                            }
                            if ($level) {
                                echo '<span class="vlm-level">' . get_the_title($level) . '</span>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
}
