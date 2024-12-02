jQuery(document).ready(function($) {
    // Toggle between grid and list view
    $('.vlm-view-toggle').on('click', function(e) {
        e.preventDefault();
        const view = $(this).data('view');
        $('.vlm-video-container').attr('data-view', view);
        
        // Save preference
        $.post(vlmSettings.ajaxurl, {
            action: 'vlm_save_view_preference',
            view: view,
            nonce: vlmSettings.nonce
        });
    });

    // Filter videos by category, subject, level, or class
    $('.vlm-filter select').on('change', function() {
        const filters = {};
        $('.vlm-filter select').each(function() {
            const key = $(this).attr('name');
            const value = $(this).val();
            if (value) {
                filters[key] = value;
            }
        });

        // Update video list via AJAX
        $.post(vlmSettings.ajaxurl, {
            action: 'vlm_filter_videos',
            filters: filters,
            nonce: vlmSettings.nonce
        }, function(response) {
            if (response.success) {
                $('.vlm-video-container').html(response.data.html);
            }
        });
    });

    // Initialize video player
    function initBunnyPlayer() {
        $('.vlm-video-player').each(function() {
            const videoId = $(this).data('video-id');
            const libraryId = $(this).data('library-id');
            
            // Initialize Bunny.net player
            if (videoId && libraryId) {
                const player = BunnyStream.createPlayer({
                    videoId: videoId,
                    libraryId: libraryId
                });
            }
        });
    }

    initBunnyPlayer();
});
