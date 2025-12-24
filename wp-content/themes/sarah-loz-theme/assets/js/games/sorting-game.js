/**
 * Sorting Game
 * Drag and drop items into correct categories or sequences
 */

class SortingGame extends GameFramework {
    constructor(containerId, options = {}) {
        // Set default options for sorting game
        const sortingGameOptions = Object.assign({
            items: [], // Array of item objects with text or image, category, and id
            categories: [], // Array of category objects with name and id
            isSequence: false, // Whether items should be sorted in a specific sequence instead of categories
            dropEffect: 'copy', // CSS for drag effect (changed from 'move' to 'copy')
            showItemShadow: true, // Show shadow of item during drag
            allowIncorrectPlacements: false, // Whether to allow items to be placed in incorrect categories
            correctSound: '', // URL to sound played on correct placement
            errorSound: '', // URL to sound played on incorrect placement
            difficulty: 'medium',
            isMobileEnabled: true, // Whether to enable touch support for mobile devices
            requireAllCorrect: false // Whether to require all items to be placed correctly for game completion
        }, options);

        super(containerId, sortingGameOptions);

        // Game-specific state
        this.gameState = {
            draggedItem: null,
            placedItems: {},
            correctPlacements: 0,
            incorrectPlacements: 0,
            totalItems: 0,
            categoriesDOM: {},
            isMobile: this.isTouchDevice(), // Detect if running on touch device
            processedItems: new Set() // Keep track of items that have been processed
        };
        
        // Add CSS for animations
        this.addAnimationStyles();
        
        // Load jQuery UI for drag and drop if needed
        this.loadDependencies();
    }

    loadDependencies() {
        // Check if jQuery UI is available
        if (typeof jQuery === 'undefined' || typeof jQuery.ui === 'undefined' || !jQuery.ui.draggable) {
            console.error('jQuery UI is required for the sorting game. Please include it in your page.');
            return false;
        }
        
        // If on mobile device and Touch Punch is not available, try to load it dynamically
        if (this.isTouchDevice() && typeof jQuery.ui.touch === 'undefined') {
            try {
                this.loadTouchPunch();
            } catch (error) {
                console.warn('Could not load jQuery UI Touch Punch:', error);
                // Will fallback to custom touch handling instead
            }
        }
        
        return true;
    }

    // Dynamically load jQuery UI Touch Punch
    loadTouchPunch() {
        console.log('Attempting to load jQuery UI Touch Punch');
        
        // Check if already loaded or being loaded
        if (document.querySelector('script[src*="jquery.ui.touch-punch"]')) {
            console.log('Touch Punch script already loading');
            return;
        }
        
        // Check if jQuery UI is available first
        if (typeof jQuery === 'undefined' || typeof jQuery.ui === 'undefined' || !jQuery.ui.draggable) {
            console.error('Cannot load Touch Punch: jQuery UI is required first');
            return;
        }
        
        // Try loading from CDN
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js';
        script.async = true;
        script.onload = () => {
            console.log('Touch Punch loaded successfully');
            
            // If game is already initialized, refresh draggable elements
            if (this.gameContainer && this.gameContainer.querySelector('.sorting-item')) {
                console.log('Refreshing draggable elements with Touch Punch support');
                try {
                    jQuery('.sorting-item').draggable('destroy');
                    
                    // Re-initialize drag and drop with Touch Punch support
                    setTimeout(() => this.setupDragAndDrop(), 100);
                } catch (err) {
                    console.warn('Error refreshing draggables:', err);
                }
            }
        };
        script.onerror = () => {
            console.warn('Failed to load Touch Punch from CDN, falling back to local touch handling');
            this.enableSimpleTouchHandling();
        };
        
        document.head.appendChild(script);
    }

    onGameStart() {
        // Add requireAllCorrect option with a default value if not set
        if (typeof this.options.requireAllCorrect === 'undefined') {
            this.options.requireAllCorrect = false; // Default to allowing incorrect placements for game completion
        }
        
        // Store start time
        this.startTime = Date.now();
        
        // Clear previous game
        this.clearElements();
        this.gameContent.innerHTML = '';
        
        // Reset game-specific state
        this.gameState.placedItems = {};
        this.gameState.correctPlacements = 0;
        this.gameState.incorrectPlacements = 0;
        this.gameState.processedItems = new Set();
        
        // Create game elements
        this.createGameElements();
        
        // Setup drag and drop
        setTimeout(() => {
            this.setupDragAndDrop();
        }, 500);
    }

