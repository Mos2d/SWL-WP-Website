/**
 * Video Filter AJAX Functionality
 * Handles filtering videos by category
 */
(function($) {
    'use strict';

    // Cache DOM elements
    let $videoGrid = null;
    let $loadingSpinner = null;
    let $filterForm = null;
    let $categorySelect = null;
    let $paginationContainer = null;
    
    // Current filter state
    let currentFilters = {
        category: '',
        paged: 1
    };

    /**
     * Initialize the video filter functionality
     */
    function init() {
        // Cache DOM elements
        $videoGrid = $('.video-grid');
        $loadingSpinner = $('.video-loading-spinner');
        $filterForm = $('.video-filter-form');
        $categorySelect = $('.video-category-filter');
        $paginationContainer = $('.video-pagination');

        if ($videoGrid.length === 0) {
            return; // Exit if not on video archive page
        }

        // Bind events
        bindEvents();
        
        // Initialize filters from URL parameters
        initializeFiltersFromURL();
    }

    /**
     * Bind event handlers
     */
    function bindEvents() {
        // Category filter change
        if ($categorySelect.length) {
            $categorySelect.on('change', function() {
                currentFilters.category = $(this).val();
                currentFilters.paged = 1; // Reset to first page
                filterVideos();
            });
        }

        // Pagination clicks
        $(document).on('click', '.video-pagination a', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            if (page) {
                currentFilters.paged = page;
                filterVideos();
            }
        });

        // Clear filters button
        $(document).on('click', '.clear-filters-btn', function(e) {
            e.preventDefault();
            clearFilters();
        });
    }

    /**
     * Initialize filters from URL parameters
     */
    function initializeFiltersFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        
        if (urlParams.has('category')) {
            currentFilters.category = urlParams.get('category');
            $categorySelect.val(currentFilters.category);
        }
    }

    /**
     * Filter videos via AJAX
     */
    function filterVideos() {
        if (!$videoGrid.length) return;

        // Show loading spinner
        showLoading();

        // Update URL without page reload
        updateURL();

        // Prepare AJAX data
        const ajaxData = {
            action: 'sarah_loz_filter_videos',
            nonce: sarah_loz_video_filter.nonce,
            category: currentFilters.category,
            paged: currentFilters.paged
        };

        // Make AJAX request
        $.ajax({
            url: sarah_loz_video_filter.ajax_url,
            type: 'POST',
            data: ajaxData,
            success: function(response) {
                if (response.success) {
                    updateVideoGrid(response.data.html);
                    updatePagination(response.data);
                    updateResultsCount(response.data.found_posts);
                } else {
                    showError('حدث خطأ أثناء تحميل الفيديوهات');
                }
            },
            error: function() {
                showError('حدث خطأ في الاتصال');
            },
            complete: function() {
                hideLoading();
            }
        });
    }

    /**
     * Update the video grid with new content
     */
    function updateVideoGrid(html) {
        if ($videoGrid.length) {
            $videoGrid.fadeOut(300, function() {
                $(this).html(html).fadeIn(300);
            });
        }
    }

    /**
     * Update pagination
     */
    function updatePagination(data) {
        if (!$paginationContainer.length) return;

        let paginationHTML = '';
        
        if (data.max_pages > 1) {
            paginationHTML = '<div class="flex justify-center space-x-2 rtl:space-x-reverse mt-8">';
            
            // Previous button
            if (data.current_page > 1) {
                paginationHTML += `<a href="#" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition" data-page="${data.current_page - 1}">
                    <i class="fas fa-arrow-right ml-2"></i> السابق
                </a>`;
            }
            
            // Page numbers
            for (let i = 1; i <= data.max_pages; i++) {
                if (i === data.current_page) {
                    paginationHTML += `<span class="px-4 py-2 bg-dark text-white rounded-lg">${i}</span>`;
                } else {
                    paginationHTML += `<a href="#" class="px-4 py-2 bg-gray-200 text-dark rounded-lg hover:bg-gray-300 transition" data-page="${i}">${i}</a>`;
                }
            }
            
            // Next button
            if (data.current_page < data.max_pages) {
                paginationHTML += `<a href="#" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition" data-page="${data.current_page + 1}">
                    التالي <i class="fas fa-arrow-left mr-2"></i>
                </a>`;
            }
            
            paginationHTML += '</div>';
        }
        
        $paginationContainer.html(paginationHTML);
    }

    /**
     * Update results count
     */
    function updateResultsCount(count) {
        const $resultsCount = $('.video-results-count');
        if ($resultsCount.length) {
            $resultsCount.text(`${count} فيديو`);
        }
    }

    /**
     * Show loading spinner
     */
    function showLoading() {
        if ($loadingSpinner.length) {
            $loadingSpinner.show();
        } else {
            // Create loading spinner if it doesn't exist
            const spinnerHTML = `
                <div class="video-loading-spinner fixed inset-0 bg-white/80 flex items-center justify-center z-50">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
                        <p class="text-lg text-gray-600">${sarah_loz_video_filter.loading_text}</p>
                    </div>
                </div>
            `;
            $('body').append(spinnerHTML);
            $loadingSpinner = $('.video-loading-spinner');
        }
    }

    /**
     * Hide loading spinner
     */
    function hideLoading() {
        if ($loadingSpinner.length) {
            $loadingSpinner.fadeOut(300);
        }
    }

    /**
     * Show error message
     */
    function showError(message) {
        const errorHTML = `
            <div class="col-span-full text-center p-8 bg-red-50 rounded-2xl border border-red-200">
                <div class="text-4xl mb-4">⚠️</div>
                <h3 class="text-xl font-bold text-red-600 mb-2">خطأ</h3>
                <p class="text-red-500">${message}</p>
            </div>
        `;
        
        if ($videoGrid.length) {
            $videoGrid.html(errorHTML);
        }
    }

    /**
     * Clear all filters
     */
    function clearFilters() {
        currentFilters = {
            category: '',
            paged: 1
        };
        
        // Reset form elements
        if ($categorySelect.length) {
            $categorySelect.val('');
        }
        
        // Update URL
        updateURL();
        
        // Reload videos
        filterVideos();
    }

    /**
     * Update URL with current filters
     */
    function updateURL() {
        const url = new URL(window.location);
        
        // Clear existing parameters
        url.searchParams.delete('category');
        url.searchParams.delete('paged');
        
        // Add current filters
        if (currentFilters.category) {
            url.searchParams.set('category', currentFilters.category);
        }
        if (currentFilters.paged > 1) {
            url.searchParams.set('paged', currentFilters.paged);
        }
        
        // Update URL without page reload
        window.history.pushState({}, '', url);
    }

    // Initialize when document is ready
    $(document).ready(function() {
        init();
    });

    // Handle browser back/forward buttons
    $(window).on('popstate', function() {
        initializeFiltersFromURL();
        filterVideos();
    });

})(jQuery);
