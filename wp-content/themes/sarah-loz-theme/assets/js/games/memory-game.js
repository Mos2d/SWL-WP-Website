/**
 * Memory Game
 * Match pairs of cards with the same images
 */

class MemoryGame extends GameFramework {
    constructor(containerId, options = {}) {
        // Set default options for memory game
        const memoryGameOptions = Object.assign({
            gridSize: 4, // 4x4 grid (16 cards)
            cardBackImage: '', // URL to card back image
            cardImages: [], // Array of image URLs for card faces
            matchSound: '', // URL to sound played on match
            errorSound: '', // URL to sound played on error
            cardFlipDuration: 500, // Duration of flip animation in milliseconds
            difficulty: 'medium',
            mobileResponsive: true, // Enable mobile responsiveness
            adaptiveGridSize: true // Adjust grid size based on screen size
        }, options);

        super(containerId, memoryGameOptions);

        // Game-specific state
        this.gameState = {
            cards: [],
            flippedCards: [],
            matchedPairs: 0,
            totalPairs: 0,
            canFlip: false,
            moves: 0,
            currentGridSize: this.getGridSizeByDifficulty()
        };
        
        // Debugging to identify when gameState is initialized
        console.log('MemoryGame constructor: gameState initialized', this.gameState);
    }

    onGameStart() {
        console.log('Memory Game: onGameStart called');
        
        // Clear previous game
        this.clearElements();
        this.gameContent.innerHTML = '';
        
        console.log('Memory Game: Container dimensions:', {
            width: this.gameContent.clientWidth,
            height: this.gameContent.clientHeight,
            containerClass: this.gameContainer.className
        });
        
        // Ensure gameState is properly initialized
        if (!this.gameState) {
            console.warn('gameState was undefined, reinitializing');
            this.gameState = {
                cards: [],
                flippedCards: [],
                matchedPairs: 0,
                totalPairs: 0,
                canFlip: false,
                moves: 0,
                currentGridSize: this.getGridSizeByDifficulty()
            };
        }
        
        // Reset game-specific state
        this.gameState.cards = [];
        this.gameState.flippedCards = [];
        this.gameState.matchedPairs = 0;
        this.gameState.moves = 0;
        
        // Create cards based on difficulty
        this.createCards();
        
        // Create score popup element (hidden initially)
        this.createScorePopup();
        
        // Allow card flipping after a short delay
        setTimeout(() => {
            if (this.gameState) {
                this.gameState.canFlip = true;
            }
        }, 500);
    }

    createScorePopup() {
        // Create a popup element for score notifications
        this.scorePopup = this.createElement('div', {
            classes: 'memory-game-score-popup',
            parent: this.gameContent,
            styles: {
                position: 'absolute',
                top: '50%',
                left: '50%',
                transform: 'translate(-50%, -50%) scale(0)',
                backgroundColor: 'rgba(76, 175, 80, 0.9)',
                color: 'white',
                padding: '20px 30px',
                borderRadius: '50%',
                fontSize: '24px',
                fontWeight: 'bold',
                zIndex: '150',
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                width: '120px',
                height: '120px',
                boxShadow: '0 4px 8px rgba(0,0,0,0.3)',
                transition: 'transform 0.3s ease-in-out',
                opacity: '0',
                pointerEvents: 'none'
            }
        });
    }

