/**
 * Sarah Loz Practices JavaScript
 */
(function($) {
    'use strict';

    // Variables to track touch state
    let touchStartX = 0;
    let touchStartY = 0;
    let isDragging = false;
    let currentDragItem = null;

    // Initialize when document is ready
    $(document).ready(function() {
        initPractices();
        handleMobileDevices();
    });

    /**
     * Handle mobile device specific behaviors
     */
    function handleMobileDevices() {
        // Check if device is mobile
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        
        if (isMobile) {
            // Check orientation and add rotation notice if needed
            checkOrientation();
            
            // Listen for orientation changes
            $(window).on('resize orientationchange', function() {
                checkOrientation();
            });
            
            // Enhance touch behavior for matching practices
            enhanceTouchForMatching();
            
            // Add class to body for CSS targeting
            $('body').addClass('is-touch-device');
        }
    }
    
    /**
     * Check device orientation and show notice if needed
     */
    function checkOrientation() {
        const isPortrait = window.innerHeight > window.innerWidth;
        
        // Only show rotation notice for matching practices in portrait mode
        if (isPortrait && $('.matching-practice').length > 0) {
            // Remove any existing notices first
            $('.rotation-notice').remove();
            
            // Add rotation notice
            const noticeHtml = `
                <div class="rotation-notice bg-amber-100 text-amber-800 p-4 rounded-lg mb-4 text-center">
                    <i class="fas fa-mobile-alt mr-2"></i>
                    <span>${sarahLozPractices.i18n.rotateDevice || 'Please rotate your device to landscape mode for a better experience with matching practices.'}</span>
                </div>
            `;
            
            $('.sarah-loz-practice').prepend(noticeHtml);
        } else {
            // Remove notice if in landscape
            $('.rotation-notice').remove();
        }
    }
    
    /**
     * Enhance touch experience for matching practices
     */
    function enhanceTouchForMatching() {
        const $practices = $('.matching-practice');
        
        if ($practices.length) {
            // Try to load jQuery UI Touch Punch dynamically if not available
            if (typeof $.fn.draggable.prototype._touchStart !== 'function') {
                loadTouchPunch();
            }
            
            // Improve visual feedback for touch
            $practices.find('.right-items .matching-item').each(function() {
                const $item = $(this);
                
                // Add touch-specific styles
                $item.addClass('touch-enabled');
                
                // Add visual feedback
                $item.on('touchstart', function() {
                    $(this).addClass('touch-active');
                }).on('touchend touchcancel', function() {
                    $(this).removeClass('touch-active');
                });
            });
            
            // Make the drop targets larger on touch devices
            $practices.find('.left-items .matching-item').each(function() {
                const $dropZone = $(this);
                $dropZone.addClass('touch-drop-target');
            });
            
            // Add custom touch handling for better responsiveness
            implementCustomTouchHandling($practices);
        }
    }
    
    /**
     * Try to load jQuery UI Touch Punch dynamically
     */
    function loadTouchPunch() {
        // Check if it's already being loaded
        if ($('script[src*="jquery.ui.touch-punch"]').length) {
            return;
        }
        
        console.log('Loading jQuery UI Touch Punch for better touch support');
        
        // Create script element
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js';
        script.async = true;
        
        // Add to document
        document.head.appendChild(script);
        
        // Add fallback if script fails to load
        script.onerror = function() {
            console.warn('Failed to load Touch Punch, using fallback touch handling');
            implementCustomTouchHandling($('.matching-practice'));
        };
    }
    
    /**
     * Implement custom touch handling for matching practices
     */
    function implementCustomTouchHandling($practices) {
        const dragItems = $practices.find('.right-items .matching-item');
        const dropTargets = $practices.find('.left-items .matching-item');
        
        // Remove standard draggable if it might conflict
        dragItems.each(function() {
            if ($(this).data('ui-draggable')) {
                try {
                    $(this).draggable('destroy');
                } catch (e) {
                    console.warn('Error destroying draggable:', e);
                }
            }
        });
        
        // Add custom touch handling
        dragItems.each(function() {
            const $item = $(this);
            
            $item.on('touchstart', function(e) {
                if ($(this).hasClass('ui-draggable-disabled')) return;
                
                const touch = e.originalEvent.touches[0];
                touchStartX = touch.clientX;
                touchStartY = touch.clientY;
                currentDragItem = $(this);
                
                // Create visual clone for dragging
                const $clone = $(this).clone().addClass('touch-drag-clone');
                $clone.css({
                    position: 'fixed',
                    top: touchStartY - $(this).height() / 2 + 'px',
                    left: touchStartX - $(this).width() / 2 + 'px',
                    width: $(this).width() + 'px',
                    height: $(this).height() + 'px',
                    zIndex: 9999,
                    opacity: 0.8,
                    pointerEvents: 'none'
                });
                
                // Add clone to body
                $('body').append($clone);
                
                // Mark as dragging
                isDragging = true;
                $(this).addClass('being-touch-dragged');
                
                // Prevent scrolling
                e.preventDefault();
            });
        });
        
        // Handle touch move for dragging
        $(document).on('touchmove', function(e) {
            if (!isDragging || !currentDragItem) return;
            
            const touch = e.originalEvent.touches[0];
            const $clone = $('.touch-drag-clone');
            
            if ($clone.length) {
                // Move the clone
                $clone.css({
                    top: touch.clientY - $clone.height() / 2 + 'px',
                    left: touch.clientX - $clone.width() / 2 + 'px'
                });
                
                // Check if over a drop target
                dropTargets.each(function() {
                    const $target = $(this);
                    if ($target.data('droppable-disabled')) return;
                    
                    const rect = this.getBoundingClientRect();
                    
                    // Check if touch point is over this target
                    if (touch.clientX >= rect.left && touch.clientX <= rect.right &&
                        touch.clientY >= rect.top && touch.clientY <= rect.bottom) {
                        
                        // Add hover class
                        $target.addClass('drop-hover');
                    } else {
                        $target.removeClass('drop-hover');
                    }
                });
            }
            
            // Prevent scrolling
            e.preventDefault();
        });
        
        // Handle touch end
        $(document).on('touchend touchcancel', function(e) {
            if (!isDragging || !currentDragItem) return;
            
            // Find which drop target we're over (if any)
            const $clone = $('.touch-drag-clone');
            let dropped = false;
            
            if ($clone.length) {
                const cloneRect = $clone[0].getBoundingClientRect();
                const cloneCenterX = cloneRect.left + cloneRect.width / 2;
                const cloneCenterY = cloneRect.top + cloneRect.height / 2;
                
                dropTargets.each(function() {
                    if (dropped) return;
                    const $target = $(this);
                    if ($target.data('droppable-disabled')) return;
                    
                    const rect = this.getBoundingClientRect();
                    
                    // Check if clone center is over this target
                    if (cloneCenterX >= rect.left && cloneCenterX <= rect.right &&
                        cloneCenterY >= rect.top && cloneCenterY <= rect.bottom) {
                        
                        // Trigger drop
                        handleCustomDrop($target, currentDragItem);
                        dropped = true;
                    }
                });
                
                // Remove clone
                $clone.remove();
            }
            
            // Reset drag state
            isDragging = false;
            currentDragItem.removeClass('being-touch-dragged');
            currentDragItem = null;
            
            // Remove hover classes
            dropTargets.removeClass('drop-hover');
        });
    }
    
    /**
     * Handle custom drop for touch devices
     */
    function handleCustomDrop($dropZone, $draggedItem) {
        // Store the match
        $dropZone.data('matched-with', $draggedItem.data('correct-match'));
        
        // Visual feedback
        $dropZone.addClass('has-match');
        $dropZone.find('.item-match-status').html(
            '<div class="mt-2 p-2 bg-blue-100 text-blue-800 rounded text-center">' + 
            sarahLozPractices.i18n.submit + 
            '</div>'
        );
        
        // Disable further drops on this item
        $dropZone.data('droppable-disabled', true);
        if ($dropZone.data('ui-droppable')) {
            try {
                $dropZone.droppable('option', 'disabled', true);
            } catch (e) {
                console.warn('Error disabling droppable:', e);
            }
        }
        
        // Hide the dragged item
        $draggedItem.css('opacity', '0.5');
        $draggedItem.data('draggable-disabled', true);
        if ($draggedItem.data('ui-draggable')) {
            try {
                $draggedItem.draggable('option', 'disabled', true);
            } catch (e) {
                console.warn('Error disabling draggable:', e);
            }
        }
    }

    /**
     * Initialize practices functionality
     */
    function initPractices() {
        // Initialize multiple choice practices
        $('.multiple-choice-practice').each(function() {
            initMultipleChoicePractice($(this));
        });

        // Initialize matching practices
        $('.matching-practice').each(function() {
            initMatchingPractice($(this));
        });
        
        // Initialize audio controls behavior
        initAudioControls();
    }
    
    /**
     * Initialize audio behavior for practices
     */
    function initAudioControls() {
        // When an audio element plays, pause all other audio elements
        $('audio').on('play', function() {
            pauseAllOtherAudio(this);
        });
        
        // Custom play buttons for audio elements
        $('.answer-item').on('click', function() {
            // Pause all audio when selecting a new answer
            pauseAllAudio();
        });
    }
    
    /**
     * Pause all audio elements except the one provided
     */
    function pauseAllOtherAudio(currentAudio) {
        $('audio').each(function() {
            if (this !== currentAudio) {
                this.pause();
            }
        });
    }
    
    /**
     * Pause all audio elements
     */
    function pauseAllAudio() {
        $('audio').each(function() {
            this.pause();
        });
    }

    /**
     * Initialize multiple choice practice
     */
    function initMultipleChoicePractice($practice) {
        // Form submission
        $practice.on('submit', function(e) {
            e.preventDefault();
            // Pause all audio before submission
            pauseAllAudio();
            submitMultipleChoicePractice($practice);
        });

        // Try again button
        $practice.find('.try-again').on('click', function() {
            resetPractice($practice);
        });
        
        // Setup audio playback in answer labels
        $practice.find('label').on('click', function() {
            const $audio = $(this).find('audio');
            if ($audio.length) {
                // Pause all other audio first
                pauseAllOtherAudio($audio[0]);
            }
        });
    }

    /**
     * Initialize matching practice
     */
    function initMatchingPractice($practice) {
        // Make items draggable and droppable
        setupDragAndDrop($practice);

        // Form submission
        $practice.on('submit', function(e) {
            e.preventDefault();
            // Pause all audio before submission
            pauseAllAudio();
            submitMatchingPractice($practice);
        });

        // Try again button
        $practice.find('.try-again').on('click', function() {
            resetPractice($practice);
        });
        
        // Setup audio playback in matching items
        $practice.find('.matching-item').on('click', function() {
            const $audio = $(this).find('audio');
            if ($audio.length) {
                // Pause all other audio first
                pauseAllOtherAudio($audio[0]);
            }
        });
    }

    /**
     * Setup drag and drop for matching practice
     */
    function setupDragAndDrop($practice) {
        const $leftItems = $practice.find('.left-items .matching-item');
        const $rightItems = $practice.find('.right-items .matching-item');

        // Make right items draggable
        $rightItems.draggable({
            revert: 'invalid',
            containment: $practice,
            cursor: 'move',
            helper: 'clone',
            zIndex: 100,
            // Enhanced touch support
            distance: 5,  // Reduced from 10 to make it more responsive
            delay: 50,    // Reduced from 100 to make it more responsive
            scroll: false,
            start: function(event, ui) {
                $(this).addClass('being-dragged');
                // Ensure scrolling is disabled during drag on touch devices
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    $('body').css('overflow', 'hidden');
                }
            },
            stop: function(event, ui) {
                $(this).removeClass('being-dragged');
                // Re-enable scrolling
                if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                    $('body').css('overflow', '');
                }
            }
        });

        // Make left items droppable
        $leftItems.droppable({
            accept: '.right-items .matching-item',
            hoverClass: 'drop-hover',
            tolerance: 'intersect', // Changed from 'pointer' to 'intersect' for better touch experience
            drop: function(event, ui) {
                const $dropZone = $(this);
                const $draggedItem = ui.draggable;

                // Store the match
                $dropZone.data('matched-with', $draggedItem.data('correct-match'));
                
                // Visual feedback
                $dropZone.addClass('has-match');
                $dropZone.find('.item-match-status').html(
                    '<div class="mt-2 p-2 bg-blue-100 text-blue-800 rounded text-center">' + 
                    sarahLozPractices.i18n.submit + 
                    '</div>'
                );
                
                // Disable further drops on this item
                $dropZone.droppable('option', 'disabled', true);
                
                // Hide the dragged item
                $draggedItem.draggable('option', 'disabled', true);
                $draggedItem.css('opacity', '0.5');
            }
        });
    }

    /**
     * Submit multiple choice practice
     */
    function submitMultipleChoicePractice($practice) {
        const $practiceContainer = $practice.closest('.sarah-loz-practice');
        const practiceId = $practiceContainer.data('practice-id');
        
        // Collect answers
        const answers = {};
        $practice.find('.practice-question').each(function() {
            const $question = $(this);
            const questionIndex = $question.data('question-index');
            const questionNumber = questionIndex + 1;
            const $selectedAnswer = $question.find('input[name="q' + questionNumber + '"]:checked');
            
            if ($selectedAnswer.length) {
                answers['q' + questionNumber] = $selectedAnswer.val();
            }
        });
        
        // Show loading state
        $practice.find('.submit-practice').prop('disabled', true).text(sarahLozPractices.i18n.loading);
        
        // Submit via AJAX
        $.ajax({
            url: sarahLozPractices.ajaxUrl,
            type: 'POST',
            data: {
                action: 'check_practice_answers',
                practice_id: practiceId,
                nonce: sarahLozPractices.nonce,
                answers: answers
            },
            success: function(response) {
                if (response.success) {
                    displayMultipleChoiceResults($practice, response.data);
                } else {
                    // Handle error
                    alert(response.data.message || 'An error occurred.');
                    $practice.find('.submit-practice').prop('disabled', false).text(sarahLozPractices.i18n.submit);
                }
            },
            error: function() {
                // Handle error
                alert('A server error occurred. Please try again.');
                $practice.find('.submit-practice').prop('disabled', false).text(sarahLozPractices.i18n.submit);
            }
        });
    }

    /**
     * Submit matching practice
     */
    function submitMatchingPractice($practice) {
        const $practiceContainer = $practice.closest('.sarah-loz-practice');
        const practiceId = $practiceContainer.data('practice-id');
        
        // Collect matches
        const matches = {};
        $practice.find('.left-items .matching-item').each(function() {
            const $item = $(this);
            const itemIndex = $item.data('item-index');
            const matchedWith = $item.data('matched-with');
            
            if (matchedWith !== undefined) {
                matches[itemIndex] = matchedWith;
            }
        });
        
        // Check if all items are matched
        const totalLeftItems = $practice.find('.left-items .matching-item').length;
        if (Object.keys(matches).length < totalLeftItems) {
            alert(__('يرجى توصيل جميع العناصر قبل التسليم.', 'sarah-loz'));
            return;
        }
        
        // Show loading state
        $practice.find('.submit-practice').prop('disabled', true).text(sarahLozPractices.i18n.loading);
        
        // Submit via AJAX
        $.ajax({
            url: sarahLozPractices.ajaxUrl,
            type: 'POST',
            data: {
                action: 'check_practice_answers',
                practice_id: practiceId,
                nonce: sarahLozPractices.nonce,
                matches: matches
            },
            success: function(response) {
                if (response.success) {
                    displayMatchingResults($practice, response.data);
                } else {
                    // Handle error
                    alert(response.data.message || 'An error occurred.');
                    $practice.find('.submit-practice').prop('disabled', false).text(sarahLozPractices.i18n.submit);
                }
            },
            error: function() {
                // Handle error
                alert('A server error occurred. Please try again.');
                $practice.find('.submit-practice').prop('disabled', false).text(sarahLozPractices.i18n.submit);
            }
        });
    }

    /**
     * Display multiple choice results
     */
    function displayMultipleChoiceResults($practice, data) {
        // Show question feedback
        for (const questionKey in data.results) {
            const result = data.results[questionKey];
            const questionNumber = questionKey.substring(1);
            const $question = $practice.find('[data-question-index="' + (questionNumber - 1) + '"]');
            
            const $feedback = $question.find('.question-feedback');
            $feedback.removeClass('hidden').addClass(result.correct ? 'text-green-600' : 'text-red-600');
            $feedback.html('<i class="fas fa-' + (result.correct ? 'check' : 'times') + '-circle"></i> ' + result.message);
            
            // Highlight correct/incorrect answers
            $question.find('input[type="radio"]').each(function() {
                const $radio = $(this);
                const $label = $radio.closest('label');
                
                if ($radio.prop('checked')) {
                    $label.addClass(result.correct ? 'border-green-500 bg-green-50' : 'border-red-500 bg-red-50');
                }
                
                if ($radio.data('correct') === 'true') {
                    $label.addClass('border-green-500');
                }
            });
        }
        
        // Show overall results
        $practice.find('.results-message').text(data.message);
        $practice.find('.results-score').text(
            sarahLozPractices.i18n.score + data.score + ' / ' + data.total + ' (' + data.percentage + '%)'
        );
        
        // Show custom feedback based on score
        let feedbackHtml = '';
        if (data.percentage >= 75) {
            feedbackHtml = '<div class="text-green-600"><i class="fas fa-trophy mr-2"></i>' + 
                           sarahLozPractices.i18n.congratulations + '</div>';
        } else {
            feedbackHtml = '<div class="text-amber-600"><i class="fas fa-sync-alt mr-2"></i>' + 
                           sarahLozPractices.i18n.tryAgain + '</div>';
        }
        $practice.find('.results-feedback').html(feedbackHtml);
        
        // Show results container and hide submit button
        $practice.find('.practice-results').removeClass('hidden');
        $practice.find('.submit-practice').addClass('hidden');
        
        // Disable form inputs
        $practice.find('input').prop('disabled', true);
    }

    /**
     * Display matching results
     */
    function displayMatchingResults($practice, data) {
        // Show item feedback
        for (const itemIndex in data.results) {
            const result = data.results[itemIndex];
            const $item = $practice.find('.left-items .matching-item[data-item-index="' + itemIndex + '"]');
            
            const statusHtml = '<div class="p-2 rounded text-center ' + 
                              (result.correct ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') + '">' +
                              '<i class="fas fa-' + (result.correct ? 'check' : 'times') + '-circle mr-1"></i> ' +
                              result.message +
                              '</div>';
            
            $item.find('.item-match-status').html(statusHtml);
            $item.addClass(result.correct ? 'border-green-500' : 'border-red-500');
        }
        
        // Show overall results
        $practice.find('.results-message').text(data.message);
        $practice.find('.results-score').text(
            sarahLozPractices.i18n.score + data.score + ' / ' + data.total + ' (' + data.percentage + '%)'
        );
        
        // Show custom feedback based on score
        let feedbackHtml = '';
        if (data.percentage >= 75) {
            feedbackHtml = '<div class="text-green-600"><i class="fas fa-trophy mr-2"></i>' + 
                           sarahLozPractices.i18n.congratulations + '</div>';
        } else {
            feedbackHtml = '<div class="text-amber-600"><i class="fas fa-sync-alt mr-2"></i>' + 
                           sarahLozPractices.i18n.tryAgain + '</div>';
        }
        $practice.find('.results-feedback').html(feedbackHtml);
        
        // Show results container and hide submit button
        $practice.find('.practice-results').removeClass('hidden');
        $practice.find('.submit-practice').addClass('hidden');
        
        // Disable draggable items
        $practice.find('.right-items .matching-item').draggable('disable');
    }

    /**
     * Reset practice for retrying
     */
    function resetPractice($practice) {
        if ($practice.hasClass('multiple-choice-practice')) {
            // Reset multiple choice
            $practice.find('input[type="radio"]').prop('checked', false).prop('disabled', false);
            $practice.find('label').removeClass('border-green-500 border-red-500 bg-green-50 bg-red-50');
            $practice.find('.question-feedback').addClass('hidden').empty();
        } else if ($practice.hasClass('matching-practice')) {
            // Reset matching
            $practice.find('.left-items .matching-item').removeClass('has-match border-green-500 border-red-500')
                .data('matched-with', null)
                .find('.item-match-status').empty();
            
            $practice.find('.left-items .matching-item').droppable('option', 'disabled', false);
            
            $practice.find('.right-items .matching-item').css('opacity', '1')
                .draggable('option', 'disabled', false);
        }
        
        // Hide results and show submit button
        $practice.find('.practice-results').addClass('hidden');
        $practice.find('.submit-practice').removeClass('hidden').prop('disabled', false)
            .text(sarahLozPractices.i18n.submit);
    }

})(jQuery); 