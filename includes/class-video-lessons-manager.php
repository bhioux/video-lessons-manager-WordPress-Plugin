<?php
class Video_Lessons_Manager {
    private $post_types;
    
    public function init() {
        // Initialize components
        $this->init_hooks();
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Add meta boxes
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
    }

    private function init_hooks() {
        // Register post types and taxonomies early
        add_action('init', array($this, 'register_post_types'), 0);
        add_action('init', array($this, 'register_taxonomies'), 0);
        
        // Admin hooks
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('save_post', array($this, 'save_video_lesson_meta'));
        
        // Template filters
        add_filter('single_template', array($this, 'load_single_template'));
        add_filter('archive_template', array($this, 'load_archive_template'));
        
        // AJAX handlers
        add_action('wp_ajax_vlm_filter_videos', array($this, 'ajax_filter_videos'));
        add_action('wp_ajax_nopriv_vlm_filter_videos', array($this, 'ajax_filter_videos'));
    }

    public function register_post_types() {
        // Register Class Post Type
        $class_labels = array(
            'name'                  => __('Classes', 'video-lessons-manager'),
            'singular_name'         => __('Class', 'video-lessons-manager'),
            'menu_name'            => __('Classes', 'video-lessons-manager'),
            'add_new'              => __('Add New', 'video-lessons-manager'),
            'add_new_item'         => __('Add New Class', 'video-lessons-manager'),
            'edit_item'            => __('Edit Class', 'video-lessons-manager'),
            'new_item'             => __('New Class', 'video-lessons-manager'),
            'view_item'            => __('View Class', 'video-lessons-manager'),
            'search_items'         => __('Search Classes', 'video-lessons-manager'),
            'not_found'            => __('No classes found', 'video-lessons-manager'),
            'not_found_in_trash'   => __('No classes found in trash', 'video-lessons-manager'),
        );

        $class_args = array(
            'labels'              => $class_labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'class'),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => null,
            'supports'            => array('title', 'editor', 'thumbnail'),
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-welcome-learn-more',
        );

        register_post_type('class', $class_args);

        // Register Level Post Type
        $level_labels = array(
            'name'                  => __('Levels', 'video-lessons-manager'),
            'singular_name'         => __('Level', 'video-lessons-manager'),
            'menu_name'            => __('Levels', 'video-lessons-manager'),
            'add_new'              => __('Add New', 'video-lessons-manager'),
            'add_new_item'         => __('Add New Level', 'video-lessons-manager'),
            'edit_item'            => __('Edit Level', 'video-lessons-manager'),
            'new_item'             => __('New Level', 'video-lessons-manager'),
            'view_item'            => __('View Level', 'video-lessons-manager'),
            'search_items'         => __('Search Levels', 'video-lessons-manager'),
            'not_found'            => __('No levels found', 'video-lessons-manager'),
            'not_found_in_trash'   => __('No levels found in trash', 'video-lessons-manager'),
        );

