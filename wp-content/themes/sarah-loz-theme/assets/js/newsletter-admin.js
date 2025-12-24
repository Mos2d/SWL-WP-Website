/**
 * Newsletter Admin JavaScript
 * 
 * Handles AJAX requests for the newsletter administration
 */

jQuery(document).ready(function($) {
    
    // Newsletter sending form
    $('#swl-newsletter-form').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $submitButton = $('#swl-send-newsletter');
        const $spinner = $('#swl-newsletter-spinner');
        const $results = $('#swl-newsletter-results');
        
        // Get data
        const formData = new FormData(this);
        
        // Validate form
        if (!formData.get('subject')) {
            $results.html('<div class="swl-error">يرجى إدخال عنوان للنشرة البريدية</div>');
            return;
        }
        
        // Get content from TinyMCE if available
        if (typeof tinyMCE !== 'undefined' && tinyMCE.get('newsletter-content')) {
            formData.set('content', tinyMCE.get('newsletter-content').getContent());
        }
        
        if (!formData.get('content')) {
            $results.html('<div class="swl-error">يرجى إدخال محتوى للنشرة البريدية</div>');
            return;
        }
        
        // Check if we're sending a test and if test email is provided
        if (formData.get('send_type') === 'test' && !formData.get('test_email')) {
            $results.html('<div class="swl-error">يرجى إدخال بريد إلكتروني للاختبار</div>');
            return;
        }
        
        // Disable form and show spinner
        $submitButton.prop('disabled', true);
        $spinner.addClass('is-active');
        $results.html('');
        
        // Send AJAX request
        $.ajax({
            url: swl_newsletter.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $results.html('<div class="swl-success">' + response.data.message + '</div>');
                    
                    // Clear form if it was a real send (not test)
                    if (formData.get('send_type') === 'all') {
                        $form.find('input[name="subject"]').val('');
                        
                        // Clear TinyMCE if available
                        if (typeof tinyMCE !== 'undefined' && tinyMCE.get('newsletter-content')) {
                            tinyMCE.get('newsletter-content').setContent('');
                        } else {
                            $form.find('textarea[name="content"]').val('');
                        }
                    }
                } else {
                    $results.html('<div class="swl-error">' + response.data.message + '</div>');
                }
            },
            error: function() {
                $results.html('<div class="swl-error">حدث خطأ أثناء إرسال الطلب. يرجى المحاولة مرة أخرى.</div>');
            },
            complete: function() {
                $submitButton.prop('disabled', false);
                $spinner.removeClass('is-active');
            }
        });
    });
    
    // Toggle test email field visibility
    $('input[name="send_type"]').on('change', function() {
        const isTest = $('input[name="send_type"]:checked').val() === 'test';
        $('input[name="test_email"]').prop('disabled', !isTest).toggleClass('disabled', !isTest);
    });
    
    // Initialize the toggle state
    $('input[name="test_email"]').prop('disabled', $('input[name="send_type"]:checked').val() !== 'test');
}); 