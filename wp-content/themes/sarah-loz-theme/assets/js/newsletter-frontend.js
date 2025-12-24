/**
 * Newsletter Frontend JavaScript
 * 
 * Handles the newsletter subscription form
 */

jQuery(document).ready(function($) {
    
    // Newsletter subscription form
    $('.swl-newsletter-form').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $submitButton = $form.find('button[type="submit"]');
        const $responseContainer = $form.find('.swl-newsletter-response');
        
        // If there's no response container, create one
        if ($responseContainer.length === 0) {
            $form.append('<div class="swl-newsletter-response mt-4"></div>');
            $responseContainer = $form.find('.swl-newsletter-response');
        }
        
        // Get data
        const formData = new FormData(this);
        formData.append('action', 'swl_newsletter_subscribe');
        formData.append('nonce', swl_newsletter_vars.nonce);
        
        // Check if terms checkbox is checked
        if ($form.find('input[name="terms"]').length > 0 && !$form.find('input[name="terms"]').is(':checked')) {
            $responseContainer.html('<div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-3 rounded">يجب الموافقة على سياسة الخصوصية وشروط الاستخدام</div>');
            return;
        }
        
        // Disable button to prevent multiple submissions
        $submitButton.prop('disabled', true);
        $submitButton.addClass('opacity-50 cursor-not-allowed');
        
        // Show loading indicator
        $responseContainer.html('<div class="text-gray-500">جاري معالجة طلبك...</div>');
        
        // Send AJAX request
        $.ajax({
            url: swl_newsletter_vars.ajax_url,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $responseContainer.html('<div class="bg-green-100 border-r-4 border-green-500 text-green-700 p-3 rounded">' + response.data.message + '</div>');
                    $form.find('input[type="email"]').val('');
                    $form.find('input[type="checkbox"]').prop('checked', false);
                } else {
                    $responseContainer.html('<div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-3 rounded">' + response.data.message + '</div>');
                }
            },
            error: function() {
                $responseContainer.html('<div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-3 rounded">حدث خطأ أثناء معالجة طلبك. يرجى المحاولة مرة أخرى.</div>');
            },
            complete: function() {
                $submitButton.prop('disabled', false);
                $submitButton.removeClass('opacity-50 cursor-not-allowed');
            }
        });
    });
}); 