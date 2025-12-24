<?php
/**
 * The template for displaying single broadcast
 */

if (!is_user_logged_in() && get_field('broadcast_requires_registration')) {
    // Check if WooCommerce is active
    if (function_exists('wc_get_page_permalink')) {
        // Redirect to WooCommerce my-account page with register tab
        $my_account_url = wc_get_page_permalink('myaccount');
        
        // Add register parameter to show the registration form by default
        // Store the current broadcast URL as the redirect_to parameter
        $redirect_url = add_query_arg(array(
            'register' => 'true',
            'redirect_to' => urlencode(get_permalink()),
        ), $my_account_url);
        
        wp_redirect($redirect_url);
    } else {
        // Fallback to WordPress login if WooCommerce is not active
        wp_redirect(wp_login_url(get_permalink()));
    }
    exit;
}

get_header();

// Track the post view
do_action('sarah_loz_track_post_view', get_the_ID());

// Get broadcast details
$broadcast_type = get_field('broadcast_type');
$video_url = get_field('broadcast_video_url');
$embed_code = get_field('broadcast_embed_code');
$scheduled_date = get_field('broadcast_scheduled_date');
$live_embed = get_field('broadcast_live_embed');
$chat_enabled = get_field('broadcast_chat_enabled');
$age_range = get_field('broadcast_age_range');

// Is it live?
$is_live = $broadcast_type === 'live';
$is_scheduled = $broadcast_type === 'scheduled';
$is_recorded = $broadcast_type === 'recorded';

// Determine the color and status label
$status_color = 'gray';
$status_label = __('مسجل', 'sarah-loz');

if ($is_live) {
    $status_color = 'red';
    $status_label = __('مباشر الآن', 'sarah-loz');
} elseif ($is_scheduled) {
    $status_color = 'blue';
    $status_label = __('قريباً', 'sarah-loz');
}
?>