        $level_args = array(
            'labels'              => $level_labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'level'),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => null,
            'supports'            => array('title', 'editor', 'thumbnail'),
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-chart-bar',
        );

        register_post_type('level', $level_args);

        // Register Subject Post Type
        $subject_labels = array(
            'name'                  => __('Subjects', 'video-lessons-manager'),
            'singular_name'         => __('Subject', 'video-lessons-manager'),
            'menu_name'            => __('Subjects', 'video-lessons-manager'),
            'add_new'              => __('Add New', 'video-lessons-manager'),
            'add_new_item'         => __('Add New Subject', 'video-lessons-manager'),
            'edit_item'            => __('Edit Subject', 'video-lessons-manager'),
            'new_item'             => __('New Subject', 'video-lessons-manager'),
            'view_item'            => __('View Subject', 'video-lessons-manager'),
            'search_items'         => __('Search Subjects', 'video-lessons-manager'),
            'not_found'            => __('No subjects found', 'video-lessons-manager'),
            'not_found_in_trash'   => __('No subjects found in trash', 'video-lessons-manager'),
        );

        $subject_args = array(
            'labels'              => $subject_labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'subject'),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => null,
            'supports'            => array('title', 'editor', 'thumbnail'),
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-book',
        );

        register_post_type('subject', $subject_args);

        // Register Video Lesson Post Type
        $video_lesson_labels = array(
            'name'                  => __('Video Lessons', 'video-lessons-manager'),
            'singular_name'         => __('Video Lesson', 'video-lessons-manager'),
            'menu_name'            => __('Video Lessons', 'video-lessons-manager'),
            'add_new'              => __('Add New', 'video-lessons-manager'),
            'add_new_item'         => __('Add New Video Lesson', 'video-lessons-manager'),
            'edit_item'            => __('Edit Video Lesson', 'video-lessons-manager'),
            'new_item'             => __('New Video Lesson', 'video-lessons-manager'),
            'view_item'            => __('View Video Lesson', 'video-lessons-manager'),
            'search_items'         => __('Search Video Lessons', 'video-lessons-manager'),
            'not_found'            => __('No video lessons found', 'video-lessons-manager'),
            'not_found_in_trash'   => __('No video lessons found in trash', 'video-lessons-manager'),
        );

        $video_lesson_args = array(
            'labels'              => $video_lesson_labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'query_var'           => true,
            'rewrite'             => array('slug' => 'video-lesson'),
            'capability_type'     => 'post',
            'has_archive'         => true,
            'hierarchical'        => false,
            'menu_position'       => null,
            'supports'            => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-video-alt3',
        );

        register_post_type('video_lesson', $video_lesson_args);

        // Flush rewrite rules if needed
        if (get_option('vlm_flush_rewrite_rules')) {
            flush_rewrite_rules();
            delete_option('vlm_flush_rewrite_rules');
        }
    }

    public function register_taxonomies() {
        // Register Categories
        $category_labels = array(
            'name'              => __('Lesson Categories', 'video-lessons-manager'),
            'singular_name'     => __('Category', 'video-lessons-manager'),
            'search_items'      => __('Search Categories', 'video-lessons-manager'),
            'all_items'         => __('All Categories', 'video-lessons-manager'),
            'parent_item'       => __('Parent Category', 'video-lessons-manager'),
            'parent_item_colon' => __('Parent Category:', 'video-lessons-manager'),
            'edit_item'         => __('Edit Category', 'video-lessons-manager'),
            'update_item'       => __('Update Category', 'video-lessons-manager'),
            'add_new_item'      => __('Add New Category', 'video-lessons-manager'),
            'new_item_name'     => __('New Category Name', 'video-lessons-manager'),
            'menu_name'         => __('Categories', 'video-lessons-manager'),
        );

        $category_args = array(
            'hierarchical'      => true,
            'labels'            => $category_labels,
            'show_ui'          => true,
            'show_admin_column' => true,
            'query_var'        => true,
            'rewrite'          => array('slug' => 'lesson-category'),
            'show_in_rest'     => true,
        );

        register_taxonomy('lesson_category', array('video_lesson'), $category_args);

        // Register Tags
        $tag_labels = array(
            'name'              => __('Lesson Tags', 'video-lessons-manager'),
            'singular_name'     => __('Tag', 'video-lessons-manager'),
            'search_items'      => __('Search Tags', 'video-lessons-manager'),
            'all_items'         => __('All Tags', 'video-lessons-manager'),
            'edit_item'         => __('Edit Tag', 'video-lessons-manager'),
            'update_item'       => __('Update Tag', 'video-lessons-manager'),
            'add_new_item'      => __('Add New Tag', 'video-lessons-manager'),
            'new_item_name'     => __('New Tag Name', 'video-lessons-manager'),
            'menu_name'         => __('Tags', 'video-lessons-manager'),
        );

        $tag_args = array(
            'hierarchical'      => false,
            'labels'            => $tag_labels,
            'show_ui'          => true,
            'show_admin_column' => true,
            'query_var'        => true,
            'rewrite'          => array('slug' => 'lesson-tag'),
            'show_in_rest'     => true,
        );

        register_taxonomy('lesson_tag', array('video_lesson'), $tag_args);
    }

    // ... rest of the class methods remain the same ...
}
