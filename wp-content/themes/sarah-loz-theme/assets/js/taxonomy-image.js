jQuery(document).ready(function($) {
    
    // Handle taxonomy image upload
    $(document).on('click', '#upload-taxonomy-image', function(e) {
        e.preventDefault();
        
        var button = $(this);
        var field = $('#taxonomy-image');
        var preview = $('#taxonomy-image-preview');
        var removeBtn = $('#remove-taxonomy-image');
        
        var mediaUploader = wp.media({
            title: taxonomyImageAjax.upload_title,
            button: {
                text: taxonomyImageAjax.upload_button
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });
        
        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            
            field.val(attachment.id);
            preview.attr('src', attachment.sizes.thumbnail ? attachment.sizes.thumbnail.url : attachment.url).show();
            removeBtn.show();
        });
        
        mediaUploader.open();
    });
    
    // Handle taxonomy image removal
    $(document).on('click', '#remove-taxonomy-image', function(e) {
        e.preventDefault();
        
        var field = $('#taxonomy-image');
        var preview = $('#taxonomy-image-preview');
        var removeBtn = $('#remove-taxonomy-image');
        
        field.val('');
        preview.hide();
        removeBtn.hide();
    });
    
    // Add thumbnail column to taxonomy list tables
    function addThumbnailColumn() {
        // Add header
        $('.wp-list-table thead tr, .wp-list-table tfoot tr').each(function() {
            $(this).find('th:first').after('<th scope="col" class="manage-column column-image">الصورة</th>');
        });
        
        // Add cells for each row
        $('.wp-list-table tbody tr').each(function() {
            var termId = $(this).attr('id');
            if (termId) {
                termId = termId.replace('tag-', '');
                
                // Get image if exists (this would need AJAX call to get actual images)
                $(this).find('td:first').after('<td class="column-image"><div class="taxonomy-image-thumb" style="width: 40px; height: 40px; background: #f1f1f1; border-radius: 4px;"></div></td>');
            }
        });
    }
    
    // Only add columns on taxonomy edit pages
    if ($('body').hasClass('edit-tags-php')) {
        // Wait for page to load completely
        setTimeout(addThumbnailColumn, 100);
    }
}); 