    showScorePopup(score) {
        if (!this.scorePopup) return;
        
        // Update content with current score and a star emoji
        this.scorePopup.innerHTML = `<div>⭐<br>${score}%</div>`;
        
        // Show and animate the popup
        this.scorePopup.style.opacity = '1';
        this.scorePopup.style.transform = 'translate(-50%, -50%) scale(1)';
        
        // Add a confetti effect (simple CSS animation)
        const confetti = this.createElement('div', {
            classes: 'memory-game-confetti',
            parent: this.gameContent,
            styles: {
                position: 'absolute',
                top: '0',
                left: '0',
                width: '100%',
                height: '100%',
                pointerEvents: 'none',
                zIndex: '140'
            }
        });
        
        // Add confetti particles
        for (let i = 0; i < 30; i++) {
            const particle = document.createElement('div');
            particle.style.position = 'absolute';
            particle.style.width = '10px';
            particle.style.height = '10px';
            particle.style.backgroundColor = this.getRandomColor();
            particle.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
            particle.style.top = `${Math.random() * 100}%`;
            particle.style.left = `${Math.random() * 100}%`;
            particle.style.opacity = '1';
            particle.style.animation = `fall ${1 + Math.random() * 2}s linear forwards`;
            confetti.appendChild(particle);
        }
        
        // Add animation style if not already added
        if (!document.getElementById('memory-game-animations')) {
            const style = document.createElement('style');
            style.id = 'memory-game-animations';
            style.textContent = `
                @keyframes fall {
                    0% { transform: translateY(-10px) rotate(0deg); opacity: 1; }
                    100% { transform: translateY(100vh) rotate(360deg); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }
        
        // Hide popup and remove confetti after animation
        setTimeout(() => {
            this.scorePopup.style.opacity = '0';
            this.scorePopup.style.transform = 'translate(-50%, -50%) scale(0)';
            
            setTimeout(() => {
                if (confetti && confetti.parentNode) {
                    confetti.parentNode.removeChild(confetti);
                }
            }, 2000);
        }, 1500);
    }
    
    getRandomColor() {
        const colors = ['#FF5252', '#FFEB3B', '#2196F3', '#4CAF50', '#9C27B0', '#FF9800'];
        return colors[Math.floor(Math.random() * colors.length)];
    }

    createCards() {
        const gridSize = this.gameState.currentGridSize;
        const totalCards = gridSize * gridSize;
        // Make sure totalPairs is always an even number
        const totalPairs = Math.floor(totalCards / 2);
        this.gameState.totalPairs = totalPairs;
        
        console.log(`Memory Game: Creating cards with grid size ${gridSize}, total cards ${totalCards}, total pairs ${totalPairs}`);
        console.log('Memory Game: gameContent element:', this.gameContent);
        console.log('Memory Game: gameContainer element:', this.gameContainer);
        
        // Check if we have any card images
        if (!this.options.cardImages || this.options.cardImages.length === 0) {
            console.error('No card images provided for memory game!');
            // Create default placeholder images
            this.options.cardImages = [];
            for (let i = 0; i < totalPairs; i++) {
                const color = Math.floor(Math.random()*16777215).toString(16).padStart(6, '0');
                this.options.cardImages.push(`https://via.placeholder.com/150/${color}/FFFFFF?text=${i+1}`);
            }
        }
        
        // Create array of pairs (indices to images)
        let cardValues = [];
        const imageCount = this.options.cardImages.length;
        
        for (let i = 0; i < totalPairs; i++) {
            const imageIndex = i % imageCount; // Use modulo to handle cases with fewer images than pairs
            cardValues.push(imageIndex, imageIndex); // Add each image index twice (for pairs)
        }
        
        // Shuffle the cards
        cardValues = this.shuffleArray(cardValues);
        
        // Create grid container with CSS Grid for better responsiveness
        const gridContainer = this.createElement('div', {
            classes: 'memory-game-grid',
            styles: {
                display: 'grid',
                gridTemplateColumns: `repeat(${gridSize}, 1fr)`,
                gridTemplateRows: `repeat(${gridSize}, 1fr)`,
                gap: '10px',
                width: '100%',
                height: '100%',
                padding: '10px', // Reduced padding for better space usage
                boxSizing: 'border-box' // Ensure padding doesn't add to element size
            }
        });
        
        console.log('Memory Game: Created grid container:', gridContainer);
        this.gameContent.appendChild(gridContainer);
        console.log('Memory Game: Appended grid to gameContent. Content children:', this.gameContent.children);
        
        // Calculate minimum card dimension for consistent aspect ratio
        // This will be updated in onLayoutUpdate after the grid is rendered
        
