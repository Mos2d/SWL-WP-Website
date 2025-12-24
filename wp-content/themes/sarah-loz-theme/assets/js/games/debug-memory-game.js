/**
 * Memory Game Debugger
 * Helps diagnose issues with memory game
 */

document.addEventListener('DOMContentLoaded', function() {
    // Hook into any memory game initialization
    if (typeof MemoryGame !== 'undefined') {
        const originalMemoryGameInit = MemoryGame.prototype.init;
        
        // Override init method to add debugging
        MemoryGame.prototype.init = function() {
            console.log('MemoryGame init called');
            console.log('Container ID:', this.container.id);
            console.log('Game options:', this.options);
            
            // Call original init
            const result = originalMemoryGameInit.apply(this, arguments);
            
            // Add debugging after init
            console.log('Init complete');
            console.log('Game container:', this.gameContainer);
            console.log('Game content:', this.gameContent);
            
            return result;
        };
        
        const originalCreateCards = MemoryGame.prototype.createCards;
        
        // Override createCards method to add debugging
        MemoryGame.prototype.createCards = function() {
            console.log('createCards called');
            console.log('Card images:', this.options.cardImages);
            
            if (!this.options.cardImages || this.options.cardImages.length === 0) {
                console.error('No card images provided for memory game!');
                
                // Create some default cards for testing
                this.options.cardImages = [
                    'https://via.placeholder.com/150/FF5733/FFFFFF?text=1',
                    'https://via.placeholder.com/150/33FF57/000000?text=2',
                    'https://via.placeholder.com/150/5733FF/FFFFFF?text=3',
                    'https://via.placeholder.com/150/FFFF33/000000?text=4'
                ];
                
                console.log('Added default card images for testing');
            }
            
            // Call original createCards
            const result = originalCreateCards.apply(this, arguments);
            
            // Add debugging after cards created
            console.log('Cards created:', this.gameState.cards.length);
            console.log('Grid size:', this.getGridSizeByDifficulty());
            console.log('Total pairs:', this.gameState.totalPairs);
            
            return result;
        };
        
        console.log('Memory game debugging enabled');
    } else {
        console.error('MemoryGame class not found');
    }
}); 