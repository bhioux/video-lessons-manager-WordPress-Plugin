<?php
// If uninstall is not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

// Access WordPress database
global $wpdb;

// Delete all plugin options
$options_to_delete = array(
    'vlm_bunnynet_api_key',
    'vlm_library_id',
    'vlm_flush_rewrite_rules',
    'vlm_version'
);

foreach ($options_to_delete as $option) {
    delete_option($option);
    // For multisite compatibility
    delete_site_option($option);
}

// Delete all video lessons and their metadata
$video_posts = get_posts(array(
    'post_type' => 'video_lesson',
    'numberposts' => -1,
    'post_status' => 'any'
));

foreach ($video_posts as $post) {
    wp_delete_post($post->ID, true);
}

// Delete all subjects and their metadata
$subject_posts = get_posts(array(
    'post_type' => 'subject',
    'numberposts' => -1,
    'post_status' => 'any'
));

foreach ($subject_posts as $post) {
    wp_delete_post($post->ID, true);
}

// Delete all levels and their metadata
$level_posts = get_posts(array(
    'post_type' => 'level',
    'numberposts' => -1,
    'post_status' => 'any'
));

foreach ($level_posts as $post) {
    wp_delete_post($post->ID, true);
}

// Delete all classes and their metadata
$class_posts = get_posts(array(
    'post_type' => 'class',
    'numberposts' => -1,
    'post_status' => 'any'
));

foreach ($class_posts as $post) {
    wp_delete_post($post->ID, true);
}

// Delete all terms in custom taxonomies
$taxonomies = array('lesson_category', 'lesson_tag');

foreach ($taxonomies as $taxonomy) {
    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false,
    ));

    foreach ($terms as $term) {
        wp_delete_term($term->term_id, $taxonomy);
    }
}

// Clean up custom post type and taxonomy relationships
$wpdb->query(
    "DELETE FROM {$wpdb->term_relationships}
    WHERE object_id IN (
        SELECT ID FROM {$wpdb->posts}
        WHERE post_type IN ('video_lesson', 'subject', 'level', 'class')
    )"
);

// Delete all post meta for custom post types
$wpdb->query(
    "DELETE FROM {$wpdb->postmeta}
    WHERE post_id IN (
        SELECT ID FROM {$wpdb->posts}
        WHERE post_type IN ('video_lesson', 'subject', 'level', 'class')
    )"
);

// Delete any transients
$wpdb->query(
    "DELETE FROM {$wpdb->options}
    WHERE option_name LIKE '%_transient_vlm_%'
    OR option_name LIKE '%_transient_timeout_vlm_%'"
);

// Clean up any custom capabilities
$roles = wp_roles();
$capabilities = array(
    'edit_video_lesson',
    'read_video_lesson',
    'delete_video_lesson',
    'edit_video_lessons',
    'edit_others_video_lessons',
    'publish_video_lessons',
    'read_private_video_lessons',
    'manage_video_lessons'
);

foreach ($roles->role_objects as $role) {
    foreach ($capabilities as $cap) {
        $role->remove_cap($cap);
    }
}

// Delete any user meta related to the plugin
$wpdb->query(
    "DELETE FROM {$wpdb->usermeta}
    WHERE meta_key LIKE '%_vlm_%'"
);

// Delete any custom database tables if they exist
$tables = array(
    $wpdb->prefix . 'vlm_video_stats',
    $wpdb->prefix . 'vlm_video_progress'
);

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS $table");
}

// Clear any scheduled cron events
wp_clear_scheduled_hook('vlm_daily_maintenance');
wp_clear_scheduled_hook('vlm_weekly_cleanup');

// Delete uploaded files in wp-content/uploads/video-lessons-manager
$upload_dir = wp_upload_dir();
$vlm_upload_dir = $upload_dir['basedir'] . '/video-lessons-manager';

if (is_dir($vlm_upload_dir)) {
    // Recursive function to delete directory and its contents
    function vlm_delete_directory($dir) {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!vlm_delete_directory($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }

        return rmdir($dir);
    }

    vlm_delete_directory($vlm_upload_dir);
}

// Flush rewrite rules after cleanup
flush_rewrite_rules();