        // Create cards
        for (let i = 0; i < totalCards; i++) {
            try {
                const cardValue = cardValues[i];
                const card = this.createElement('div', {
                    classes: 'memory-card',
                    parent: gridContainer,
                    attributes: {
                        'data-value': cardValue
                    },
                    styles: {
                        backgroundColor: '#2196F3',
                        borderRadius: '8px',
                        cursor: 'pointer',
                        display: 'flex',
                        justifyContent: 'center',
                        alignItems: 'center',
                        transition: 'transform 0.3s',
                        transformStyle: 'preserve-3d',
                        position: 'relative',
                        perspectiveOrigin: 'center center',
                        perspective: '1000px',
                        aspectRatio: '1/1' // Keep cards square regardless of size
                    }
                });
                
                // Create card front (image)
                const cardFront = this.createElement('div', {
                    classes: 'card-front',
                    parent: card,
                    styles: {
                        position: 'absolute',
                        width: '100%',
                        height: '100%',
                        backfaceVisibility: 'hidden',
                        transform: 'rotateY(180deg)',
                        display: 'flex',
                        justifyContent: 'center',
                        alignItems: 'center',
                        backgroundColor: 'white',
                        borderRadius: '8px'
                    }
                });
                
                // Check if we have a valid image URL for this card
                const cardImageUrl = this.options.cardImages[cardValue];
                const hasValidImage = cardImageUrl && typeof cardImageUrl === 'string' && cardImageUrl.trim() !== '';
                
                // Add image to card front
                if (hasValidImage) {
                    const image = this.createElement('img', {
                        parent: cardFront,
                        attributes: {
                            src: cardImageUrl,
                            alt: 'Card Image'
                        },
                        styles: {
                            maxWidth: '80%',
                            maxHeight: '80%',
                            objectFit: 'contain'
                        }
                    });
                    
                    // Add error handler for image loading failures
                    image.addEventListener('error', () => {
                        // Replace failed image with a number
                        image.style.display = 'none';
                        cardFront.textContent = cardValue + 1;
                    });
                } else {
                    // Fallback to text if no image available
                    cardFront.textContent = cardValue + 1;
                }
                
                // Create card back
                const cardBack = this.createElement('div', {
                    classes: 'card-back',
                    parent: card,
                    styles: {
                        position: 'absolute',
                        width: '100%',
                        height: '100%',
                        backfaceVisibility: 'hidden',
                        transform: 'rotateY(0deg)',
                        display: 'flex',
                        justifyContent: 'center',
                        alignItems: 'center',
                        backgroundColor: '#2196F3',
                        borderRadius: '8px'
                    }
                });
                
                // Check if we have a valid back image
                const backImageUrl = this.options.cardBackImage;
                const hasValidBackImage = backImageUrl && typeof backImageUrl === 'string' && backImageUrl.trim() !== '';
                
                // Add back image if provided
                if (hasValidBackImage) {
                    const backImage = this.createElement('img', {
                        parent: cardBack,
                        attributes: {
                            src: backImageUrl,
                            alt: 'Card Back'
                        },
                        styles: {
                            maxWidth: '60%',
                            maxHeight: '60%',
                            objectFit: 'contain'
                        }
                    });
                    
                    // Add error handler for back image loading failures
                    backImage.addEventListener('error', () => {
                        // Replace failed image with a question mark
                        backImage.style.display = 'none';
                        
                        const icon = this.createElement('span', {
                            parent: cardBack,
                            html: '?',
                            styles: {
                                fontSize: '24px',
                                color: 'white'
                            }
                        });
                    });
                } else {
                    // Fallback to icon if no back image
                    const icon = this.createElement('span', {
                        parent: cardBack,
                        html: '?',
                        styles: {
                            fontSize: '24px',
                            color: 'white'
                        }
                    });
                }
                
                // Store card reference
                this.gameState.cards.push(card);
                
                // Add click event
                card.addEventListener('click', () => this.flipCard(card, cardValue));
            } catch (error) {
                console.error(`Error creating card ${i}:`, error);
            }
        }
    }

    flipCard(card, value) {
        if (!this.gameState.canFlip || this.gameState.flippedCards.includes(card) || 
            card.classList.contains('matched')) {
            return;
        }
        
        // Visual flip
        card.style.transform = 'rotateY(180deg)';
        
        // Add to flipped cards
        this.gameState.flippedCards.push(card);
        
        // Check for match if two cards are flipped
        if (this.gameState.flippedCards.length === 2) {
            this.gameState.moves++;
            this.gameState.canFlip = false;
            
            const [firstCard, secondCard] = this.gameState.flippedCards;
            const firstValue = firstCard.getAttribute('data-value');
            const secondValue = secondCard.getAttribute('data-value');
            
            if (firstValue === secondValue) {
                // Match found
                this.handleMatch();
            } else {
                // No match
                this.handleNoMatch();
            }
        }
    }

    handleMatch() {
        // Add match class to both cards
        this.gameState.flippedCards.forEach(card => {
            card.classList.add('matched');
            card.style.backgroundColor = '#4CAF50';
        });
        
        // Play match sound if available
        if (this.options.matchSound) {
            new Audio(this.options.matchSound).play().catch(e => console.log('Sound play error:', e));
        }
        
        // Increment matched pairs counter
        this.gameState.matchedPairs++;
        
        // Update score - ensure totalPairs is not zero to avoid division by zero
        let score = 0;
        if (this.gameState.totalPairs && this.gameState.totalPairs > 0) {
            score = Math.floor((this.gameState.matchedPairs / this.gameState.totalPairs) * 100);
        } else {
            // Log error and set a default score based on matched pairs
            console.error('totalPairs is zero or undefined:', this.gameState.totalPairs);
            score = this.gameState.matchedPairs * 10; // Fallback scoring
        }
        
        // Debug logging
        console.log(`Score calculation: ${this.gameState.matchedPairs} / ${this.gameState.totalPairs} * 100 = ${score}`);
        
        this.updateScore(score);
        
        // Show score popup with animation
        this.showScorePopup(score);
        
        // Reset flipped cards array
        this.gameState.flippedCards = [];
        
        // Allow flipping again
        this.gameState.canFlip = true;
        
        // Check for game completion
        if (this.gameState.matchedPairs >= this.gameState.totalPairs) {
            console.log("Game complete! Matched pairs:", this.gameState.matchedPairs, "Total pairs:", this.gameState.totalPairs);
            // Use a timeout to ensure the last card flip animation completes
            setTimeout(() => {
                // Call the parent class complete method with the final score
                super.complete(score);
            }, 800);
        }
    }

    handleNoMatch() {
        // Play error sound if available
        if (this.options.errorSound) {
            new Audio(this.options.errorSound).play().catch(e => console.log('Sound play error:', e));
        }
        
        // Flip cards back after a delay
        setTimeout(() => {
            this.gameState.flippedCards.forEach(card => {
                card.style.transform = 'rotateY(0deg)';
            });
            
            // Reset flipped cards array
            this.gameState.flippedCards = [];
            
            // Allow flipping again
            this.gameState.canFlip = true;
        }, this.options.cardFlipDuration);
    }

    getGridSizeByDifficulty() {
        // Return grid size based on difficulty
        switch (this.options.difficulty) {
            case 'easy':
                return 2; // 2x2 grid (4 cards, 2 pairs)
            case 'medium':
                return 4; // 4x4 grid (16 cards, 8 pairs)
            case 'hard':
                return 6; // 6x6 grid (36 cards, 18 pairs)
            default:
                return 4;
        }
    }

    // Override parent's onLayoutUpdate method
    onLayoutUpdate() {
        if (!this.gameState || !this.gameState.cards || this.gameState.cards.length === 0) {
            return;
        }
        
        // Get the container dimensions
        const contentWidth = this.gameContent.clientWidth;
        const contentHeight = this.gameContent.clientHeight;
        
        // Adjust grid size based on device and orientation
        let newGridSize = this.gameState.currentGridSize;
        
        // Temporarily disable adaptive grid size to fix layout issues
        if (false && this.options.adaptiveGridSize && this.state.isMobile) {
            const viewportWidth = window.innerWidth;
            const isPortrait = window.innerHeight > window.innerWidth;
            
            // Determine optimal grid size based on viewport
            if (isPortrait) {
                // In portrait mode, we may need a smaller grid
                if (viewportWidth < 400 && newGridSize > 4) {
                    newGridSize = 4;
                } else if (viewportWidth < 320 && newGridSize > 2) {
                    newGridSize = 2;
                } else if (this.options.difficulty === 'hard' && viewportWidth < 480) {
                    // For hard difficulty (6x6), reduce to 4x4 on narrow portrait screens
                    newGridSize = 4;
                }
            } else {
                // In landscape, we can keep the original grid size in most cases
                if (viewportWidth < 480 && newGridSize > 4) {
                    newGridSize = 4;
                }
            }
            
            // If grid size changed, recreate the cards
            if (newGridSize !== this.gameState.currentGridSize) {
                console.log(`Updating grid size from ${this.gameState.currentGridSize} to ${newGridSize}`);
                this.gameState.currentGridSize = newGridSize;
                
                // Only recreate cards if the game has started and not completed
                if (this.state.started && !this.state.completed) {
                    // Remember the matched pairs
                    const matchedPairs = this.gameState.matchedPairs;
                    // Recreate the cards
                    this.clearElements();
                    this.gameContent.innerHTML = '';
                    this.createCards();
                    this.createScorePopup();
                    // Restore matched pairs
                    this.gameState.matchedPairs = matchedPairs;
                    // Update score
                    const score = Math.floor((this.gameState.matchedPairs / this.gameState.totalPairs) * 100);
                    this.updateScore(score);
                }
                
                // Return early as we've recreated the grid
                return;
            }
        }
        
        // Update grid layout
        const gridContainer = this.gameContent.querySelector('.memory-game-grid');
        if (gridContainer) {
            // Update grid template
            gridContainer.style.gridTemplateColumns = `repeat(${this.gameState.currentGridSize}, 1fr)`;
            gridContainer.style.gridTemplateRows = `repeat(${this.gameState.currentGridSize}, 1fr)`;
            
            // For better mobile UI with touch, adjust padding based on container size
            const minDimension = Math.min(contentWidth, contentHeight);
            if (minDimension < 400) {
                gridContainer.style.gap = '5px'; // Smaller gap on small screens
                gridContainer.style.padding = '5px'; // Smaller padding on small screens
            } else {
                gridContainer.style.gap = '10px';
                gridContainer.style.padding = '10px';
            }
        }
    }

    // Helper to shuffle array
    shuffleArray(array) {
        const newArray = [...array];
        for (let i = newArray.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [newArray[i], newArray[j]] = [newArray[j], newArray[i]];
        }
        return newArray;
    }

    onGamePause() {
        // Additional pause logic specific to memory game
    }

    onGameResume() {
        // Additional resume logic specific to memory game
    }

    onGameComplete() {
        // Create a celebratory animation when the game is completed
        const celebration = this.createElement('div', {
            classes: 'memory-game-celebration',
            parent: this.gameContent,
            styles: {
                position: 'absolute',
                top: '0',
                left: '0',
                width: '100%',
                height: '100%',
                pointerEvents: 'none',
                zIndex: '160',
                display: 'flex',
                justifyContent: 'center',
                alignItems: 'center',
                flexDirection: 'column'
            }
        });
        
        // Add a trophy icon
        const trophy = this.createElement('div', {
            parent: celebration,
            html: '🏆',
            styles: {
                fontSize: '80px',
                marginBottom: '20px',
                animation: 'bounce 1s infinite alternate'
            }
        });
        
        // Add congratulations text
        const congrats = this.createElement('div', {
            parent: celebration,
            html: 'أحسنت! لقد أكملت اللعبة',
            styles: {
                fontSize: '24px',
                fontWeight: 'bold',
                color: '#4CAF50',
                textAlign: 'center',
                padding: '10px',
                backgroundColor: 'rgba(255, 255, 255, 0.8)',
                borderRadius: '10px',
                animation: 'fadeIn 1s'
            }
        });
        
        // Add animation styles if not already added
        if (!document.getElementById('memory-game-completion-animations')) {
            const style = document.createElement('style');
            style.id = 'memory-game-completion-animations';
            style.textContent = `
                @keyframes bounce {
                    0% { transform: translateY(0); }
                    100% { transform: translateY(-20px); }
                }
                @keyframes fadeIn {
                    0% { opacity: 0; }
                    100% { opacity: 1; }
                }
            `;
            document.head.appendChild(style);
        }
        
        // Show celebration for a few seconds then fade out
        setTimeout(() => {
            celebration.style.opacity = '0';
            celebration.style.transition = 'opacity 1s';
            setTimeout(() => {
                if (celebration.parentNode) {
                    celebration.parentNode.removeChild(celebration);
                }
            }, 1000);
        }, 5000);
    }
} 