    createGameElements() {
        // Define layout based on difficulty and game type
        const items = this.getItemsByDifficulty(this.options.items, this.options.difficulty);
        this.gameState.items = items;
        this.gameState.totalItems = items.length;
        
        // Update mobile detection before creating layout
        this.gameState.isMobile = this.isTouchDevice();
        
        // Create game layout container
        const layoutContainer = this.createElement('div', {
            classes: 'sorting-game-layout',
            styles: {
                display: 'flex',
                flexDirection: 'column',
                width: '100%',
                height: '100%',
                // Remove padding - let CSS handle it
                overflow: 'auto', // Add scrolling to handle overflow
                boxSizing: 'border-box' // Ensure padding doesn't add to element size
            }
        });
        
        // Create progress bar container at the top
        const progressContainer = this.createElement('div', {
            classes: 'sorting-progress-container',
            parent: layoutContainer
            // Let CSS handle all styling
        });
        
        // Create progress bar
        this.progressBar = this.createElement('div', {
            classes: 'sorting-progress-bar progress-bar',
            parent: progressContainer,
            styles: {
                width: '0%'
                // Let CSS handle other styling
            }
        });
        
        // Create progress text
        this.progressText = this.createElement('div', {
            classes: 'sorting-progress-text progress-text',
            parent: layoutContainer,
            text: `0/${this.gameState.totalItems} items placed correctly`
            // Let CSS handle styling
        });
        
        // Create instructions header with clearer text
        const instructionsText = this.options.isSequence 
            ? 'ضع العناصر في التسلسل الصحيح' 
            : 'ضع العناصر في الفئات المناسبة';
        
        this.createElement('div', {
            classes: 'sorting-instructions',
            parent: layoutContainer,
            text: instructionsText
            // Let CSS handle all styling
        });
        
        // Create categories/targets section with adjustments for mobile
        const categoriesSection = this.createElement('div', {
            classes: 'sorting-categories-section',
            parent: layoutContainer
            // Let CSS handle all styling
        });
        
        // Create a divider with reduced margin on mobile
        this.createElement('div', {
            classes: 'sorting-divider',
            parent: layoutContainer,
            styles: {
                width: '100%',
                height: '2px',
                backgroundColor: '#ddd',
                margin: this.gameState.isMobile ? '8px 0' : '10px 0',
                borderRadius: '2px'
            }
        });
        
        // Create items section with fixed height and scrolling
        const itemsSection = this.createElement('div', {
            classes: 'sorting-items-section',
            parent: layoutContainer
            // Let CSS handle all styling
        });
        
        // Create categories or sequence targets
        if (this.options.isSequence) {
            // Create sequence containers
            for (let i = 0; i < items.length; i++) {
                const position = i + 1;
                const sequenceContainer = this.createElement('div', {
                    classes: 'sequence-container',
                    parent: categoriesSection,
                    attributes: {
                        'data-position': position,
                        'data-type': 'sequence'
                    }
                    // Let CSS handle styling
                });
                
                // Add position label with smaller text on mobile
                this.createElement('div', {
                    classes: 'sequence-number',
                    parent: sequenceContainer,
                    text: position,
                    styles: {
                        position: 'absolute',
                        top: '5px',
                        right: '5px',
                        backgroundColor: '#4caf50',
                        color: 'white',
                        width: '24px',
                        height: '24px',
                        borderRadius: '50%',
                        display: 'flex',
                        justifyContent: 'center',
                        alignItems: 'center',
                        fontSize: '12px',
                        fontWeight: 'bold'
                    }
                });
                
                // Store reference in game state
                this.gameState.categoriesDOM[position] = sequenceContainer;
            }
        } else {
            // Create category containers with responsive layout
            const categories = this.options.categories || [];
            const categoryWidth = this.calculateCategoryWidth(categories.length);
            
            categories.forEach(category => {
                const categoryContainer = this.createElement('div', {
                    classes: 'category-container',
                    parent: categoriesSection,
                    attributes: {
                        'data-category': category.id,
                        'data-type': 'category'
                    },
                    styles: {
                        width: categoryWidth,
                        minHeight: this.gameState.isMobile ? '120px' : '180px',
                        maxHeight: this.gameState.isMobile ? '200px' : '300px',
                        backgroundColor: '#e9e9e9',
                        border: '2px dashed #aaa',
                        borderRadius: '8px',
                        padding: this.gameState.isMobile ? '5px' : '10px',
                        display: 'flex',
                        flexDirection: 'column',
                        alignItems: 'center',
                        gap: this.gameState.isMobile ? '8px' : '10px',
                        overflow: 'auto', // Allow scrolling if many items are placed in one category
                        boxSizing: 'border-box',
                        transition: 'background-color 0.2s ease' // Add transition for visual feedback
                    }
                });
                
                // Add category title with smaller text on mobile
                this.createElement('div', {
                    classes: 'category-title',
                    parent: categoryContainer,
                    text: category.name,
                    styles: {
                        fontWeight: 'bold',
                        backgroundColor: '#4caf50',
                        color: 'white',
                        padding: this.gameState.isMobile ? '4px 6px' : '5px 10px',
                        borderRadius: '4px',
                        width: '100%',
                        textAlign: 'center',
                        fontSize: this.gameState.isMobile ? '11px' : '14px',
                        position: 'sticky', // Make title sticky so it remains visible when scrolling
                        top: '0',
                        zIndex: '1'
                    }
                });
                
                // Create a container for placed items
                const itemsContainer = this.createElement('div', {
                    classes: 'category-items-container',
                    parent: categoryContainer,
                    styles: {
                        display: 'flex',
                        flexDirection: 'column', // Stack items vertically
                        alignItems: 'center',
                        width: '100%',
                        gap: this.gameState.isMobile ? '6px' : '10px', // Space between items
                        paddingTop: '5px',
                        paddingBottom: '5px',
                        flexGrow: 1
                    }
                });
                
                // Store references in game state
                this.gameState.categoriesDOM[category.id] = {
                    container: categoryContainer,
                    itemsContainer: itemsContainer
                };
            });
        }
        
        // Create draggable items with optimized size for device
        const itemSize = this.calculateItemSize(items.length);
        
        this.shuffleArray(items).forEach((item, index) => {
            const itemElement = this.createElement('div', {
                classes: 'sorting-item',
                parent: itemsSection,
                attributes: {
                    'data-id': item.id,
                    'data-category': item.category,
                    'data-position': item.position
                },
                styles: {
                    width: itemSize,
                    height: itemSize,
                    backgroundColor: 'white',
                    border: '2px solid #ddd',
                    borderRadius: '8px',
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    cursor: 'grab',
                    userSelect: 'none',
                    overflow: 'hidden',
                    padding: '5px',
                    boxShadow: '0 2px 5px rgba(0,0,0,0.1)',
                    transition: 'transform 0.1s, box-shadow 0.1s', // Add smooth transitions for hover effects
                    touchAction: this.gameState.isMobile ? 'none' : 'auto', // Prevent scrolling while dragging on mobile
                    willChange: 'transform' // Hint for browser optimization
                }
            });
            
            // Add hover effect with event listeners (only for non-touch devices)
            if (!this.gameState.isMobile) {
                itemElement.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                    this.style.boxShadow = '0 5px 10px rgba(0,0,0,0.2)';
                });
                
                itemElement.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                    this.style.boxShadow = '0 2px 5px rgba(0,0,0,0.1)';
                });
            }
            
            // Add content to item (text or image)
            if (item.image) {
                const img = this.createElement('img', {
                    parent: itemElement,
                    attributes: {
                        src: item.image,
                        alt: item.text || 'Item ' + item.id,
                        draggable: 'false', // Prevent native image dragging
                        loading: 'lazy' // Add lazy loading for better performance
                    },
                    styles: {
                        maxWidth: '90%',
                        maxHeight: '90%',
                        objectFit: 'contain',
                        userSelect: 'none',
                        pointerEvents: 'none' // Prevent image from interfering with drag
                    }
                });
                
                // Add error handler for image loading failures
                img.addEventListener('error', () => {
                    img.style.display = 'none';
                    // Add fallback text if image fails to load
                    const fallback = document.createElement('div');
                    fallback.textContent = item.text || 'Item ' + item.id;
                    fallback.style.textAlign = 'center';
                    fallback.style.fontSize = this.gameState.isMobile ? '10px' : '12px';
                    itemElement.appendChild(fallback);
                });
            } else if (item.text) {
                this.createElement('div', {
                    parent: itemElement,
                    text: item.text,
                    styles: {
                        textAlign: 'center',
                        fontSize: this.gameState.isMobile ? '10px' : '12px',
                        wordBreak: 'break-word',
                        userSelect: 'none',
                        pointerEvents: 'none' // Prevent text from interfering with drag
                    }
                });
            }
        });
        
        // Add game container to the DOM
        this.gameContent.appendChild(layoutContainer);
        
        // Add resize event listener for responsiveness
        window.addEventListener('resize', this.handleResize.bind(this));
    }

    setupDragAndDrop() {
        try {
            console.log('Setting up drag and drop functionality');
            
            // Add diagnostic information
            console.log('Game environment:', {
                'jQuery Available': typeof jQuery !== 'undefined',
                'jQuery Version': typeof jQuery !== 'undefined' ? jQuery.fn.jquery : 'N/A',
                'jQuery UI Available': typeof jQuery !== 'undefined' && typeof jQuery.ui !== 'undefined',
                'Touch Punch Available': typeof jQuery !== 'undefined' && typeof jQuery.ui !== 'undefined' && typeof jQuery.ui.touch !== 'undefined',
                'Is Mobile Device': this.gameState.isMobile,
                'Screen Width': window.innerWidth,
                'Screen Height': window.innerHeight,
                'Game Container Width': this.gameContainer ? this.gameContainer.clientWidth : 'N/A',
                'Total Items': this.gameState.totalItems,
                'Game Mode': this.options.isSequence ? 'Sequence' : 'Categories'
            });
            
            if (!this.loadDependencies()) {
                console.error('Cannot set up drag and drop due to missing dependencies');
                this.displayMessage('Required JavaScript libraries are missing', 'error');
                return;
            }
            
            const self = this;
            
            // Make items draggable with jQuery UI
            jQuery('.sorting-item').draggable({
                revert: 'invalid',
                containment: this.gameContainer,
                cursor: 'move',
                helper: this.gameState.isMobile ? 'original' : 'clone', // Use original on mobile for better performance
                zIndex: 100,
                appendTo: 'body',
                distance: this.gameState.isMobile ? 10 : 5, // Increase drag start threshold for mobile
                start: function(event, ui) {
                    jQuery(this).css('opacity', '0.6');
                    
                    // Add visual feedback for mobile
                    if (self.gameState.isMobile) {
                        jQuery(this).css({
                            'transform': 'scale(1.05)',
                            'box-shadow': '0 5px 15px rgba(0,0,0,0.2)'
                        });
                        
                        // Highlight potential drop zones
                        if (self.options.isSequence) {
                            jQuery('.sequence-container').css('background-color', '#f0f0f0');
                        } else {
                            const itemCategory = jQuery(this).data('category');
                            jQuery('.category-container').each(function() {
                                const containerCategory = jQuery(this).data('category');
                                if (containerCategory === itemCategory) {
                                    jQuery(this).css('background-color', '#e8f5e9'); // Subtle green for correct category
                                } else {
                                    jQuery(this).css('background-color', '#f0f0f0'); // Neutral for other categories
                                }
                            });
                        }
                    }
                },
                stop: function(event, ui) {
                    jQuery(this).css({
                        'opacity': '1',
                        'transform': 'scale(1)',
                        'box-shadow': '0 2px 5px rgba(0,0,0,0.1)'
                    });
                    
                    // Reset drop zone highlighting
                    if (self.gameState.isMobile) {
                        if (self.options.isSequence) {
                            jQuery('.sequence-container').css('background-color', '#e9e9e9');
                        } else {
                            jQuery('.category-container').css('background-color', '#e9e9e9');
                        }
                    }
                }
            });
            
            // Add touch feedback for drop zones
            const addDropVisualFeedback = function(element) {
                element.addEventListener('touchstart', function() {
                    this.style.boxShadow = '0 0 0 3px rgba(76, 175, 80, 0.5)';
                });
                
                element.addEventListener('touchend', function() {
                    this.style.boxShadow = 'none';
                });
            };
            
            // Enable jQuery UI Touch Punch if available (for mobile touch support)
            if (this.gameState.isMobile && typeof jQuery.ui.touch !== 'undefined') {
                console.log('Touch Punch detected, enabling touch events');
                // Touch Punch should be auto-enabled if loaded
                
                // Add visual feedback to drop zones
                if (this.options.isSequence) {
                    document.querySelectorAll('.sequence-container').forEach(addDropVisualFeedback);
                } else {
                    document.querySelectorAll('.category-container').forEach(addDropVisualFeedback);
                }
            } else if (this.gameState.isMobile) {
                console.log('Touch device detected but jQuery UI Touch Punch not available');
                
                // Load Touch Punch dynamically
                this.loadTouchPunch();
                
                // Fallback to simple touch handling if needed
                setTimeout(() => {
                    if (typeof jQuery.ui.touch === 'undefined') {
                        this.enableSimpleTouchHandling();
                        
                        // Add visual feedback to drop zones
                        if (this.options.isSequence) {
                            document.querySelectorAll('.sequence-container').forEach(addDropVisualFeedback);
                        } else {
                            document.querySelectorAll('.category-container').forEach(addDropVisualFeedback);
                        }
                    }
                }, 1000); // Wait a bit for Touch Punch to load
            }
            
            // Set up enhanced droppable options with better mobile handling
            const dropOptions = {
                accept: '.sorting-item',
                tolerance: this.gameState.isMobile ? 'touch' : 'pointer', // Use touch intersection on mobile
                classes: {
                    'ui-droppable-hover': 'ui-state-hover' // Add class for visual feedback
                },
                over: function(event, ui) {
                    jQuery(this).css({
                        'background-color': '#e0f2f1',
                        'border-color': '#80cbc4'
                    });
                },
                out: function(event, ui) {
                    jQuery(this).css({
                        'background-color': '#e9e9e9',
                        'border-color': '#aaa'
                    });
                },
                drop: function(event, ui) {
                    const $dropZone = jQuery(this);
                    const $draggedItem = ui.draggable;
                    
                    // Get item and target information
                    const itemId = $draggedItem.data('id');
                    const itemCategory = $draggedItem.data('category');
                    const itemPosition = $draggedItem.data('position');
                    
                    // Determine whether this is a sequence game or category game
                    let targetId, isCorrect;
                    
                    if (self.options.isSequence) {
                        // For sequence game
                        targetId = $dropZone.data('position');
                        isCorrect = itemPosition == targetId;
                    } else {
                        // For category game
                        targetId = $dropZone.data('category');
                        isCorrect = itemCategory == targetId;
                    }
                    
                    console.log(`Drop detected - Item: ${itemId}, Target: ${targetId}, Correct: ${isCorrect}`);
                    
                    // Visual feedback
                    $dropZone.css({
                        'background-color': isCorrect ? '#e8f5e9' : '#ffebee',
                        'border-color': isCorrect ? '#66bb6a' : '#ef5350'
                    });
                    
                    // Clone the item into the drop zone
                    const $clone = $draggedItem.clone();
                    $clone.css({
                        'position': 'relative',
                        'top': 'auto',
                        'left': 'auto',
                        'z-index': 'auto',
                        'transform': 'none',
                        'margin': '5px auto',
                        'border': isCorrect ? '2px solid #4CAF50' : '2px solid #F44336',
                        'box-shadow': isCorrect ? '0 0 8px rgba(76,175,80,0.5)' : '0 0 8px rgba(244,67,54,0.5)'
                    });
                    
                    // Add to drop zone
                    if (self.options.isSequence) {
                        // Clear existing items in this sequence position
                        $dropZone.find('.sorting-item').remove();
                        $dropZone.append($clone);
                    } else {
                        // Add to category items container
                        const $itemsContainer = $dropZone.find('.category-items-container');
                        if ($itemsContainer.length) {
                            $itemsContainer.append($clone);
                        } else {
                            $dropZone.append($clone);
                        }
                    }
                    
                    // Handle the original item
                    if (isCorrect) {
                        // If correct, completely remove the item from source list
                        // First add a "success" animation effect
                        $draggedItem.addClass('correct-placement-animation');
                        $draggedItem.css({
                            'transform': 'scale(1.1)',
                            'transition': 'all 0.3s ease',
                            'z-index': '200',
                            'box-shadow': '0 0 15px rgba(76, 175, 80, 0.8)'
                        });
                        
                        // Then fade out and remove
                        setTimeout(() => {
                            $draggedItem.fadeOut(300, function() {
                                $draggedItem.remove(); // Completely remove from DOM
                            });
                        }, 300);
                    } else {
                        // If incorrect, just disable and fade it
                        $draggedItem.css('opacity', '0.3');
                        $draggedItem.draggable('option', 'disabled', true);
                    }
                    
                    // Record placement in game state
                    self.recordPlacement(itemId, targetId, isCorrect);
                    
                    // Play appropriate sound
                    if (isCorrect && self.options.correctSound) {
                        self.playSound(self.options.correctSound);
                    } else if (!isCorrect && self.options.errorSound) {
                        self.playSound(self.options.errorSound);
                    }
                    
                    // Check game completion
                    self.checkGameCompletion();
                }
            };
            
            if (this.options.isSequence) {
                // Setup droppable for sequence game
                jQuery('.sequence-container').droppable(dropOptions);
            } else {
                // Setup droppable for category game
                jQuery('.category-container').droppable(dropOptions);
            }
            
            console.log('Drag and drop setup complete');
        } catch (error) {
            console.error('Error setting up drag and drop:', error);
            this.displayMessage('Error setting up game interactions', 'error');
        }
    }
    
    recordPlacement(itemId, targetId, isCorrect) {
        console.log(`Recording placement of item ${itemId} to target ${targetId}. Correct: ${isCorrect}`);
        
        // Store placement in game state (don't modify original item)
        this.gameState.placedItems[itemId] = {
            targetId: targetId,
            isCorrect: isCorrect
        };
        
        // Initialize counts if not already done
        if (typeof this.gameState.correctPlacements !== 'number') {
            this.gameState.correctPlacements = 0;
        }
        if (typeof this.gameState.incorrectPlacements !== 'number') {
            this.gameState.incorrectPlacements = 0;
        }
        
        // Update counts
        if (isCorrect) {
            this.gameState.correctPlacements++;
        } else {
            this.gameState.incorrectPlacements++;
        }
        
        console.log('Current game state:', this.gameState);
    }
    
    checkGameCompletion() {
        // Count unique items that have been placed
        const placedUniqueItems = new Set(Object.keys(this.gameState.placedItems));
        const placedItemsCount = placedUniqueItems.size;
        const totalItemsCount = this.gameState.items.length;
        
        // Count correctly placed items
        const correctItems = Object.values(this.gameState.placedItems).filter(item => item.isCorrect).length;
        
        console.log(`Checking game completion: ${placedItemsCount}/${totalItemsCount} unique items placed, ${correctItems} correct`);
        
        // Update progress based on correct placements
        const progressPercentage = Math.floor((correctItems / totalItemsCount) * 100);
        this.updateProgress(progressPercentage);
        
        // Check if all items are placed correctly (or all items are placed if we're not strict)
        const isComplete = this.options.requireAllCorrect 
            ? correctItems >= totalItemsCount 
            : placedItemsCount >= totalItemsCount;
        
        if (isComplete) {
            console.log('All items placed, ending game');
            this.endGame();
        }
    }
    
    updateProgress(progressPercentage) {
        console.log(`Updating progress: ${progressPercentage}%`);
        
        // Get counts for more detailed progress display
        const correctCount = this.gameState.correctPlacements || 0;
        const totalItems = this.gameState.totalItems || 0;
        
        // Update progress bar if it exists
        if (this.progressBar) {
            this.progressBar.style.width = `${progressPercentage}%`;
            this.progressBar.setAttribute('aria-valuenow', progressPercentage);
            
            // Change color based on progress
            if (progressPercentage >= 80) {
                this.progressBar.style.backgroundColor = '#4caf50'; // Green
            } else if (progressPercentage >= 50) {
                this.progressBar.style.backgroundColor = '#ff9800'; // Orange
            } else {
                this.progressBar.style.backgroundColor = '#f44336'; // Red
            }
        }
        
        // Update progress text if it exists
        if (this.progressText) {
            this.progressText.textContent = `${correctCount}/${totalItems} items placed correctly`;
        }
        
        // If no progress elements exist, try to use GameFramework's updateScore method instead
        if (!this.progressBar && !this.progressText && typeof this.updateScore === 'function') {
            this.updateScore(progressPercentage);
        }
    }
    
    endGame() {
        try {
            // Calculate score based on correct placements
            const score = this.calculateScore();
            const correctPlacements = this.gameState.correctPlacements || 0;
            const totalItems = this.gameState.items.length;
            const correctPercent = totalItems > 0 ? (correctPlacements / totalItems) * 100 : 0;
            const timeElapsed = (Date.now() - this.startTime) / 1000; // in seconds
            
            console.log(`Game ended with score: ${score}, correct: ${correctPercent.toFixed(2)}%, time: ${timeElapsed}s`);
            
            // Show game completion message
            this.showGameCompleteMessage(score, correctPercent, timeElapsed);
            
            // Track progress and statistics
            try {
                if (typeof this.trackProgress === 'function') {
                    this.trackProgress({
                        score: score,
                        correctPlacements: correctPlacements,
                        incorrectPlacements: this.gameState.incorrectPlacements || 0,
                        timeElapsed: timeElapsed,
                        completedAt: new Date().toISOString(),
                        gameType: 'sorting',
                        difficulty: this.options.difficulty
                    });
                } else {
                    console.warn('trackProgress method not available in GameFramework');
                }
            } catch (trackError) {
                console.error('Error tracking progress:', trackError);
            }
            
            // Update game state
            this.gameState.isComplete = true;
            this.gameState.endTime = Date.now();
            
            // Call parent complete method if it exists
            if (typeof super.complete === 'function') {
                super.complete();
            }
        } catch (error) {
            console.error('Error ending game:', error);
            this.displayMessage('Error completing the game', 'error');
        }
    }
    
    calculateScore() {
        // Count unique correctly placed items
        const correctPlacements = this.gameState.correctPlacements;
        const totalItems = this.gameState.items.length;
        
        // Calculate base score as percentage of correctly placed items
        const baseScore = Math.floor((correctPlacements / totalItems) * 1000);
        
        // Calculate time bonus if all items are correctly placed
        let timeBonus = 0;
        if (correctPlacements >= totalItems) {
            const timeElapsed = (Date.now() - this.startTime) / 1000; // in seconds
            const maxTimeBonus = 200;
            const optimalTime = totalItems * 2; // 2 seconds per item is considered optimal
            timeBonus = Math.max(0, Math.floor(maxTimeBonus * (1 - (timeElapsed / (optimalTime * 2)))));
        }
        
        const totalScore = baseScore + timeBonus;
        console.log(`Score calculation: base ${baseScore} + time bonus ${timeBonus} = ${totalScore}`);
        
        return totalScore;
    }
    
    showGameCompleteMessage(score, correctPercent, timeElapsed) {
        const messageElement = document.createElement('div');
        messageElement.className = 'game-complete-message';
        
        // Adjust message styles for mobile
        const isMobile = this.gameState.isMobile;
        
        const styles = {
            position: 'absolute',
            top: '50%',
            left: '50%',
            transform: 'translate(-50%, -50%)',
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            color: '#fff',
            padding: isMobile ? '15px' : '20px',
            borderRadius: '10px',
            textAlign: 'center',
            zIndex: '1000',
            boxShadow: '0 0 20px rgba(0, 0, 0, 0.5)',
            maxWidth: isMobile ? '90%' : '80%',
            width: isMobile ? '280px' : 'auto'
        };
        
        Object.assign(messageElement.style, styles);
        
        let message = '';
        if (correctPercent === 100) {
            message = '<h2>Perfect Score!</h2>';
        } else if (correctPercent >= 80) {
            message = '<h2>Great Job!</h2>';
        } else if (correctPercent >= 60) {
            message = '<h2>Good Work!</h2>';
        } else {
            message = '<h2>Game Complete</h2>';
        }
        
        message += `
            <p>Score: ${score}</p>
            <p>Correct: ${correctPercent.toFixed(0)}%</p>
            <p>Time: ${timeElapsed.toFixed(1)} seconds</p>
            <div class="game-complete-buttons" style="margin-top: 15px;">
                <button class="play-again-btn" style="padding: 8px 15px; margin-right: 10px; background-color: #4CAF50; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: ${isMobile ? '14px' : '16px'};">Play Again</button>
                <button class="view-stats-btn" style="padding: 8px 15px; background-color: #2196F3; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: ${isMobile ? '14px' : '16px'};">View Stats</button>
            </div>
        `;
        
        messageElement.innerHTML = message;
        this.gameContainer.appendChild(messageElement);
        
        // Add event listeners to buttons
        messageElement.querySelector('.play-again-btn').addEventListener('click', () => {
            this.gameContainer.removeChild(messageElement);
            this.resetGame();
            this.startGame();
        });
        
        messageElement.querySelector('.view-stats-btn').addEventListener('click', () => {
            // Redirect to stats page or show stats modal
            this.showStatistics();
        });
    }
    
    getItemsByDifficulty(items, difficulty) {
        if (!items || !Array.isArray(items) || items.length === 0) {
            console.warn('No items provided for difficulty filtering');
            return [];
        }
        
        if (!difficulty || difficulty === 'all') {
            return items;
        }
        
        const difficultyMap = {
            easy: 1,
            medium: 2,
            hard: 3
        };
        
        const difficultyLevel = difficultyMap[difficulty] || 2; // Default to medium
        
        // Filter items by difficulty if they have a difficulty property
        const filteredItems = items.filter(item => {
            if (item.difficulty) {
                return item.difficulty === difficultyLevel;
            }
            
            // Include items without difficulty specified for backward compatibility
            return true;
        });
        
        // If we didn't find any items for this difficulty, return all items
        if (filteredItems.length === 0) {
            console.warn(`No items found for difficulty ${difficulty}, using all items`);
            return items;
        }
        
        return filteredItems;
    }
    
    // Helper method to play sounds
    playSound(soundUrl) {
        if (!soundUrl) return;
        
        try {
            const audio = new Audio(soundUrl);
            audio.play().catch(error => {
                console.warn('Could not play sound:', error);
            });
        } catch (error) {
            console.warn('Error playing sound:', error);
        }
    }
    
    showStatistics() {
        console.log('Showing statistics');
        
        // If we're in WordPress, redirect to the statistics page
        if (typeof sarahLozGame !== 'undefined' && sarahLozGame.statsPageUrl) {
            window.location.href = sarahLozGame.statsPageUrl;
        } else {
            alert('Game statistics will be available in your user profile.');
        }
    }

    shuffleArray(array) {
        if (!Array.isArray(array)) {
            console.error('shuffleArray: Argument is not an array');
            return array;
        }
        
        try {
            const shuffled = [...array];
            for (let i = shuffled.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
            }
            return shuffled;
        } catch (error) {
            console.error('Error in shuffleArray:', error);
            return array;
        }
    }

    // Helper method to display messages
    displayMessage(message, type = 'info') {
        console.log(`Game message (${type}): ${message}`);
        
        // Create message element
        const messageElement = document.createElement('div');
        messageElement.className = `game-message game-message-${type}`;
        messageElement.textContent = message;
        
        // Style message
        const styles = {
            position: 'absolute',
            top: '50%',
            left: '50%',
            transform: 'translate(-50%, -50%)',
            backgroundColor: type === 'error' ? 'rgba(255, 0, 0, 0.8)' : 'rgba(0, 0, 0, 0.8)',
            color: '#fff',
            padding: '15px 20px',
            borderRadius: '5px',
            zIndex: '1000',
            maxWidth: '80%',
            textAlign: 'center'
        };
        
        Object.assign(messageElement.style, styles);
        
        // Add to game container
        this.gameContainer.appendChild(messageElement);
        
        // Remove after delay
        setTimeout(() => {
            if (messageElement.parentNode) {
                messageElement.parentNode.removeChild(messageElement);
            }
        }, 5000);
    }

    // Detect if device has touch capabilities
    isTouchDevice() {
        try {
            // Primary check for touch capability
            const hasTouchPoints = (
                navigator.maxTouchPoints > 0 || 
                navigator.msMaxTouchPoints > 0
            );
            
            const hasTouch = (
                'ontouchstart' in window || 
                window.DocumentTouch && document instanceof window.DocumentTouch
            );
            
            // Secondary check for mobile user agent
            const mobileRegex = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i;
            const isMobileDevice = mobileRegex.test(navigator.userAgent);
            
            // Test for touch events support
            const touchEventsSupported = ('ontouch' in window) || (window.navigator.msPointerEnabled && window.MSGesture);
            
            // Check orientation API (mostly available on mobile devices)
            const hasOrientationAPI = typeof window.orientation !== 'undefined';
            
            // Check for specific mobile properties
            const hasMobileQueries = window.matchMedia && window.matchMedia('(pointer: coarse)').matches;
            
            // Cache result for better performance
            const result = hasTouchPoints || hasTouch || isMobileDevice || touchEventsSupported || hasOrientationAPI || hasMobileQueries;
            
            console.log('Touch device detection:', { 
                result,
                hasTouchPoints,
                hasTouch,
                isMobileDevice,
                touchEventsSupported,
                hasOrientationAPI,
                hasMobileQueries
            });
            
            return result;
        } catch (e) {
            console.warn('Error in touch device detection:', e);
            // Default to checking user agent as fallback
            return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        }
    }

    // Calculate optimal size for items based on container width and number of items
    calculateItemSize(itemCount) {
        const containerWidth = this.gameContainer.clientWidth;
        const padding = this.gameState.isMobile ? 20 : 40; // Account for container padding
        const availableWidth = containerWidth - padding;
        
        // Define size constraints - smaller for mobile
        const minSize = this.gameState.isMobile ? 50 : 70;
        const maxSize = this.gameState.isMobile ? 70 : 90;
        
        // Calculate optimal items per row based on count and screen size
        const baseItemsPerRow = this.gameState.isMobile ? 
            (window.innerWidth < 400 ? 3 : 4) : // Fewer items per row on small mobile screens
            (window.innerWidth < 768 ? 4 : 5);  // Fewer items per row on tablets
        
        const itemsPerRow = Math.min(baseItemsPerRow, itemCount);
        
        // Calculate width considering gap between items
        const gapSize = this.gameState.isMobile ? 6 : 12;
        const totalGapWidth = (itemsPerRow - 1) * gapSize;
        const calculatedSize = Math.floor((availableWidth - totalGapWidth) / itemsPerRow);
        
        // Return the calculated size, limited by min and max constraints
        return Math.min(maxSize, Math.max(minSize, calculatedSize)) + 'px';
    }
    
    // Calculate optimal width for category containers
    calculateCategoryWidth(categoryCount) {
        const containerWidth = this.gameContainer.clientWidth;
        const padding = this.gameState.isMobile ? 20 : 40; // Account for container padding
        const availableWidth = containerWidth - padding;
        
        // Smaller min/max widths for mobile
        const minWidth = this.gameState.isMobile ? 90 : 140;
        const maxWidth = this.gameState.isMobile ? 140 : 220;
        
        // Get optimal categories per row based on device and count
        const categoriesPerRow = this.determineOptimalCategoriesPerRow(categoryCount);
        
        // Calculate width considering gap between categories
        const gapSize = this.gameState.isMobile ? 8 : 15;
        const totalGapWidth = (categoriesPerRow - 1) * gapSize;
        const calculatedWidth = Math.floor((availableWidth - totalGapWidth) / categoriesPerRow);
        
        // Return the calculated width, limited by min and max constraints
        return Math.min(maxWidth, Math.max(minWidth, calculatedWidth)) + 'px';
    }
    
    // Determine optimal number of categories per row based on count and screen size
    determineOptimalCategoriesPerRow(categoryCount) {
        // Get current screen dimensions
        const screenWidth = window.innerWidth;
        const screenHeight = window.innerHeight;
        const isPortrait = screenHeight > screenWidth;
        
        // For portrait orientation on mobile, show fewer categories per row
        if (this.gameState.isMobile && isPortrait) {
            if (categoryCount <= 2) return categoryCount;
            if (screenWidth < 400) return 1; // Single column for very narrow screens
            return 2; // 2 columns for larger mobile portrait
        }
        
        // For landscape/desktop
        if (this.gameState.isMobile) {
            // On mobile landscape
            if (categoryCount <= 2) return categoryCount;
            if (screenWidth < 600) return 2;
            return 3; // Max 3 per row on mobile landscape
        } else {
            // On desktop
            if (categoryCount <= 2) return categoryCount;
            if (categoryCount <= 4) return Math.min(3, categoryCount);
            if (screenWidth < 992) return 3; // Max 3 for smaller desktops/tablets
            return 4; // Max 4 per row on large desktop
        }
    }
    
    // Handle window resize events
    handleResize() {
        // Update item and category sizes if needed
        const items = this.gameContainer.querySelectorAll('.sorting-item');
        const newItemSize = this.calculateItemSize(items.length);
        
        items.forEach(item => {
            item.style.width = newItemSize;
            item.style.height = newItemSize;
        });
        
        // Update category containers
        if (!this.options.isSequence) {
            const categories = this.gameContainer.querySelectorAll('.category-container');
            const newCategoryWidth = this.calculateCategoryWidth(categories.length);
            
            categories.forEach(category => {
                category.style.width = newCategoryWidth;
            });
        }
    }

    // Simple touch handling as a fallback if jQuery UI Touch Punch is not available
    enableSimpleTouchHandling() {
        console.log('Setting up simple touch handling');
        
        const self = this;
        const items = this.gameContainer.querySelectorAll('.sorting-item');
        let activeItem = null;
        let startX, startY;
        
        // Touch events for items
        items.forEach(item => {
            // Start touch
            item.addEventListener('touchstart', function(e) {
                e.preventDefault();
                activeItem = this;
                startX = e.touches[0].clientX;
                startY = e.touches[0].clientY;
                this.style.opacity = '0.6';
                this.style.zIndex = '100';
            });
            
            // Move touch
            item.addEventListener('touchmove', function(e) {
                if (activeItem !== this) return;
                e.preventDefault();
                
                const deltaX = e.touches[0].clientX - startX;
                const deltaY = e.touches[0].clientY - startY;
                
                // Update position
                this.style.transform = `translate(${deltaX}px, ${deltaY}px)`;
            });
            
            // End touch
            item.addEventListener('touchend', function(e) {
                if (activeItem !== this) return;
                e.preventDefault();
                
                this.style.opacity = '1';
                this.style.transform = '';
                
                // Get final touch position
                const finalX = e.changedTouches[0].clientX;
                const finalY = e.changedTouches[0].clientY;
                
                // Check which drop zone is under the touch position
                const dropTargets = self.options.isSequence 
                    ? self.gameContainer.querySelectorAll('.sequence-container') 
                    : self.gameContainer.querySelectorAll('.category-container');
                
                let foundTarget = false;
                
                dropTargets.forEach(target => {
                    const rect = target.getBoundingClientRect();
                    if (finalX >= rect.left && finalX <= rect.right && 
                        finalY >= rect.top && finalY <= rect.bottom) {
                        
                        foundTarget = true;
                        
                        // Handle drop based on game type
                        if (self.options.isSequence) {
                            const itemId = this.dataset.id;
                            const itemPosition = parseInt(this.dataset.position, 10);
                            const zonePosition = parseInt(target.dataset.position, 10);
                            const isCorrect = itemPosition === zonePosition;
                            
                            // Check if this item has already been processed for this position
                            const itemKey = `${itemId}-${zonePosition}`;
                            if (self.gameState.processedItems.has(itemKey)) {
                                return;
                            }
                            
                            if (isCorrect || self.options.allowIncorrectPlacements) {
                                // Create clone
                                const clone = this.cloneNode(true);
                                clone.style.position = 'relative';
                                clone.style.top = '0';
                                clone.style.left = '0';
                                clone.style.transform = '';
                                
                                // Clear existing item in this position if any
                                const existingItems = target.querySelectorAll('.sorting-item');
                                existingItems.forEach(existingItem => {
                                    target.removeChild(existingItem);
                                });
                                
                                // Add clone to target
                                target.appendChild(clone);
                                
                                // Mark as processed
                                self.gameState.processedItems.add(itemKey);
                                
                                // Apply visual feedback
                                if (isCorrect) {
                                    clone.classList.add('correct-placement');
                                    clone.style.border = '2px solid #4caf50';
                                    clone.style.boxShadow = '0 0 8px rgba(76, 175, 80, 0.6)';
                                    
                                    // Remove original item if correct
                                    const originalItem = this;
                                    // Add success animation
                                    originalItem.classList.add('correct-placement-animation');
                                    originalItem.style.transform = 'scale(1.1)';
                                    originalItem.style.transition = 'all 0.3s ease';
                                    originalItem.style.zIndex = '200';
                                    originalItem.style.boxShadow = '0 0 15px rgba(76, 175, 80, 0.8)';
                                    
                                    // Then fade out and remove
                                    setTimeout(() => {
                                        originalItem.style.opacity = '0';
                                        setTimeout(() => {
                                            if (originalItem.parentNode) {
                                                originalItem.parentNode.removeChild(originalItem);
                                            }
                                        }, 300);
                                    }, 300);
                                } else {
                                    clone.classList.add('incorrect-placement');
                                    clone.style.border = '2px solid #f44336';
                                    clone.style.boxShadow = '0 0 8px rgba(244, 67, 54, 0.6)';
                                }
                                
                                // Record placement
                                self.recordPlacement(itemId, zonePosition, isCorrect);
                                self.checkGameCompletion();
                                
                                // Play sound
                                if (isCorrect && self.options.correctSound) {
                                    self.playSound(self.options.correctSound);
                                } else if (!isCorrect && self.options.errorSound) {
                                    self.playSound(self.options.errorSound);
                                }
                            }
                        } else {
                            const itemId = this.dataset.id;
                            const categoryId = target.dataset.category;
                            const itemCategoryId = this.dataset.category;
                            const isCorrect = itemCategoryId === categoryId;
                            
                            // Check if this item has already been processed for this category
                            const itemKey = `${itemId}-${categoryId}`;
                            if (self.gameState.processedItems.has(itemKey)) {
                                return;
                            }
                            
                            if (isCorrect || self.options.allowIncorrectPlacements) {
                                // Find the items container within the category
                                const itemsContainer = target.querySelector('.category-items-container');
                                const container = itemsContainer || target;
                                
                                // Calculate item size based on category width
                                const containerWidth = target.offsetWidth;
                                const itemSize = Math.max(60, Math.min(120, containerWidth * 0.8));
                                
                                // Create clone
                                const clone = this.cloneNode(true);
                                clone.style.position = 'relative';
                                clone.style.top = '0';
                                clone.style.left = '0';
                                clone.style.transform = '';
                                clone.style.margin = '5px';
                                clone.style.width = `${itemSize}px`;
                                clone.style.height = `${itemSize}px`;
                                clone.style.flexShrink = '0';
                                
                                // Add clone to container
                                container.appendChild(clone);
                                
                                // Mark as processed
                                self.gameState.processedItems.add(itemKey);
                                
                                // Apply visual feedback
                                if (isCorrect) {
                                    clone.classList.add('correct-placement');
                                    clone.style.border = '2px solid #4caf50';
                                    clone.style.boxShadow = '0 0 8px rgba(76, 175, 80, 0.6)';
                                    
                                    // Remove original item if correct
                                    const originalItem = this;
                                    // Add success animation
                                    originalItem.classList.add('correct-placement-animation');
                                    originalItem.style.transform = 'scale(1.1)';
                                    originalItem.style.transition = 'all 0.3s ease';
                                    originalItem.style.zIndex = '200';
                                    originalItem.style.boxShadow = '0 0 15px rgba(76, 175, 80, 0.8)';
                                    
                                    // Then fade out and remove
                                    setTimeout(() => {
                                        originalItem.style.opacity = '0';
                                        setTimeout(() => {
                                            if (originalItem.parentNode) {
                                                originalItem.parentNode.removeChild(originalItem);
                                            }
                                        }, 300);
                                    }, 300);
                                } else {
                                    clone.classList.add('incorrect-placement');
                                    clone.style.border = '2px solid #f44336';
                                    clone.style.boxShadow = '0 0 8px rgba(244, 67, 54, 0.6)';
                                }
                                
                                // Record placement
                                self.recordPlacement(itemId, categoryId, isCorrect);
                                self.checkGameCompletion();
                                
                                // Play sound
                                if (isCorrect && self.options.correctSound) {
                                    self.playSound(self.options.correctSound);
                                } else if (!isCorrect && self.options.errorSound) {
                                    self.playSound(self.options.errorSound);
                                }
                            }
                        }
                    }
                });
                
                if (!foundTarget) {
                    // Reset item position if not dropped on a valid target
                    this.style.transform = '';
                    this.style.position = '';
                    this.style.zIndex = '';
                }
                
                activeItem = null;
            });
        });
    }

    // Add CSS styles for animations
    addAnimationStyles() {
        // Check if the styles are already added
        if (document.getElementById('sorting-game-animations')) {
            return;
        }
        
        // Create style element
        const styleEl = document.createElement('style');
        styleEl.id = 'sorting-game-animations';
        
        // Define animations
        const css = `
            @keyframes correctPlacementPulse {
                0% { transform: scale(1.1); box-shadow: 0 0 15px rgba(76, 175, 80, 0.8); }
                50% { transform: scale(1.2); box-shadow: 0 0 20px rgba(76, 175, 80, 1); }
                100% { transform: scale(1); opacity: 0; }
            }
            
            .correct-placement-animation {
                animation: correctPlacementPulse 0.6s ease;
            }
            
            .sorting-item.ui-draggable-disabled {
                cursor: default !important;
            }
        `;
        
        // Add styles to document
        styleEl.textContent = css;
        document.head.appendChild(styleEl);
    }
} 