<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="<?php echo esc_url(get_post_type_archive_link('broadcast')); ?>" class="inline-flex items-center text-primary hover:underline">
            <i class="fas fa-arrow-left mr-2"></i>
            <?php _e('العودة إلى البث', 'sarah-loz'); ?>
        </a>
    </div>
    
    <!-- Broadcast Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content - Broadcast and Details -->
        <div class="lg:col-span-2">
            <!-- Broadcast Video/Embed -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                <div class="relative">
                    <div class="aspect-w-16 aspect-h-9 bg-gray-900">
                        <?php if ($is_live && $live_embed) : ?>
                            <div class="w-full h-full">
                                <?php echo $live_embed; ?>
                            </div>
                        <?php elseif ($is_scheduled && $scheduled_date) : ?>
                            <div class="w-full h-full flex flex-col items-center justify-center p-6 text-center">
                                <?php if (has_post_thumbnail()) : ?>
                                    <div class="mb-6 w-full max-w-md">
                                        <?php the_post_thumbnail('large', array('class' => 'w-full h-auto rounded-lg')); ?>
                                    </div>
                                <?php else : ?>
                                    <i class="fas fa-calendar-alt text-6xl text-gray-400 mb-6"></i>
                                <?php endif; ?>
                                
                                <h2 class="text-2xl md:text-3xl font-bold text-white mb-4">
                                    <?php _e('سيبدأ البث في', 'sarah-loz'); ?>
                                </h2>
                                
                                <div class="countdown-timer flex items-center justify-center gap-4 mb-6" data-scheduled-date="<?php echo esc_attr($scheduled_date); ?>">
                                    <div class="bg-white bg-opacity-20 rounded-lg p-3 w-16 text-center">
                                        <div class="countdown-days text-3xl font-bold text-white">--</div>
                                        <div class="text-xs text-gray-300"><?php _e('يوم', 'sarah-loz'); ?></div>
                                    </div>
                                    <div class="bg-white bg-opacity-20 rounded-lg p-3 w-16 text-center">
                                        <div class="countdown-hours text-3xl font-bold text-white">--</div>
                                        <div class="text-xs text-gray-300"><?php _e('ساعة', 'sarah-loz'); ?></div>
                                    </div>
                                    <div class="bg-white bg-opacity-20 rounded-lg p-3 w-16 text-center">
                                        <div class="countdown-minutes text-3xl font-bold text-white">--</div>
                                        <div class="text-xs text-gray-300"><?php _e('دقيقة', 'sarah-loz'); ?></div>
                                    </div>
                                    <div class="bg-white bg-opacity-20 rounded-lg p-3 w-16 text-center">
                                        <div class="countdown-seconds text-3xl font-bold text-white">--</div>
                                        <div class="text-xs text-gray-300"><?php _e('ثانية', 'sarah-loz'); ?></div>
                                    </div>
                                </div>
                                
                                <p class="text-gray-300">
                                    <?php echo date_i18n('j F Y, g:i a', strtotime($scheduled_date)); ?>
                                </p>
                            </div>
                        <?php elseif ($is_recorded && $embed_code) : ?>
                            <div class="w-full h-full">
                                <?php echo $embed_code; ?>
                            </div>
                        <?php elseif ($is_recorded && $video_url) : ?>
                            <div class="w-full h-full">
                                <?php echo wp_oembed_get($video_url); ?>
                            </div>
                        <?php else : ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('large', array('class' => 'w-full h-auto')); ?>
                                <?php else : ?>
                                    <i class="fas fa-tv text-6xl text-gray-400"></i>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="absolute top-4 right-4 bg-<?php echo esc_attr($status_color); ?>-500 text-white px-3 py-1 rounded-full text-sm font-bold flex items-center">
                        <?php if ($is_live) : ?>
                            <span class="animate-ping absolute inline-flex h-3 w-3 rounded-full bg-<?php echo esc_attr($status_color); ?>-400 opacity-75 mr-1"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-<?php echo esc_attr($status_color); ?>-500 mr-1"></span>
                        <?php endif; ?>
                        <?php echo esc_html($status_label); ?>
                    </div>
                </div>
                
                <div class="p-6">
                    <h1 class="text-2xl md:text-3xl font-bold mb-2"><?php the_title(); ?></h1>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        <span class="inline-flex items-center bg-gray-100 text-gray-800 text-xs px-3 py-1 rounded-full">
                            <i class="far fa-calendar mr-1"></i>
                            <?php echo get_the_date(); ?>
                        </span>
                        
                        <?php if ($age_range && $age_range !== 'all') : ?>
                            <span class="inline-flex items-center bg-primary bg-opacity-10 text-primary text-xs px-3 py-1 rounded-full">
                                <i class="fas fa-child mr-1"></i>
                                <?php echo esc_html($age_range . ' ' . __('سنوات', 'sarah-loz')); ?>
                            </span>
                        <?php elseif ($age_range === 'all') : ?>
                            <span class="inline-flex items-center bg-primary bg-opacity-10 text-primary text-xs px-3 py-1 rounded-full">
                                <i class="fas fa-child mr-1"></i>
                                <?php _e('جميع الأعمار', 'sarah-loz'); ?>
                            </span>
                        <?php endif; ?>
                        
                        <?php if ($is_scheduled && $scheduled_date) : ?>
                            <span class="inline-flex items-center bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full">
                                <i class="far fa-clock mr-1"></i>
                                <?php echo date_i18n('j F Y, g:i a', strtotime($scheduled_date)); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($is_live && current_user_can('edit_posts')) : ?>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                        <h3 class="text-lg font-bold text-yellow-800 mb-2">
                            <i class="fas fa-broadcast-tower mr-2"></i><?php _e('إدارة البث المباشر', 'sarah-loz'); ?>
                        </h3>
                        <p class="text-yellow-700 mb-3"><?php _e('عند انتهاء البث المباشر، يمكنك تحويله إلى بث مسجل للحفاظ عليه.', 'sarah-loz'); ?></p>
                        <button id="transition-broadcast" class="bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded-lg transition-colors" data-id="<?php echo get_the_ID(); ?>">
                            <i class="fas fa-video mr-1"></i> <?php _e('إنهاء البث وتحويله إلى مسجل', 'sarah-loz'); ?>
                        </button>
                    </div>
                    
                    <script>
                    jQuery(document).ready(function($) {
                        $('#transition-broadcast').on('click', function() {
                            if (!confirm('<?php _e('هل أنت متأكد من إنهاء البث المباشر وتحويله إلى بث مسجل؟', 'sarah-loz'); ?>')) {
                                return;
                            }
                            
                            var broadcastId = $(this).data('id');
                            $(this).html('<i class="fas fa-spinner fa-spin mr-1"></i> <?php _e('جارٍ التحويل...', 'sarah-loz'); ?>').prop('disabled', true);
                            
                            $.ajax({
                                url: sarah_loz_broadcast.ajax_url,
                                type: 'POST',
                                data: {
                                    action: 'sarah_loz_transition_broadcast',
                                    broadcast_id: broadcastId,
                                    nonce: sarah_loz_broadcast.nonce
                                },
                                success: function(response) {
                                    if (response.success) {
                                        alert(response.data.message);
                                        window.location.href = response.data.redirect;
                                    } else {
                                        alert(response.data.message);
                                        $('#transition-broadcast').html('<i class="fas fa-video mr-1"></i> <?php _e('إنهاء البث وتحويله إلى مسجل', 'sarah-loz'); ?>').prop('disabled', false);
                                    }
                                },
                                error: function() {
                                    alert('<?php _e('حدث خطأ أثناء معالجة الطلب. يرجى المحاولة مرة أخرى.', 'sarah-loz'); ?>');
                                    $('#transition-broadcast').html('<i class="fas fa-video mr-1"></i> <?php _e('إنهاء البث وتحويله إلى مسجل', 'sarah-loz'); ?>').prop('disabled', false);
                                }
                            });
                        });
                    });
                    </script>
                    <?php endif; ?>
                    
                    <div class="prose max-w-none">
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
            
            <?php if ($is_live && $chat_enabled) : ?>
                <!-- Chat Section for Live Broadcasts -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                    <div class="p-4 bg-primary text-white font-bold">
                        <i class="fas fa-comments mr-2"></i>
                        <?php _e('الدردشة المباشرة', 'sarah-loz'); ?>
                    </div>
                    
                    <div class="chat-container h-96 flex flex-col">
                        <div id="chat-messages" class="flex-grow overflow-y-auto p-4 space-y-3">
                            <div class="text-center text-gray-500 py-4">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                <?php _e('جاري تحميل الرسائل...', 'sarah-loz'); ?>
                            </div>
                        </div>
                        
                        <div class="border-t p-4">
                            <?php if (is_user_logged_in()) : ?>
                                <form id="chat-form" class="flex gap-2">
                                    <input type="hidden" id="broadcast-id" value="<?php echo get_the_ID(); ?>">
                                    <input type="text" id="chat-message" placeholder="<?php esc_attr_e('اكتب رسالة...', 'sarah-loz'); ?>" class="flex-grow border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary">
                                    <button type="submit" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-opacity-90 transition">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </form>
                            <?php else : ?>
                                <div class="text-center py-3 bg-gray-50 rounded-lg">
                                    <p class="text-gray-600 mb-2"><?php _e('يجب عليك تسجيل الدخول للمشاركة في الدردشة', 'sarah-loz'); ?></p>
                                    <?php 
                                    // Check if WooCommerce is active
                                    if (function_exists('wc_get_page_permalink')) {
                                        $my_account_url = wc_get_page_permalink('myaccount');
                                        $login_url = add_query_arg(array(
                                            'redirect_to' => urlencode(get_permalink()),
                                        ), $my_account_url);
                                    } else {
                                        // Fallback to WordPress login
                                        $login_url = wp_login_url(get_permalink());
                                    }
                                    ?>
                                    <a href="<?php echo esc_url($login_url); ?>" class="inline-block bg-primary text-white py-1 px-4 rounded-lg hover:bg-opacity-90 transition text-sm">
                                        <?php _e('تسجيل الدخول', 'sarah-loz'); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Sidebar Content -->
        <div class="lg:col-span-1">
            <!-- Related Broadcasts -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-8">
                <div class="p-4 bg-secondary text-white font-bold">
                    <i class="fas fa-tv mr-2"></i>
                    <?php _e('بث ذات صلة', 'sarah-loz'); ?>
                </div>
                
                <div class="p-4">
                    <?php
                    $related_args = array(
                        'post_type' => 'broadcast',
                        'posts_per_page' => 5,
                        'post__not_in' => array(get_the_ID()),
                        'orderby' => 'rand',
                    );
                    
                    $related_broadcasts = new WP_Query($related_args);
                    
                    if ($related_broadcasts->have_posts()) :
                        while ($related_broadcasts->have_posts()) : $related_broadcasts->the_post();
                            $rel_broadcast_type = get_field('broadcast_type');
                            $rel_is_live = $rel_broadcast_type === 'live';
                            $rel_color = $rel_is_live ? 'red' : ($rel_broadcast_type === 'scheduled' ? 'blue' : 'gray');
                    ?>
                        <a href="<?php the_permalink(); ?>" class="flex items-start gap-3 hover:bg-gray-50 p-2 rounded-lg mb-3 transition">
                            <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg overflow-hidden">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('thumbnail', array('class' => 'w-full h-full object-cover')); ?>
                                <?php else : ?>
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-tv text-2xl text-gray-400"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex-grow">
                                <h3 class="font-medium text-sm mb-1 line-clamp-2"><?php the_title(); ?></h3>
                                <span class="inline-block bg-<?php echo esc_attr($rel_color); ?>-100 text-<?php echo esc_attr($rel_color); ?>-800 text-xs px-2 py-0.5 rounded-full">
                                    <?php 
                                    if ($rel_is_live) {
                                        echo __('مباشر', 'sarah-loz');
                                    } elseif ($rel_broadcast_type === 'scheduled') {
                                        echo __('مجدول', 'sarah-loz');
                                    } else {
                                        echo __('مسجل', 'sarah-loz');
                                    }
                                    ?>
                                </span>
                            </div>
                        </a>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                    ?>
                        <div class="text-center py-6 text-gray-500">
                            <i class="fas fa-tv text-3xl mb-2"></i>
                            <p><?php _e('لا يوجد بث ذات صلة', 'sarah-loz'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Other Content Types -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="p-4 bg-accent text-dark font-bold">
                    <i class="fas fa-star mr-2"></i>
                    <?php _e('محتوى مقترح', 'sarah-loz'); ?>
                </div>
                
                <div class="p-4">
                    <?php
                    $suggested_args = array(
                        'post_type' => array('game', 'activity', 'video'),
                        'posts_per_page' => 3,
                        'orderby' => 'rand',
                    );
                    
                    $suggested_query = new WP_Query($suggested_args);
                    
                    if ($suggested_query->have_posts()) :
                        while ($suggested_query->have_posts()) : $suggested_query->the_post();
                            $post_type = get_post_type();
                            $icon_class = 'fas fa-gamepad';
                            $color = 'blue';
                            
                            if ($post_type === 'activity') {
                                $icon_class = 'fas fa-tasks';
                                $color = 'purple';
                            } elseif ($post_type === 'video') {
                                $icon_class = 'fas fa-play-circle';
                                $color = 'red';
                            }
                    ?>
                        <a href="<?php the_permalink(); ?>" class="flex items-start gap-3 hover:bg-gray-50 p-2 rounded-lg mb-3 transition">
                            <div class="rounded-full bg-<?php echo esc_attr($color); ?>-100 p-3 flex-shrink-0 w-12 h-12 flex items-center justify-center">
                                <i class="<?php echo esc_attr($icon_class); ?> text-<?php echo esc_attr($color); ?>-500"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-sm mb-1"><?php the_title(); ?></h3>
                                <p class="text-xs text-gray-500 line-clamp-2"><?php echo wp_trim_words(get_the_excerpt(), 10); ?></p>
                            </div>
                        </a>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                    ?>
                        <div class="text-center py-6 text-gray-500">
                            <i class="fas fa-star text-3xl mb-2"></i>
                            <p><?php _e('لا يوجد محتوى مقترح', 'sarah-loz'); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Countdown Timer for Scheduled Broadcasts
document.addEventListener('DOMContentLoaded', function() {
    const countdownTimer = document.querySelector('.countdown-timer');
    
    if (countdownTimer) {
        const scheduledDate = countdownTimer.getAttribute('data-scheduled-date');
        const scheduledTimestamp = new Date(scheduledDate).getTime();
        
        const daysElement = document.querySelector('.countdown-days');
        const hoursElement = document.querySelector('.countdown-hours');
        const minutesElement = document.querySelector('.countdown-minutes');
        const secondsElement = document.querySelector('.countdown-seconds');
        
        const countdownInterval = setInterval(function() {
            const now = new Date().getTime();
            const distance = scheduledTimestamp - now;
            
            // Time calculations
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
            
            // Display the results
            daysElement.textContent = days;
            hoursElement.textContent = hours;
            minutesElement.textContent = minutes;
            secondsElement.textContent = seconds;
            
            // If the countdown is finished
            if (distance < 0) {
                clearInterval(countdownInterval);
                daysElement.textContent = '0';
                hoursElement.textContent = '0';
                minutesElement.textContent = '0';
                secondsElement.textContent = '0';
                
                // Reload the page to show the live broadcast
                setTimeout(function() {
                    window.location.reload();
                }, 3000);
            }
        }, 1000);
    }
    
    // Chat functionality
    const chatForm = document.getElementById('chat-form');
    const chatMessages = document.getElementById('chat-messages');
    
    if (chatForm && chatMessages) {
        // Load initial messages
        loadChatMessages();
        
        // Set up polling for new messages every 5 seconds
        setInterval(loadChatMessages, 5000);
        
        // Handle form submission
        chatForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const messageInput = document.getElementById('chat-message');
            const message = messageInput.value.trim();
            const broadcastId = document.getElementById('broadcast-id').value;
            
            if (message !== '') {
                sendChatMessage(broadcastId, message);
                messageInput.value = '';
            }
        });
    }
    
    function loadChatMessages() {
        const broadcastId = document.getElementById('broadcast-id').value;
        
        fetch(sarah_loz_broadcast.ajax_url + '?action=sarah_loz_get_chat_messages&broadcast_id=' + broadcastId + '&nonce=' + sarah_loz_broadcast.nonce)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.messages) {
                    displayChatMessages(data.data.messages);
                }
            })
            .catch(error => {
                console.error('Error loading chat messages:', error);
            });
    }
    
    function sendChatMessage(broadcastId, message) {
        const formData = new FormData();
        formData.append('action', 'sarah_loz_send_chat_message');
        formData.append('broadcast_id', broadcastId);
        formData.append('message', message);
        formData.append('nonce', sarah_loz_broadcast.nonce);
        
        fetch(sarah_loz_broadcast.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Message sent successfully, will be loaded on next poll
                // Optionally add immediately to the UI for faster feedback
                const currentUser = true; // Current user's message
                const userMessage = {
                    user_name: data.data.user_name,
                    message: data.data.message,
                    timestamp: data.data.timestamp,
                    is_current_user: true
                };
                
                addMessageToChat(userMessage);
            }
        })
        .catch(error => {
            console.error('Error sending chat message:', error);
        });
    }
    
    function displayChatMessages(messages) {
        // Clear loading message if present
        if (chatMessages.querySelector('.text-center.text-gray-500')) {
            chatMessages.innerHTML = '';
        }
        
        // Add all messages
        if (messages.length === 0) {
            chatMessages.innerHTML = `
                <div class="text-center text-gray-500 py-4">
                    <i class="fas fa-comments mr-2"></i>
                    <?php _e('لا توجد رسائل بعد. كن أول من يشارك!', 'sarah-loz'); ?>
                </div>
            `;
        } else {
            // Keep existing messages if any (avoid flickering)
            if (chatMessages.children.length === 0 || chatMessages.querySelector('.text-center.text-gray-500')) {
                chatMessages.innerHTML = '';
                messages.forEach(message => {
                    addMessageToChat(message);
                });
            } else {
                // Check if we have new messages to add
                const lastMsgElement = chatMessages.lastElementChild;
                const lastMsg = lastMsgElement ? lastMsgElement.textContent : '';
                
                // Add only new messages (simple approach)
                const lastMessages = messages.slice(-3); // Get last few messages
                let hasNewMessages = false;
                
                for (const message of lastMessages) {
                    if (!chatMessages.textContent.includes(message.message)) {
                        addMessageToChat(message);
                        hasNewMessages = true;
                    }
                }
                
                if (hasNewMessages) {
                    // Scroll to bottom on new messages
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            }
        }
    }
    
    function addMessageToChat(message) {
        const messageElement = document.createElement('div');
        messageElement.className = message.is_current_user 
            ? 'flex flex-col items-end' 
            : 'flex flex-col items-start';
        
        // Add message ID as a data attribute if available
        if (message.id) {
            messageElement.setAttribute('data-message-id', message.id);
        }
        
        const bubbleClass = message.is_current_user
            ? 'bg-primary text-white rounded-tl-lg rounded-tr-lg rounded-bl-lg'
            : 'bg-gray-100 text-gray-800 rounded-tl-lg rounded-tr-lg rounded-br-lg';
        
        messageElement.innerHTML = `
            <div class="flex items-center mb-1">
                <span class="font-bold text-xs ${message.is_current_user ? 'text-primary' : 'text-gray-600'}">${message.user_name}</span>
                <span class="text-gray-500 text-xs ml-2">${message.timestamp}</span>
            </div>
            <div class="px-4 py-2 max-w-[80%] ${bubbleClass}">
                ${message.message}
            </div>
        `;
        
        chatMessages.appendChild(messageElement);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    // Track active viewers for live broadcasts
    const broadcastId = document.getElementById('broadcast-id');
    const isLive = document.querySelector('.bg-red-500') !== null; // Red badge indicates live
    
    if (isLive && broadcastId && sarah_loz_broadcast.is_user_logged_in === 'yes') {
        // Send initial ping
        trackActiveViewer();
        
        // Send periodic pings to maintain active status (every 30 seconds)
        setInterval(trackActiveViewer, 30000);
        
        // Also track on user activity
        let activityTimeout;
        const resetActivityTimeout = function() {
            clearTimeout(activityTimeout);
            activityTimeout = setTimeout(trackActiveViewer, 5000); // Track after 5 seconds of activity
        };
        
        // Track on common user interactions
        document.addEventListener('mousemove', resetActivityTimeout);
        document.addEventListener('keydown', resetActivityTimeout);
        document.addEventListener('click', resetActivityTimeout);
        document.addEventListener('scroll', resetActivityTimeout);
    }
    
    function trackActiveViewer() {
        const formData = new FormData();
        formData.append('action', 'sarah_loz_track_broadcast_viewer');
        formData.append('broadcast_id', broadcastId.value);
        formData.append('nonce', sarah_loz_broadcast.nonce);
        
        fetch(sarah_loz_broadcast.ajax_url, {
            method: 'POST',
            body: formData
        }).catch(error => {
            console.error('Error tracking viewer:', error);
        });
    }
});
</script>

<?php get_footer(); ?> 