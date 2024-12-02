<?php
/**
 * Plugin Name: Video Lessons Manager
 * Plugin URI: https://yourwebsite.com/video-lessons-manager
 * Description: Manage video lessons with categories, tags, subjects, levels, and classes using Bunny.net streaming.
 * Version: 1.0.0
 * Author: Emman Akinbodewa
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * Text Domain: video-lessons-manager
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('VLM_VERSION', '1.0.0');
define('VLM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VLM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once VLM_PLUGIN_DIR . 'includes/class-video-lessons-manager.php';
require_once VLM_PLUGIN_DIR . 'includes/admin/class-vlm-admin.php';
require_once VLM_PLUGIN_DIR . 'includes/frontend/class-vlm-frontend.php';

// Register activation hook
register_activation_hook(__FILE__, 'vlm_activate');

function vlm_activate() {
    // Set flag to flush rewrite rules
    add_option('vlm_flush_rewrite_rules', true);
    
    // Create default categories and terms if needed
    if (!term_exists('Beginner', 'lesson_category')) {
        wp_insert_term('Beginner', 'lesson_category');
    }
    if (!term_exists('Intermediate', 'lesson_category')) {
        wp_insert_term('Intermediate', 'lesson_category');
    }
    if (!term_exists('Advanced', 'lesson_category')) {
        wp_insert_term('Advanced', 'lesson_category');
    }
}

// Register deactivation hook
register_deactivation_hook(__FILE__, 'vlm_deactivate');

function vlm_deactivate() {
    // Clean up if needed
    flush_rewrite_rules();
}

// Initialize the plugin
function vlm_init() {
    $video_lessons_manager = new Video_Lessons_Manager();
    $video_lessons_manager->init();
}
add_action('plugins_loaded', 'vlm_init');
