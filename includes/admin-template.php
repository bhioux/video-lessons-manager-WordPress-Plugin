<?php
class VLM_Admin_Template {
    public static function render_video_meta_box($post) {
        wp_nonce_field('vlm_video_save', 'vlm_video_nonce');
        $video_url = get_post_meta($post->ID, '_vlm_video_url', true);
        $subject = get_post_meta($post->ID, '_vlm_subject', true);
        $level = get_post_meta($post->ID, '_vlm_level', true);
        $class = get_post_meta($post->ID, '_vlm_class', true);
        ?>
        <div class="vlm-meta-box">
            <p>
                <label for="vlm_video_url"><?php _e('Bunny.net Video URL', 'video-lessons-manager'); ?></label>
                <input type="url" id="vlm_video_url" name="vlm_video_url" value="<?php echo esc_url($video_url); ?>" class="widefat">
            </p>
            <p>
                <label for="vlm_subject"><?php _e('Subject', 'video-lessons-manager'); ?></label>
                <?php
                wp_dropdown_posts(array(
                    'post_type' => 'subject',
                    'selected' => $subject,
                    'name' => 'vlm_subject',
                    'show_option_none' => __('Select Subject', 'video-lessons-manager'),
                    'class' => 'widefat',
                ));
                ?>
            </p>
            <p>
                <label for="vlm_level"><?php _e('Level', 'video-lessons-manager'); ?></label>
                <?php
                wp_dropdown_posts(array(
                    'post_type' => 'level',
                    'selected' => $level,
                    'name' => 'vlm_level',
                    'show_option_none' => __('Select Level', 'video-lessons-manager'),
                    'class' => 'widefat',
                ));
                ?>
            </p>
            <p>
                <label for="vlm_class"><?php _e('Class', 'video-lessons-manager'); ?></label>
                <?php
                wp_dropdown_posts(array(
                    'post_type' => 'class',
                    'selected' => $class,
                    'name' => 'vlm_class',
                    'show_option_none' => __('Select Class', 'video-lessons-manager'),
                    'class' => 'widefat',
                ));
                ?>
            </p>
        </div>
        <?php
    }

    public static function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (isset($_POST['vlm_save_settings']) && check_admin_referer('vlm_settings_nonce')) {
            // Save settings
            update_option('vlm_bunnynet_api_key', sanitize_text_field($_POST['vlm_bunnynet_api_key']));
            update_option('vlm_library_id', sanitize_text_field($_POST['vlm_library_id']));
            echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'video-lessons-manager') . '</p></div>';
        }

        $api_key = get_option('vlm_bunnynet_api_key');
        $library_id = get_option('vlm_library_id');
        ?>
        <div class="wrap">
            <h1><?php _e('Video Lessons Manager Settings', 'video-lessons-manager'); ?></h1>
            <form method="post" action="">
                <?php wp_nonce_field('vlm_settings_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="vlm_bunnynet_api_key"><?php _e('Bunny.net API Key', 'video-lessons-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="vlm_bunnynet_api_key" name="vlm_bunnynet_api_key" value="<?php echo esc_attr($api_key); ?>" class="regular-text">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="vlm_library_id"><?php _e('Library ID', 'video-lessons-manager'); ?></label>
                        </th>
                        <td>
                            <input type="text" id="vlm_library_id" name="vlm_library_id" value="<?php echo esc_attr($library_id); ?>" class="regular-text">
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <input type="submit" name="vlm_save_settings" class="button button-primary" value="<?php _e('Save Settings', 'video-lessons-manager'); ?>">
                </p>
            </form>
        </div>
        <?php
    }
}
